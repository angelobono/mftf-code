<?php

namespace Bono\MftfCode\Test;

use Bono\MftfCode\Generator\DataGenerator;

/**
 * Class DataGeneratorTest
 * @package Bono\MftfCode\Generator\Test
 */
class DataGeneratorTest extends \PHPUnit\Framework\TestCase
{
    const XML_RESULT = '<?xml version="1.0" encoding="UTF-8"?>
<entities xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:noNamespaceSchemaLocation="urn:magento:mftf:DataGenerator/etc/dataProfileSchema.xsd">
    <entity name="GridData" type="TestModel">
        <data key="id">1</data>
        <data key="title">Text</data>
        <data key="price">1.5</data>
    </entity>
</entities>';

    /**
     * @throws \ReflectionException
     */
    public function testGenerateDataXml()
    {
        $generator = DataGenerator::fromDTO(Fixtures\TestModel::class);
        $this->assertSame(self::XML_RESULT, $generator->render()->getResult());
    }
}
