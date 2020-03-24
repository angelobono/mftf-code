<?php
/**
 * @Author: Angelo Bono
 * @Date:   2019-06-25 03:36:18
 * @Last Modified by:   Angelo Bono
 * @Last Modified time: 2020-03-23 23:24:23
 */
declare(strict_types=1);

namespace Bono\MftfCode\Generator;

/**
 * Class Page
 * @package Bono\MftfCode\Generator
 */
class PageGenerator extends AbstractGenerator
{
    const XML_TEMPLATE = '<?xml version="1.0" encoding="UTF-8"?>
<pages xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
       xsi:noNamespaceSchemaLocation="urn:magento:mftf:Page/etc/PageObject.xsd">
    <page name="{{pageName}}Page" url="{{url}}" area="{{area}}" module="{{moduleName}}"></page>
</pages>';

    /**
     * @var string
     */
    public $result = '';

    /**
     * @var array
     */
    public $attributes = [
        'moduleName' => '',
        'pageName' => '',
        'area' => '',
        'url' => '',
    ];

    /**
     *
     */
    public static function from(
        string $moduleName,
        string $pageName,
        string $url,
        string $area = 'admin'
    ): PageGenerator {
        $instance = new PageGenerator();
        $instance->attributes = [
            'moduleName' => $moduleName,
            'pageName' => $pageName,
            'area' => $area,
            'url' => $url,
        ];
        return $instance;
    }

    /**
     * @return PageGenerator
     */
    public function render(): PageGenerator
    {
        if (empty($this->attributes)) {
            throw new \LogicException('Empty attributes!', 422);
        }
        $this->result = str_replace(
            '{{moduleName}}',
            $this->attributes['moduleName'],
            self::XML_TEMPLATE
        );
        $this->result = str_replace(
            '{{pageName}}',
            $this->attributes['pageName'],
            $this->result
        );
        $this->result = str_replace(
            '{{area}}',
            $this->attributes['area'],
            $this->result
        );
        $this->result = str_replace(
            '{{url}}',
            $this->attributes['url'],
            $this->result
        );
        return $this;
    }

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
