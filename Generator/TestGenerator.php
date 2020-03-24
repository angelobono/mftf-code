<?php
/**
 * @Author: Angelo Bono
 * @Date:   2019-06-25 03:36:18
 * @Last Modified by:   Angelo Bono
 * @Last Modified time: 2020-03-24 04:48:01
 */
declare(strict_types=1);

namespace Bono\MftfCode\Generator;

/**
 * Class Test
 * @package Bono\MftfCode\Generator
 */
class TestGenerator extends AbstractGenerator
{
    const ANNOTATIONS_TEMPLATE = '
        <annotations>
            <features value="{{TYPE}} module test case."/>
            <stories value="Add new row/delete row"/>
            <title value="{{TYPE}} module test case."/>
            <description value="{{TYPE}} module test case create."/>
            <severity value="CRITICAL"/>
            <testCaseId value="BONO-MFTF-{{TEST_ID}}"/>
            <group value="{{TYPE_LCFIRST}}"/>
        </annotations>';

    const CLICK_TEMPLATE = '
        <click stepKey="{{STEP_KEY}}" selector="{{Admin{{TYPE}}Section.{{BUTTON_SELECTOR}}}}"/>'; // addBtn

    const WAIT_TEMPLATE = '
        <wait time="{{SECONDS}}" stepKey="{{STEP_KEY}}"/>';

    const FILL_FIELD_TEMPLATE = '
        <fillField userInput="{{{{TYPE}}Data.{{FIELDNAME_LCFIRST}}}}"
            selector="{{Admin{{TYPE}}Section.{{FIELDNAME_LCFIRST}}}}"
            stepKey="{{TYPE_LCFIRST}}{{FIELDNAME_UCFIRST}}"/>';

    const SELECT_OPTION_TEMPLATE = '
        <selectOption userInput="{{GridData.gridStatus}}" selector="{{AdminGridSection.gridStatus}}" stepKey="gridStatus"/>';

    const ADMIN_LOGIN_TEMPLATE = '
        <actionGroup ref="LoginAsAdmin" stepKey="loginAsAdmin1"/>';

    const AM_ON_PAGE_TEMPLATE = '
        <amOnPage url="{{Admin{{TYPE}}Page.url}}" stepKey="{{TYPE_LCFIRST}}ListPageOpen"/>';

    const XML_TEMPLATE = '<?xml version="1.0" encoding="UTF-8"?>
<tests xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
       xsi:noNamespaceSchemaLocation="urn:magento:mftf:Test/etc/testSchema.xsd">
    <test name="{{TYPE}}Test">{{ANNOTATIONS}}{{STEPS}}
    </test>
</tests>';

    /**
     * @var string
     */
    public $result = '';

    /**
     * @var array
     */
    private $attributes = [];

    /**
     * @var string
     */
    private $modelClassName = '';

    /**
     * @var string
     */
    private $isAdminhtml;

    /**
     * @var string|null
     */
    private $testId;

    
    public static function fromDTO(string $className, bool $isAdminhtml = true, ?string $testId = null)
    {
        $attributes = [];
        $ref = new \ReflectionClass($className);
        foreach ($ref->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            if (strpos($method->getName(), 'get') === 0) {
                preg_match('/@return ([a-zA-Z]*)/', $method->getDocComment(), $matches);
                if (empty($matches)) {
                    continue;
                }
                $attributes[lcfirst(str_replace('get', '', $method->getName()))] = '';
            }
        }
        $generator = new self();
        $generator->modelClassName = $className;
        $generator->attributes = $attributes;
        $generator->isAdminhtml = $isAdminhtml;
        $generator->testId = $testId;
        return $generator;
    }

    /**
     * @return string
     */
    private function getType(): string
    {
        $expl = explode('\\', $this->modelClassName);
        return str_replace('Interface', '', $expl[sizeof($expl)-1]);
    }

    /**
     * @return TestGenerator
     */
    public function render(): TestGenerator
    {
        if (empty($this->attributes)) {
            throw new \LogicException('Empty attributes!', 422);
        }
        $this->result = str_replace(
            '{{TYPE}}',
            ($this->isAdminhtml ? 'Admin' : '') . $this->getType(),
            self::XML_TEMPLATE
        );
        $this->result = str_replace(
            '{{ANNOTATIONS}}',
            $this->renderAnnotations(),
            $this->result
        );
        $steps =
            $this->renderAdminLogin() .
            $this->renderAmOnPage() .
            $this->renderWait(2, lcfirst($this->getType()) . 'ListPageLoad') .
            $this->renderClick('addBtn') .
            $this->renderWait(5, 'addRowPageOpen');

        foreach ($this->attributes as $name => $value) {
            $steps .= $this->renderFillField($name);
        }
        $steps .=
            $this->renderClick('save') .
            $this->renderWait(5, lcfirst($this->getType()) . 'DataSave');

        $this->result = str_replace(
            '{{STEPS}}',
            $steps,
            $this->result
        );
        return $this;
    }

    /**
     * @return string
     */
    private function renderAnnotations(): string
    {
        $result = str_replace(
            '{{TYPE}}',
            $this->getType(),
            self::ANNOTATIONS_TEMPLATE
        );
        $result = str_replace(
            '{{TEST_ID}}',
            $this->testId ? $this->testId : uniqid('BONO', true),
            $result
        );
        $result = str_replace(
            '{{TYPE_LCFIRST}}',
            lcfirst($this->getType()),
            $result
        );
        return $result;
    }
    
    /**
     * @return string
     */
    private function renderClick(string $btnSelector, ?string $stepKey = null): string
    {
        $result = str_replace(
            '{{TYPE}}',
            $this->getType(),
            self::CLICK_TEMPLATE
        );
        $result = str_replace(
            '{{BUTTON_SELECTOR}}',
            $btnSelector,
            $result
        );
        $result = str_replace(
            '{{STEP_KEY}}',
            $stepKey ? $stepKey : $btnSelector,
            $result
        );
        return $result;
    }
    
    /**
     * @return string
     */
    private function renderFillField(string $fieldName): string
    {
        $result = str_replace(
            '{{TYPE}}',
            $this->getType(),
            self::FILL_FIELD_TEMPLATE
        );
        $result = str_replace(
            '{{FIELDNAME_LCFIRST}}',
            lcfirst($fieldName),
            $result
        );
        $result = str_replace(
            '{{TYPE_LCFIRST}}',
            lcfirst($this->getType()),
            $result
        );
        $result = str_replace(
            '{{FIELDNAME_UCFIRST}}',
            ucfirst($fieldName),
            $result
        );
        return $result;
    }

    /**
     * @return string
     */
    private function renderWait(int $seconds, string $stepKey): string
    {
        $result = str_replace(
            '{{TYPE}}',
            $this->getType(),
            self::WAIT_TEMPLATE
        );
        $result = str_replace(
            '{{SECONDS}}',
            $seconds,
            $result
        );
        $result = str_replace(
            '{{STEP_KEY}}',
            $stepKey,
            $result
        );
        return $result;
    }

    /**
     * @return string
     */
    private function renderAdminLogin(): string
    {
        return self::ADMIN_LOGIN_TEMPLATE;
    }

    /**
     * @return string
     */
    private function renderAmOnPage(): string
    {
        $result = str_replace(
            '{{TYPE}}',
            $this->getType(),
            self::AM_ON_PAGE_TEMPLATE
        );
        $result = str_replace(
            '{{TYPE_LCFIRST}}',
            lcfirst($this->getType()),
            $result
        );
        return $result;
    }

    /**
     * @return string
     */
    public function getResult(): string
    {
        return $this->result;
    }
}
