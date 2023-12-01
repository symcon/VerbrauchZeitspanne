<?php

declare(strict_types=1);

if (defined('PHPUNIT_TESTSUITE')) {
    trait TestTime
    {
        private $currentTime = 989884800;

        public function SetTime(int $Time)
        {
            $this->currentTime = $Time;
        }

        private function getTime()
        {
            return $this->currentTime;
        }
    }
} else {
    trait TestTime
    {
        private function getTime()
        {
            return time();
        }
    }
}
