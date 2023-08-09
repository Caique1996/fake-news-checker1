<?php

namespace App\Helpers;


class LoadTimeHelper
{
    private $startTime;

    public function start(): void
    {
        $this->startTime = microtime(true);
    }

    private function stop()
    {
        $endtime = microtime(true);
        return  $endtime - $this->startTime;
    }

}
