<?php

namespace Bono\MftfCode\Test;

use Bono\MftfCode\Generator\PageGenerator;

/**
 * Class PageGeneratorTest
 * @package Bono\MftfCode\Generator\Test
 */
class PageGeneratorTest extends \PHPUnit\Framework\TestCase
{
    const XML_RESULT = '<?xml version="1.0" encoding="UTF-8"?>
<pages xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
       xsi:noNamespaceSchemaLocation="urn:magento:mftf:Page/etc/PageObject.xsd">
    <page name="TestModelAdminGrid" url="/testmodel/index" area="admin" module="Bono_TestModel"></page>
</pages>';

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @throws \ReflectionException
     */
    public function testGenerateDataXml()
    {
        $generator = PageGenerator::from(
            'Bono_TestModel',
            'TestModelAdminGrid',
            '/testmodel/index',
            'admin'
        );
        $this->assertSame(self::XML_RESULT, $generator->render()->getResult());
    }
}
