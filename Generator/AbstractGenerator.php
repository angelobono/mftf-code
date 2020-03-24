<?php
/**
 * @Author: Angelo Bono
 * @Date:   2019-06-25 03:36:18
 * @Last Modified by:   Angelo Bono
 * @Last Modified time: 2020-03-23 23:00:28
 */
declare(strict_types=1);

namespace Bono\MftfCode\Generator;

/**
 * Class AbstractGenerator
 * @package Bono\MftfCode\Generator
 */
abstract class AbstractGenerator
{
    /**
     * Create a new Generator
     */
    protected function __construct()
    {
        // ..
    }

    /**
     * @param string $filename
     */
    public function toFile(string $filename)
    {
        if (empty($this->getResult())) {
            throw new \LogicException('Empty result!');
        }
        file_put_contents($filename, $this->getResult());
    }

    /**
     * @return string
     */
    abstract public function getResult(): string;

    /**
     * @return void
     */
    abstract public function render();
}
