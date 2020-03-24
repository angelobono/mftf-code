<?php
/**
 * @Author: Angelo Bono
 * @Date:   2019-06-25 03:36:18
 * @Last Modified by:   Angelo Bono
 * @Last Modified time: 2020-03-19 01:04:12
 */
declare(strict_types=1);

namespace Bono\MftfCode\Generator;

/**
 * Class Metadata
 * @package Bono\MftfCode\Generator
 */
class MetadataGenerator extends AbstractGenerator
{
    const XML_TEMPLATE = '';

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
