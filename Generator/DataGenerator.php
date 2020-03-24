<?php
/**
 * @Author: Angelo Bono
 * @Date:   2019-06-25 03:36:18
 * @Last Modified by:   Angelo Bono
 * @Last Modified time: 2020-03-23 23:29:12
 */
declare(strict_types=1);

namespace Bono\MftfCode\Generator;

/**
 * Class DataGenerator
 * @package Bono\MftfCode\Generator
 */
class DataGenerator extends AbstractGenerator
{
    const ATTRIBUTE_TEMPLATE = '
        <data key="{{key}}">{{value}}</data>';

    const XML_TEMPLATE = '<?xml version="1.0" encoding="UTF-8"?>
<entities xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:noNamespaceSchemaLocation="urn:magento:mftf:DataGenerator/etc/dataProfileSchema.xsd">
    <entity name="{{type}}Data" type="{{name}}">{{attributes}}
    </entity>
</entities>';

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
     * @param string $className
     * @return DataGenerator
     * @throws \ReflectionException
     */
    public static function fromDTO(string $className): DataGenerator
    {

        $attributes = [];
        $ref = new \ReflectionClass($className);
        foreach ($ref->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            if (strpos($method->getName(), 'get') === 0) {
                preg_match('/@return ([a-zA-Z]*)/', $method->getDocComment(), $matches);
                if (empty($matches)) {
                    continue;
                }
                $value = 'Text';
                switch ($matches[1] ?? '') {
                    case 'float':
                        $value = 1.5;
                        break;
                    case 'bool':
                        $value = true;
                        break;
                    case 'int':
                        $value = 1;
                        break;
                    case 'string':
                        $value = 'Text';
                        break;
                    case 'null':
                        $value = null;
                        break;
                    case 'mixed':
                        $value = 'Mixed';
                        break;
                }
                $attributes[lcfirst(str_replace('get', '', $method->getName()))] = $value;
            }
        }
        $generator = new self();
        $generator->modelClassName = $className;
        $generator->attributes = $attributes;
        return $generator;
    }

    /**
     * @return DataGenerator
     */
    public function render(): DataGenerator
    {
        if (empty($this->attributes)) {
            throw new \LogicException('Empty attributes!', 422);
        }
        $this->result = str_replace(
            '{{attributes}}',
            $this->getAttributes($this->attributes),
            self::XML_TEMPLATE
        );
        $this->result = str_replace(
            '{{type}}',
            $this->getType(),
            $this->result
        );
        $this->result = str_replace(
            '{{name}}',
            lcfirst($this->getType()),
            $this->result
        );
        return $this;
    }

    /**
     * @param array $data
     * @return string
     */
    public function getAttributes(array $data): string
    {
        $xmlPart = '';
        foreach ($data as $key => $value) {
            $part = str_replace('{{key}}', $key, self::ATTRIBUTE_TEMPLATE);
            $xmlPart .= str_replace('{{value}}', $value, $part);
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
