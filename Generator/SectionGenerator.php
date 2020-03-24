<?php
/**
 * @Author: Angelo Bono
 * @Date:   2019-06-25 03:36:18
 * @Last Modified by:   Angelo Bono
 * @Last Modified time: 2020-03-24 05:05:43
 */
declare(strict_types=1);

namespace Bono\MftfCode\Generator;

/**
 * Class Section
 * @package Bono\MftfCode\Generator
 */
class SectionGenerator extends AbstractGenerator
{
    const ELEMENT_TEMPLATE = '
        <element name="{{name}}" type="{{type}}" selector="{{selector}}"/>';

    const XML_TEMPLATE = '<?xml version="1.0" encoding="UTF-8"?>
<sections xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
          xsi:noNamespaceSchemaLocation="urn:magento:mftf:Page/etc/SectionObject.xsd">
    <section name="{{sectionName}}Section">{{elements}}
        <element name="save" type="button" selector="#save"/>
    </section>
</sections>';

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
    private $sectionType = '';

    /**
     * @param string $className
     * @return SectionGenerator
     * @throws \ReflectionException
     */
    public static function fromDTO(string $className, string $sectionType = 'Grid', bool $isAdminhtml = false): SectionGenerator
    {
        $attributes = [];
        $ref = new \ReflectionClass($className);
        foreach ($ref->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            if (strpos($method->getName(), 'get') === 0) {
                preg_match('/@return ([a-zA-Z]*)/', $method->getDocComment(), $matches);
                if (empty($matches)) {
                    continue;
                }
                $value = '';
                $type = 'text';
                switch ($matches[1] ?? '') {
                    case 'float':
                        $value = 1.5;
                        $type = 'number';
                        break;
                    case 'int':
                        $value = 1;
                        $type = 'number';
                        break;
                    case 'string':
                        $value = 'Text';
                        $type = 'text';
                        break;
                    case 'null':
                        $value = null;
                        $type = 'text';
                        break;
                }
                $name = lcfirst(str_replace(
                    'get',
                    '',
                    $method->getName()
                ));
                $attributes[$name] = [
                    'selector' => '#' . $name,
                    'value' => $value,
                    'type' => $type,
                ];
            }
        }
        $generator = new self();
        $generator->modelClassName = $className;
        $generator->attributes = $attributes;
        $generator->sectionType = $sectionType;
        $generator->isAdminhtml = $isAdminhtml;
        return $generator;
    }

    /**
     * @return SectionGenerator
     */
    public function render(): SectionGenerator
    {
        if (empty($this->attributes)) {
            throw new \LogicException('Empty attributes!', 422);
        }
        $this->result = str_replace(
            '{{elements}}',
            $this->getElements($this->attributes),
            self::XML_TEMPLATE
        );
        $this->result = str_replace(
            '{{sectionName}}',
            ($this->isAdminhtml ? 'Admin' : '') . $this->getType() . $this->sectionType,
            $this->result
        );
        return $this;
    }

    /**
     * @param arrays $data
     * @return string
     */
    public function getElements(array $data): string
    {
        $xmlPart = '';
        foreach ($data as $name => $elementData) {
            $xmlPart .= str_replace(
                '{{type}}',
                $elementData['type'],
                self::ELEMENT_TEMPLATE
            );
            $xmlPart = str_replace(
                '{{name}}',
                $name,
                $xmlPart
            );
            $xmlPart = str_replace(
                '{{selector}}',
                $elementData['selector'],
                $xmlPart
            );
        }
        return $xmlPart;
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
     * @return string
     */
    public function getResult(): string
    {
        return $this->result;
    }
}
