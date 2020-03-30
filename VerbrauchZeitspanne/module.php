<?php

declare(strict_types=1);

include_once __DIR__ . '/timetest.php';
    class VerbrauchZeitspanne extends IPSModule
    {
        use TestTime;
        public function Create()
        {
            //Never delete this line!
            parent::Create();

            $this->RegisterPropertyInteger('SourceVariable', 0);
            $this->RegisterPropertyInteger('LevelOfDetail', 0);
        }

        public function ApplyChanges()
        {

            //Never delete this line!
            parent::ApplyChanges();

            //Get profile
            $timeProfile = '';
            $lod = $this->ReadPropertyInteger('LevelOfDetail');
            switch ($lod) {
                case 0:
                    $timeProfile = '~UnixTimestampDate';
                    break;
                case 1:
                    $timeProfile = '~UnixTimestampTime';
                    break;
                case 2:
                    $timeProfile = '~UnixTimestamp';
                    break;
            }

            //Create variables
            $this->RegisterVariableInteger('StartDate', 'Start-Datum', $timeProfile, 1);
            $this->EnableAction('StartDate');

            if (GetValue($this->GetIDForIdent('StartDate')) == 0 || $lod == 1) {
                SetValue($this->GetIDForIdent('StartDate'), $this->getTime());
            }

            $this->RegisterVariableInteger('EndDate', 'End-Datum', $timeProfile, 2);
            $this->EnableAction('EndDate');

            if (GetValue($this->GetIDForIdent('EndDate')) == 0 || $lod == 1) {
                SetValue($this->GetIDForIdent('EndDate'), $this->getTime());
            }

            $sourceVariable = $this->ReadPropertyInteger('SourceVariable');
            if ($sourceVariable > 0 && IPS_VariableExists($sourceVariable)) {
                $v = IPS_GetVariable($sourceVariable);

                $sourceProfile = '';
                if (IPS_VariableExists($sourceVariable)) {
                    $sourceProfile = $v['VariableCustomProfile'];
                    if ($sourceProfile == '') {
                        $sourceProfile = $v['VariableProfile'];
                    }
                }

                switch ($v['VariableType']) {
                    case 1: /* Integer */
                        $this->RegisterVariableInteger('Usage', 'Verbrauch', $sourceProfile, 3);
                        break;
                    case 2: /* Float */
                        $this->RegisterVariableFloat('Usage', 'Verbrauch', $sourceProfile, 3);
                        break;
                    default:
                        return;
                }
            }

            //Add references
            foreach ($this->GetReferenceList() as $referenceID) {
                $this->UnregisterReference($referenceID);
            }
            if (IPS_VariableExists($sourceVariable)) {
                $this->RegisterReference($sourceVariable);
            }
        }

        public function RequestAction($Ident, $Value)
        {
            switch ($Ident) {
                case 'StartDate':
                case 'EndDate':
                    //Neuen Wert in die Statusvariable schreiben
                    SetValue($this->GetIDForIdent($Ident), $Value);
                    //Berechnen
                    $this->Calculate();
                    break;
                default:
                    throw new Exception('Invalid Ident');
            }
        }

        /**
         * This function will be available automatically after the module is imported with the module control.
         * Using the custom prefix this function will be callable from PHP and JSON-RPC through:
         *
         * VIZ_Calculate($id);
         *
         */
        public function Calculate()
        {
            $acID = IPS_GetInstanceListByModuleID('{43192F0B-135B-4CE7-A0A7-1475603F3060}')[0];
            $variableID = $this->ReadPropertyInteger('SourceVariable');
            $startDate = GetValue($this->GetIDForIdent('StartDate'));
            $endDate = GetValue($this->GetIDForIdent('EndDate'));
            $lod = $this->ReadPropertyInteger('LevelOfDetail');
            $sum = 0;
            switch ($lod) {
                case 0: // Date
                    $values = AC_GetAggregatedValues($acID, $variableID, 1 /* Day */, $startDate, $endDate + (24 * 3600) - 1, 0);
                    break;
                case 1: // Time
                    //FirstMinutes
                    $firstMinutesStart = strtotime(date('H:i', $startDate) . ':00', $this->getTime());
                    $this->SendDebug('FirstMinutsStart', date('H:i:s', $firstMinutesStart), 0);
                    $firstMinutesEnd = strtotime(date('H', $startDate) . ':00:00 next hour', $this->getTime());
                    $this->SendDebug('FirstMinutsEnd', date('H:i:s', $firstMinutesEnd), 0);
                    $firstMinutes = AC_GetAggregatedValues($acID, $variableID, 6 /* Minutes */, $firstMinutesStart, $firstMinutesEnd, 0);

                    //Hours
                    $hoursStart = strtotime(intval(date('H', $startDate)) + 1 . ':00:00', $this->getTime());
                    $this->SendDebug('StartHours', date('H:i:s', $hoursStart), 0);
                    $hoursEnd = strtotime(intval(date('H', $endDate)) - 1 . ':59:59', $this->getTime());
                    $this->SendDebug('EndHours', date('H:i:s', $hoursEnd), 0);
                    $hours = AC_GetAggregatedValues($acID, $variableID, 0 /* Hour */, $hoursStart, $hoursEnd, 0);

                    //LastMinutes
                    $lastMinutesStart = strtotime(date('H', $endDate) . ':00:00', $this->getTime());
                    $this->SendDebug('LastMinutsStart', date('H:i:s', $lastMinutesStart), 0);
                    $lastMinutesEnd = strtotime(date('H:i', $endDate) . ':00', $this->getTime());
                    $this->SendDebug('LastMinutsEnd', date('H:i:s', $lastMinutesEnd), 0);
                    $lastMinutes = AC_GetAggregatedValues($acID, $variableID, 6 /* Minutes */, $lastMinutesStart, $lastMinutesEnd, 0);

                    $values = array_merge($firstMinutes, $hours, $lastMinutes);
                    break;
                case 2: // Date&Time

                    //FirstMinutes
                    $firstMinutesStart = strtotime(date('H:i', $startDate) . ':00', $this->getTime());
                    $this->SendDebug('FirstMinutsStart', date('H:i:s', $firstMinutesStart), 0);
                    $firstMinutesEnd = strtotime(date('H', $startDate) . ':00:00 next hour', $this->getTime());
                    $this->SendDebug('FirstMinutsEnd', date('H:i:s', $firstMinutesEnd), 0);
                    $firstMinutes = AC_GetAggregatedValues($acID, $variableID, 6 /* Minutes */, $firstMinutesStart, $firstMinutesEnd, 0);

                    //FirstHours
                    $firstHoursStart = strtotime(date('H:00:00', $startDate) . ' next hour', $startDate);
                    $this->SendDebug('FirstHoursStart', date('d.m.Y H:i:s', $firstHoursStart), 0);
                    $firstHoursEnd = strtotime('23:59:59', $startDate);
                    $this->SendDebug('FirstHoursEnd', date('d.m.Y H:i:s', $firstHoursEnd), 0);
                    $firstHours = AC_GetAggregatedValues($acID, $variableID, 0 /* Hour */, $firstHoursStart, $firstHoursEnd, 0);

                    //Days
                    $daysStart = strtotime(date('d-m-Y', $startDate) . ' 00:00:00 tomorrow', $startDate);
                    $this->SendDebug('StartDays', date('d.m.Y H:i:s', $daysStart), 0);
                    $daysEnd = strtotime(date('d-m-Y', $endDate) . ' yesterday 23:59:59', $this->getTime());
                    $this->SendDebug('EndDays', date('d.m.Y H:i:s', $daysEnd), 0);
                    $days = AC_GetAggregatedValues($acID, $variableID, 1 /* Day */, $daysStart, $daysEnd, 0);

                    //LastHours
                    $lastHoursStart = strtotime('00:00:00', $endDate);
                    $this->SendDebug('LastHoursStart', date('d.m.Y H:i:s', $lastHoursStart), 0);
                    $lastHoursEnd = strtotime(date('H:00:00', $endDate), $endDate);
                    $this->SendDebug('LastHoursEnd', date('d.m.Y H:i:s', $lastHoursEnd), 0);
                    $lastHours = AC_GetAggregatedValues($acID, $variableID, 0 /* Hour */, $lastHoursStart, $lastHoursEnd, 0);

                    //LastMinutes
                    $lastMinutesStart = strtotime(date('d.m.Y H', $endDate) . ':00:00', $this->getTime());
                    $this->SendDebug('LastMinutsStart', date('d.m.Y H:i:s', $lastMinutesStart), 0);
                    $lastMinutesEnd = strtotime(date('d.m.Y H:i', $endDate) . ':00', $this->getTime());
                    $this->SendDebug('LastMinutsEnd', date('d.m.Y H:i:s', $lastMinutesEnd), 0);
                    $lastMinutes = AC_GetAggregatedValues($acID, $variableID, 6 /* Minutes */, $lastMinutesStart, $lastMinutesEnd, 0);

                    $values = array_merge($firstMinutes, $firstHours, $days, $lastHours, $lastMinutes);
                    break;
            }

            if ($values === false) {
                $this->SendDebug('Error', 'NoData', 0);
                return;
            }

            foreach ($values as $value) {
                $sum += $value['Avg'];
            }

            SetValue($this->GetIDForIdent('Usage'), $sum);
        }
    }