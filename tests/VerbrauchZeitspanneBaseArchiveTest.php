<?php

declare(strict_types=1);

include_once __DIR__ . '/stubs/GlobalStubs.php';
include_once __DIR__ . '/stubs/KernelStubs.php';
include_once __DIR__ . '/stubs/ModuleStubs.php';

use PHPUnit\Framework\TestCase;

class VerbrauchZeitspanneBaseArchiveTest extends TestCase
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

    public function testDate(): void
    {

        $archiveID = IPS_CreateInstance('{43192F0B-135B-4CE7-A0A7-1475603F3060}');
        $instanceID = IPS_CreateInstance('{F74AA9EF-7B80-4AC8-BE0E-D4C24D8F624B}');

        $sourceVariableID = IPS_CreateVariable(1 /*Integer*/);
        IPS_SetIdent($sourceVariableID, 'Usage');
        IPS_SetParent($sourceVariableID, $instanceID);

        IPS_SetProperty($instanceID, 'SourceVariable', $sourceVariableID);
        IPS_SetProperty($instanceID, 'LevelOfDetail', 0);
        IPS_ApplyChanges($instanceID);+-
        VIZ_setTime($instanceID, strtotime('5th November 2005 06:00:00'));
        
        AC_SetLoggingStatus($archiveID, $sourceVariableID, true);
        $aggregationDataDay = [
            [
                'Avg' => 150,
                'Duration' => 60*60*24,
                'Max' => 50,
                'MaxTime' => strtotime('01-11-2005 13:00:00'),
                'Min' => 32,
                'MinTime' => strtotime('01-11-2005 11:00:00'),
                'TimeStamp' => strtotime('01-11-2005 00:00:00')
            ],
            [
                'Avg' => 25,
                'Duration' => 60*60*24,
                'Max' => 50,
                'MaxTime' => strtotime('02-11-2005 13:30:00'),
                'Min' => 10,
                'MinTime' => strtotime('02-11-2005 11:20:00'),
                'TimeStamp' => strtotime('02-11-2005 00:00:00')
            ],
            [
                'Avg' => 25,
                'Duration' => 60*60*24,
                'Max' => 50,
                'MaxTime' => strtotime('03-11-2005 13:00:00'),
                'Min' => 7,
                'MinTime' => strtotime('03-11-2005 11:00:00'),
                'TimeStamp' => strtotime('03-11-2005 00:00:00')
            ],
            [
                'Avg' => 50,
                'Duration' => 60*60*24,
                'Max' => 75,
                'MaxTime' => strtotime('04-11-2005 13:00:00'),
                'Min' => 32,
                'MinTime' => strtotime('04-11-2005 11:00:00'),
                'TimeStamp' => strtotime('04-11-2005 00:00:00')
            ]
        ];

        IPS_EnableDebug($instanceID, 5);
        AC_StubsAddAggregatedValues($archiveID, $sourceVariableID, 1, $aggregationDataDay);
        IPS_SendDebug($instanceID, 'AggregationTest', json_encode(AC_GetAggregatedValues($archiveID, $sourceVariableID, 1, strtotime('02-11-2005 00:00:00'), strtotime('04-11-2005 23:59:59'), 0)), 0);

        SetValue(IPS_GetObjectIDByIdent('StartDate', $instanceID), strtotime('02-11-2005 00:00:00'));
        SetValue(IPS_GetObjectIDByIdent('EndDate', $instanceID), strtotime('04-11-2005 23:59:59'));
        VIZ_Calculate($instanceID);
        echo "Verbrauch: " . GetValue(IPS_GetObjectIDByIdent('Usage', $instanceID));
        $this->assertEquals(100, GetValue(IPS_GetObjectIDByIdent('Usage', $instanceID)));
        $this->assertTrue(true);
    }

    public function testTime(): void
    {

        $archiveID = IPS_CreateInstance('{43192F0B-135B-4CE7-A0A7-1475603F3060}');
        $instanceID = IPS_CreateInstance('{F74AA9EF-7B80-4AC8-BE0E-D4C24D8F624B}');

        $sourceVariableID = IPS_CreateVariable(1 /*Integer*/);
        IPS_SetIdent($sourceVariableID, 'Usage');
        IPS_SetParent($sourceVariableID, $instanceID);

        IPS_SetProperty($instanceID, 'SourceVariable', $sourceVariableID);
        IPS_SetProperty($instanceID, 'LevelOfDetail', 1);
        IPS_ApplyChanges($instanceID);
        VIZ_setTime($instanceID, strtotime('5th November 2005 19:00:00'));

        AC_SetLoggingStatus($archiveID, $sourceVariableID, true);

        $aggregationDataMinuteStart = [
            [
                'Avg' => 2,
                'Duration' => 60,
                'Max' => 1,
                'MaxTime' => 0,
                'Min' => 50,
                'MinTime' => 0,
                'TimeStamp' => strtotime('05-11-2005 06:58:00')
            ],
            [
                'Avg' => 2,
                'Duration' => 60,
                'Max' => 2,
                'MaxTime' => 0,
                'Min' => 32, 
                'MinTime' => 0,
                'TimeStamp' => strtotime('05-11-2005 06:59:00')
            ],
            [
                'Avg' => 1,
                'Duration' => 60,
                'Max' => 3,
                'MaxTime' => 0,
                'Min' => 32,
                'MinTime' => 0,
                'TimeStamp' => strtotime('05-11-2005 07:00:00')
            ]
        ];

        $aggregationDataHour = [
            [
                'Avg' => 2,
                'Duration' => 60 * 60,
                'Max' => 4,
                'MaxTime' => 0,
                'Min' => 32,
                'MinTime' => 0,
                'TimeStamp' => strtotime('05-11-2005 07:00:00')
            ],
            [
                'Avg' => 2,
                'Duration' => 60 * 60,
                'Max' => 5,
                'MaxTime' => 0,
                'Min' => 32,
                'MinTime' => 0,
                'TimeStamp' => strtotime('05-11-2005 08:00:00')
            ],
        ];

        $aggregationDataMinuteEnd = [
            [
                'Avg' => 2,
                'Duration' => 60,
                'Max' => 6,
                'MaxTime' => 0,
                'Min' => 50,
                'MinTime' => 0,
                'TimeStamp' => strtotime('05-11-2005 09:00:00')
            ],
            [
                'Avg' => 2,
                'Duration' => 60,
                'Max' => 7,
                'MaxTime' => 0,
                'Min' => 32,
                'MinTime' => 0,
                'TimeStamp' => strtotime('05-11-2005 09:01:00')
            ],
            [
                'Avg' => 2,
                'Duration' => 60,
                'Max' => 8,
                'MaxTime' => 0,
                'Min' => 32,
                'MinTime' => 0,
                'TimeStamp' => strtotime('05-11-2005 09:02:00')
            ]
        ];

        IPS_EnableDebug($instanceID, 5);
        AC_StubsAddAggregatedValues($archiveID, $sourceVariableID, 6, $aggregationDataMinuteStart);
        AC_StubsAddAggregatedValues($archiveID, $sourceVariableID, 0, $aggregationDataHour);
        AC_StubsAddAggregatedValues($archiveID, $sourceVariableID, 6, $aggregationDataMinuteEnd);
        IPS_SendDebug($instanceID, 'AggregatedArchiveContent', json_encode(AC_GetStubArchive($archiveID)), 0);
        IPS_SendDebug($instanceID, 'AggregationTest', json_encode(
            AC_GetAggregatedValues($archiveID, $sourceVariableID, 1, strtotime('05-11-2005 06:58:00'), strtotime('05-11-2005 09:02:00'), 0)
        ), 0);

        SetValue(IPS_GetObjectIDByIdent('StartDate', $instanceID), strtotime('05-11-2005 06:58:00'));
        SetValue(IPS_GetObjectIDByIdent('EndDate', $instanceID), strtotime('05-11-2005 09:02:00'));
        VIZ_Calculate($instanceID);
        echo "Verbrauch: " . GetValue(IPS_GetObjectIDByIdent('Usage', $instanceID));
        
        $this->assertTrue(true);
    }
    
    public function testDateTime(): void
    {

        $archiveID = IPS_CreateInstance('{43192F0B-135B-4CE7-A0A7-1475603F3060}');
        $instanceID = IPS_CreateInstance('{F74AA9EF-7B80-4AC8-BE0E-D4C24D8F624B}');

        $sourceVariableID = IPS_CreateVariable(1 /*Integer*/);
        IPS_SetIdent($sourceVariableID, 'Usage');
        IPS_SetParent($sourceVariableID, $instanceID);

        IPS_SetProperty($instanceID, 'SourceVariable', $sourceVariableID);
        IPS_SetProperty($instanceID, 'LevelOfDetail', 2);
        IPS_ApplyChanges($instanceID);+-
        VIZ_setTime($instanceID, strtotime('5th November 2005 19:00:00'));
        
        AC_SetLoggingStatus($archiveID, $sourceVariableID, true);

        $aggregationDataFirstMinuteStart = [
            [
                'Avg' => 2,
                'Duration' => 60,
                'Max' => 1,
                'MaxTime' => 0,
                'Min' => 50,
                'MinTime' => 0,
                'TimeStamp' => strtotime('05-11-2005 06:58:00')
            ],
            [
                'Avg' => 2,
                'Duration' => 60,
                'Max' => 2,
                'MaxTime' => 0,
                'Min' => 32, 
                'MinTime' => 0,
                'TimeStamp' => strtotime('05-11-2005 06:59:00')
            ],
            [
                'Avg' => 1,
                'Duration' => 60,
                'Max' => 3,
                'MaxTime' => 0,
                'Min' => 32,
                'MinTime' => 0,
                'TimeStamp' => strtotime('05-11-2005 07:00:00')
            ]
        ];

        $aggregationDataHour = [
            [
                'Avg' => 2,
                'Duration' => 60 * 60,
                'Max' => 4,
                'MaxTime' => 0,
                'Min' => 32,
                'MinTime' => 0,
                'TimeStamp' => strtotime('05-11-2005 07:00:00')
            ],
            [
                'Avg' => 2,
                'Duration' => 60 * 60,
                'Max' => 5,
                'MaxTime' => 0,
                'Min' => 32,
                'MinTime' => 0,
                'TimeStamp' => strtotime('05-11-2005 08:00:00')
            ],
        ];

        $aggregationDataMinuteEnd = [
            [
                'Avg' => 2,
                'Duration' => 60,
                'Max' => 6,
                'MaxTime' => 0,
                'Min' => 50,
                'MinTime' => 0,
                'TimeStamp' => strtotime('05-11-2005 09:00:00')
            ],
            [
                'Avg' => 2,
                'Duration' => 60,
                'Max' => 7,
                'MaxTime' => 0,
                'Min' => 32,
                'MinTime' => 0,
                'TimeStamp' => strtotime('05-11-2005 09:01:00')
            ],
            [
                'Avg' => 2,
                'Duration' => 60,
                'Max' => 8,
                'MaxTime' => 0,
                'Min' => 32,
                'MinTime' => 0,
                'TimeStamp' => strtotime('05-11-2005 09:02:00')
            ]
        ];


        IPS_EnableDebug($instanceID, 5);
        
        SetValue(IPS_GetObjectIDByIdent('StartDate', $instanceID), strtotime('05-11-2005 06:58:00'));
        SetValue(IPS_GetObjectIDByIdent('EndDate', $instanceID), strtotime('08-11-2005 09:02:00'));

        VIZ_Calculate($instanceID);

        $this->assertTrue(true);
    }
}