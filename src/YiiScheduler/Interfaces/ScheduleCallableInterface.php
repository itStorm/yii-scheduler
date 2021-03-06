<?php
namespace YiiScheduler\Interfaces;

/**
 * Interface ScheduleCallable
 * @package YiiScheduler\Interfaces
 */
interface ScheduleCallableInterface
{
    /**
     * @return mixed
     */
    public function getIdentify();

    /**
     * @param $identify
     * @return self|null
     */
    public static function loadByIdentify($identify);
}