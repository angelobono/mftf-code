<?php

namespace Bono\MftfCode\Test;

use Bono\MftfCode\Generator\TestGenerator;

/**
 * Class TestGeneratorTest
 * @package Bono\MftfCode\Generator\Test
 */
class TestGeneratorTest extends \PHPUnit\Framework\TestCase
{
    const XML_RESULT = '<?xml version="1.0" encoding="UTF-8"?>
<tests xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
       xsi:noNamespaceSchemaLocation="urn:magento:mftf:Test/etc/testSchema.xsd">
    <test name="TestModelTest">
        <annotations>
            <features value="TestModel module test case."/>
            <stories value="Add new row/delete row"/>
            <title value="TestModel module test case."/>
            <description value="TestModel module test case create."/>
            <severity value="CRITICAL"/>
            <testCaseId value="BONO-MFTF-71192"/>
            <group value="testModel"/>
        </annotations>
        <actionGroup ref="LoginAsAdmin" stepKey="loginAsAdmin1"/>
        <amOnPage url="{{AdminTestModelPage.url}}" stepKey="testModelListPageOpen"/>
        <wait time="2" stepKey="testModelListPageLoad"/>
        <click stepKey="addBtn" selector="{{AdminTestModelSection.addBtn}}"/>
        <wait time="5" stepKey="addRowPageOpen"/>
        <fillField userInput="{{TestModelData.id}}"
            selector="{{AdminTestModelSection.id}}"
            stepKey="testModelId"/>
        <fillField userInput="{{TestModelData.title}}"
            selector="{{AdminTestModelSection.title}}"
            stepKey="testModelTitle"/>
        <fillField userInput="{{TestModelData.price}}"
            selector="{{AdminTestModelSection.price}}"
            stepKey="testModelPrice"/>
        <click stepKey="save" selector="{{AdminTestModelSection.save}}"/>
        <wait time="5" stepKey="testModelDataSave"/>
    </test>
</tests>';

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @throws \ReflectionException
     */
    public function testGenerateDataXml()
    {
        $generator = TestGenerator::fromDTO(Fixtures\TestModel::class, true, '71192');
        $this->assertSame(self::XML_RESULT, $generator->render()->getResult());
    }
}
