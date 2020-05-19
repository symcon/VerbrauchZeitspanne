<?php

declare(strict_types=1);

define('ARCHIVE_GUID', '{43192F0B-135B-4CE7-A0A7-1475603F3060}');
define('VERBRAUCHZEITSPANNE_GUID', '{F74AA9EF-7B80-4AC8-BE0E-D4C24D8F624B}');

include_once __DIR__ . '/stubs/GlobalStubs.php';
include_once __DIR__ . '/stubs/KernelStubs.php';
include_once __DIR__ . '/stubs/ModuleStubs.php';

use PHPUnit\Framework\TestCase;

class VerbrauchZeitspanneStatusTest extends TestCase
{
    protected function setUp(): void
    {
        //Reset
        IPS\Kernel::reset();

        //Register our core stubs for testing
        IPS\ModuleLoader::loadLibrary(__DIR__ . '/stubs/CoreStubs/library.json');

        //Register our library we need for testing
        IPS\ModuleLoader::loadLibrary(__DIR__ . '/../library.json');

        //Register required profiles
        if (!IPS\ProfileManager::variableProfileExists('~UnixTimestampDate')) {
            IPS\ProfileManager::createVariableProfile('~UnixTimestampDate', 1);
        }
        if (!IPS\ProfileManager::variableProfileExists('~UnixTimestampTime')) {
            IPS\ProfileManager::createVariableProfile('~UnixTimestampTime', 1);
        }
        if (!IPS\ProfileManager::variableProfileExists('~UnixTimestamp')) {
            IPS\ProfileManager::createVariableProfile('~UnixTimestamp', 1);
        }

        parent::setUp();
    }

    public function testNotSelected(): void
    {
        $archiveID = IPS_CreateInstance(ARCHIVE_GUID);
        $instanceID = IPS_CreateInstance(VERBRAUCHZEITSPANNE_GUID);

        $sourceVariableID = IPS_CreateVariable(1 /*Integer*/);
        AC_SetLoggingStatus($archiveID, $sourceVariableID, true);
        AC_SetAggregationType($archiveID, $sourceVariableID, 1);
        IPS_SetProperty($instanceID, 'SourceVariable', $sourceVariableID);
        IPS_ApplyChanges($instanceID);
        $status = IPS_GetInstance($instanceID)['InstanceStatus'];
        $this->assertEquals(102, $status);
    }

    public function testNotExist(): void
    {
        $archiveID = IPS_CreateInstance(ARCHIVE_GUID);
        $instanceID = IPS_CreateInstance(VERBRAUCHZEITSPANNE_GUID);
        IPS_SetProperty($instanceID, 'SourceVariable', 5);
        IPS_ApplyChanges($instanceID);
        $status = IPS_GetInstance($instanceID)['InstanceStatus'];
        $this->assertEquals(200, $status);
    }

    public function testNotLogged(): void
    {
        $archiveID = IPS_CreateInstance(ARCHIVE_GUID);
        $instanceID = IPS_CreateInstance(VERBRAUCHZEITSPANNE_GUID);

        $sourceVariableID = IPS_CreateVariable(1 /*Integer*/);
        IPS_SetProperty($instanceID, 'SourceVariable', $sourceVariableID);
        IPS_ApplyChanges($instanceID);
        $status = IPS_GetInstance($instanceID)['InstanceStatus'];
        $this->assertEquals(201, $status);
    }

    public function testLoggedStandard(): void
    {
        $archiveID = IPS_CreateInstance(ARCHIVE_GUID);
        $instanceID = IPS_CreateInstance(VERBRAUCHZEITSPANNE_GUID);

        $sourceVariableID = IPS_CreateVariable(1 /*Integer*/);
        AC_SetLoggingStatus($archiveID, $sourceVariableID, true);
        AC_SetAggregationType($archiveID, $sourceVariableID, 0);
        IPS_SetProperty($instanceID, 'SourceVariable', $sourceVariableID);
        IPS_ApplyChanges($instanceID);
        $status = IPS_GetInstance($instanceID)['InstanceStatus'];
        $this->assertEquals(202, $status);
    }
}