<?php

declare(strict_types=1);

define('ARCHIVE_GUID', '{43192F0B-135B-4CE7-A0A7-1475603F3060}');
define('VERBRAUCHZEITSPANNE_GUID', '{F74AA9EF-7B80-4AC8-BE0E-D4C24D8F624B}');

include_once __DIR__ . '/stubs/GlobalStubs.php';
include_once __DIR__ . '/stubs/KernelStubs.php';
include_once __DIR__ . '/stubs/ModuleStubs.php';

use PHPUnit\Framework\TestCase;

class VerbrauchZeitspanneSameDateTest extends TestCase
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

    // public function testDate(): void
    // {
    //     $archiveID = IPS_CreateInstance(ARCHIVE_GUID);
    //     $instanceID = IPS_CreateInstance(VERBRAUCHZEITSPANNE_GUID);

    //     $sourceVariableID = IPS_CreateVariable(1 /*Integer*/);
    //     AC_SetLoggingStatus($archiveID, $sourceVariableID, true);
    //     AC_SetAggregationType($archiveID, $sourceVariableID, 1);
    //     IPS_ApplyChanges($archiveID);

    //     IPS_SetIdent($sourceVariableID, 'Usage');
    //     IPS_SetParent($sourceVariableID, $instanceID);

    //     IPS_SetProperty($instanceID, 'SourceVariable', $sourceVariableID);
    //     IPS_SetProperty($instanceID, 'LevelOfDetail', 0);
    //     IPS_ApplyChanges($instanceID);
    //     VIZ_SetTime($instanceID, strtotime('5th November 2005 06:00:00'));
    //     $aggregationDataDay = [
    //         [
    //             'Avg'       => 1000,
    //             'Duration'  => 60 * 60 * 24,
    //             'Max'       => 0,
    //             'MaxTime'   => 0,
    //             'Min'       => 0,
    //             'MinTime'   => 0,
    //             'TimeStamp' => strtotime('01-11-2005 00:00:00')
    //         ],
    //         [
    //             'Avg'       => 41,
    //             'Duration'  => 60 * 60 * 24,
    //             'Max'       => 0,
    //             'MaxTime'   => 0,
    //             'Min'       => 0,
    //             'MinTime'   => 0,
    //             'TimeStamp' => strtotime('02-11-2005 00:00:00')
    //         ],
    //         [
    //             'Avg'       => 42,
    //             'Duration'  => 60 * 60 * 24,
    //             'Max'       => 0,
    //             'MaxTime'   => 0,
    //             'Min'       => 0,
    //             'MinTime'   => 0,
    //             'TimeStamp' => strtotime('03-11-2005 00:00:00')
    //         ],
    //         [
    //             'Avg'       => 43,
    //             'Duration'  => 60 * 60 * 24,
    //             'Max'       => 0,
    //             'MaxTime'   => 0,
    //             'Min'       => 0,
    //             'MinTime'   => 0,
    //             'TimeStamp' => strtotime('04-11-2005 00:00:00')
    //         ]
    //     ];

    //     AC_StubsAddAggregatedValues($archiveID, $sourceVariableID, 1, $aggregationDataDay);

    //     SetValue(IPS_GetObjectIDByIdent('StartDate', $instanceID), strtotime('02-11-2005 00:00:00'));
    //     SetValue(IPS_GetObjectIDByIdent('EndDate', $instanceID), strtotime('02-11-2005 00:00:00'));

    //     VIZ_Calculate($instanceID);
    //     $this->assertEquals(41, GetValue(IPS_GetObjectIDByIdent('Usage', $instanceID)));
    // }

    public function testTime(): void
    {
        $archiveID = IPS_CreateInstance(ARCHIVE_GUID);
        $instanceID = IPS_CreateInstance(VERBRAUCHZEITSPANNE_GUID);

        $sourceVariableID = IPS_CreateVariable(1 /*Integer*/);
        AC_SetLoggingStatus($archiveID, $sourceVariableID, true);
        AC_SetAggregationType($archiveID, $sourceVariableID, 1);
        IPS_ApplyChanges($archiveID);
        IPS_SetIdent($sourceVariableID, 'Usage');
        IPS_SetParent($sourceVariableID, $instanceID);

        IPS_SetProperty($instanceID, 'SourceVariable', $sourceVariableID);
        IPS_SetProperty($instanceID, 'LevelOfDetail', 1);
        IPS_ApplyChanges($instanceID);
        VIZ_SetTime($instanceID, strtotime('5th November 2005 19:00:00'));

        AC_SetLoggingStatus($archiveID, $sourceVariableID, true);

        $aggregationDataDay = [
            [
                'Avg'       => 1000,
                'Duration'  => 60 * 60 * 24,
                'Max'       => 0,
                'MaxTime'   => 0,
                'Min'       => 0,
                'MinTime'   => 0,
                'TimeStamp' => strtotime('04-11-2005 00:00:00')
            ],
            [
                'Avg'       => 41,
                'Duration'  => 60 * 60 * 24,
                'Max'       => 0,
                'MaxTime'   => 0,
                'Min'       => 0,
                'MinTime'   => 0,
                'TimeStamp' => strtotime('05-11-2005 00:00:00')
            ],
            [
                'Avg'       => 42,
                'Duration'  => 60 * 60 * 24,
                'Max'       => 0,
                'MaxTime'   => 0,
                'Min'       => 0,
                'MinTime'   => 0,
                'TimeStamp' => strtotime('06-11-2005 00:00:00')
            ]
        ];

        AC_StubsAddAggregatedValues($archiveID, $sourceVariableID, 1, $aggregationDataDay);

        $aggregationDataMinuteStart = [
            [
                'Avg'       => 21,
                'Duration'  => 60,
                'Max'       => 0,
                'MaxTime'   => 0,
                'Min'       => 0,
                'MinTime'   => 0,
                'TimeStamp' => strtotime('05-11-2005 06:58:00')
            ],
            [
                'Avg'       => 22,
                'Duration'  => 60,
                'Max'       => 0,
                'MaxTime'   => 0,
                'Min'       => 0,
                'MinTime'   => 0,
                'TimeStamp' => strtotime('05-11-2005 06:59:00')
            ],
            [
                'Avg'       => 100,
                'Duration'  => 60,
                'Max'       => 0,
                'MaxTime'   => 0,
                'Min'       => 0,
                'MinTime'   => 0,
                'TimeStamp' => strtotime('05-11-2005 07:00:00')
            ]
        ];

        $aggregationDataHour = [
            [
                'Avg'       => 31,
                'Duration'  => 60 * 60,
                'Max'       => 0,
                'MaxTime'   => 0,
                'Min'       => 0,
                'MinTime'   => 0,
                'TimeStamp' => strtotime('05-11-2005 07:00:00')
            ],
            [
                'Avg'       => 32,
                'Duration'  => 60 * 60,
                'Max'       => 0,
                'MaxTime'   => 0,
                'Min'       => 0,
                'MinTime'   => 0,
                'TimeStamp' => strtotime('05-11-2005 08:00:00')
            ],
        ];

        $aggregationDataMinuteEnd = [
            [
                'Avg'       => 23,
                'Duration'  => 60,
                'Max'       => 0,
                'MaxTime'   => 0,
                'Min'       => 0,
                'MinTime'   => 0,
                'TimeStamp' => strtotime('05-11-2005 09:00:00')
            ],
            [
                'Avg'       => 24,
                'Duration'  => 60,
                'Max'       => 0,
                'MaxTime'   => 0,
                'Min'       => 0,
                'MinTime'   => 0,
                'TimeStamp' => strtotime('05-11-2005 09:01:00')
            ],
            [
                'Avg'       => 1000,
                'Duration'  => 60,
                'Max'       => 0,
                'MaxTime'   => 0,
                'Min'       => 0,
                'MinTime'   => 0,
                'TimeStamp' => strtotime('05-11-2005 09:02:00')
            ]
        ];
        AC_StubsAddAggregatedValues($archiveID, $sourceVariableID, 6, $aggregationDataMinuteStart);
        AC_StubsAddAggregatedValues($archiveID, $sourceVariableID, 0, $aggregationDataHour);
        AC_StubsAddAggregatedValues($archiveID, $sourceVariableID, 6, $aggregationDataMinuteEnd);
        SetValue(IPS_GetObjectIDByIdent('StartDate', $instanceID), strtotime('05-11-2005 09:00:00'));
        SetValue(IPS_GetObjectIDByIdent('EndDate', $instanceID), strtotime('05-11-2005 09:00:00'));
        VIZ_Calculate($instanceID);

        $this->assertEquals(41, GetValue(IPS_GetObjectIDByIdent('Usage', $instanceID)));
    }
}