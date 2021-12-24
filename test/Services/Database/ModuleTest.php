<?php

namespace Services\Database;

use PHPUnit\Framework\TestCase;

class ModuleTest extends TestCase
{
    public function testCorrectInstance()
    {
        $testedModule = new Module();

        $this->assertInstanceOf(Module::class, $testedModule);
    }
}
