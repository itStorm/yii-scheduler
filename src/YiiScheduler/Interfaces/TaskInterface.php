<?php
namespace YiiScheduler\Interfaces;

/**
 * Interface TaskInterface
 * @package YiiScheduler\Interfaces
 */
interface TaskInterface
{
    /**
     * @return int
     */
    public function getTime();

    /**
     * @param int $time
     * @return $this
     */
    public function setTime($time);

    /**
     * @return array
     */
    public function getDays();

    /**
     * @param string|array $days
     * @return $this
     */
    public function setDays($days);

    /**
     * @return array
     */
    public function getWeekDays();

    /**
     * @param string|array $week_days
     * @return $this
     */
    public function setWeekDays($week_days);

    /**
     * @return array
     */
    public function getMonths();

    /**
     * @param string|array $months
     * @return $this
     */
    public function setMonths($months);

    /**
     * @return string
     */
    public function getSubject();

    /**
     * @param string $subject
     * @return $this
     */
    public function setSubject($subject);

    /**
     * @return string
     */
    public function getIdentify();

    /**
     * @param string $identify
     * @return $this
     */
    public function setIdentify($identify);

    /**
     * @return string
     */
    public function getAction();

    /**
     * @param string $action
     * @return $this
     */
    public function setAction($action);

    /**
     * @return array
     */
    public function getScope();

    /**
     * @param array $scope
     * @return $this
     */
    public function setScope(array $scope);

    /**
     * @return string
     */
    public function getScopeHash();

    /**
     * @return int
     */
    public function getLastExecution();

    /**
     * @param int $last_execution
     * @return $this
     */
    public function setLastExecution($last_execution);

    /**
     * @return bool
     */
    public function save();

    /**
     * @param string $objectName
     * @param string $identify
     * @param string $action
     * @param array $scope
     * @param int $time
     * @param string|array $days
     * @param string|array $weekDays
     * @param string|array $months
     * @return int Count of deleted records
     */
    public static function drop(
        $objectName,
        $identify,
        $action = null,
        $scope = null,
        $time = null,
        $days = null,
        $weekDays = null,
        $months = null
    );

    /**
     * @param int $timestamp
     * @param int $limit
     * @return $this[]
     */
    public static function getTasksForRun($timestamp, $limit = null);

    public function run();
}