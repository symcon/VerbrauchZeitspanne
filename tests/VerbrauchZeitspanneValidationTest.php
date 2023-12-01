<?php

declare(strict_types=1);
include_once __DIR__ . '/stubs/Validator.php';
class VerbrauchZeitspanneValidationTest extends TestCaseSymconValidation
{
    public function testValidateVerbrauchZeitspanne(): void
    {
        $this->validateLibrary(__DIR__ . '/..');
    }
    public function testValidateConsumptionWithinTimespanModule(): void
    {
        $this->validateModule(__DIR__ . '/../ConsumptionWithinTimespan');
    }
}