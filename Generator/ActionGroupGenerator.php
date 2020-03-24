<?php
/**
 * @Author: Angelo Bono
 * @Date:   2019-06-25 03:36:18
 * @Last Modified by:   Angelo Bono
 * @Last Modified time: 2020-03-19 01:04:15
 */
declare(strict_types=1);

namespace Bono\MftfCode\Generator;

/**
 * Class ActionGroup
 * @package Bono\MftfCode\Generator
 */
class ActionGroupGenerator extends AbstractGenerator
{
    const XML_TEMPLATE = '<?xml version="1.0" encoding="UTF-8"?>
<entities xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:noNamespaceSchemaLocation="urn:magento:mftf:DataGenerator/etc/dataProfileSchema.xsd">
    <entity name="GridData" type="{{type}}">{{attributes}}
    </entity>
</entities>
';
    
    /**
     * @var string
     */
    public $result = '';

    /**
     * @return string
     */
    public function getTemplate(): string
    {
        return self::XML_TEMPLATE;
    }

    /**
     * @return string
     */
    public function getResult(): string
    {
        return $this->result;
    }
}
