<?php

namespace Services\Database;

use PHPUnit\Framework\TestCase;

class ModuleTest extends TestCase
{
    protected Module $validModule;

    protected function setUp(): void
    {
        $this->validModule = new Module();
    }

    public function testCorrectInstance()
    {
        $testedModule = new Module();

        self::assertInstanceOf(Module::class, $testedModule);
    }

    public function testCorrectDatabase()
    {
        $this->validModule->beginTransaction();
        $databaseName = $this->validModule->executeQuery('SELECT DATABASE()');
        $this->validModule->commitTransaction();

        $expectedRows = [
            [
                'bank',
                'DATABASE()' => 'bank'
            ]
        ];

        self::assertEquals($expectedRows, $databaseName);
    }
}
