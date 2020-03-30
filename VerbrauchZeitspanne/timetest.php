<?php

declare(strict_types=1);

if (defined('PHPUNIT_TESTSUITE')) {
    trait TestTime
    {
        private $currentTime = 989884800;

        private function getTime()
        {
            return $this->currentTime;
        }

        public function SetTime(int $Time)
        {
            $this->currentTime = $Time;
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
