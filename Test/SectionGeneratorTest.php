<?php

namespace Bono\MftfCode\Test;

use Bono\MftfCode\Generator\SectionGenerator;

/**
 * Class SectionGeneratorTest
 * @package Bono\MftfCode\Generator\Test
 */
class SectionGeneratorTest extends \PHPUnit\Framework\TestCase
{
    const XML_RESULT = '<?xml version="1.0" encoding="UTF-8"?>
<sections xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
          xsi:noNamespaceSchemaLocation="urn:magento:mftf:Page/etc/SectionObject.xsd">
    <section name="TestModel">
        <element name="id" type="number" selector="#id"/>
        <element name="title" type="text" selector="#title"/>
        <element name="price" type="number" selector="#price"/>
        <element name="save" type="button" selector="#save"/>
    </section>
</sections>';

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @throws \ReflectionException
     */
    public function testGenerateDataXml()
    {
        $generator = SectionGenerator::fromDTO(Fixtures\TestModel::class);
        $this->assertSame(self::XML_RESULT, $generator->render()->getResult());
    }
}
