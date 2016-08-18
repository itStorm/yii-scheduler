<?php
namespace YiiScheduler;

use YiiScheduler\Exceptions\BadTimeException;
use YiiScheduler\Exceptions\InvalidMethodException;
use YiiScheduler\Exceptions\SaveFailedException;
use YiiScheduler\Interfaces\ScheduleCallableInterface;
use YiiScheduler\Interfaces\TaskInterface;
use YiiScheduler\Models\DateTimeRepresent;
use YiiScheduler\Models\Task;

/**
 * Class SchedulerComponent
 * @package YiiScheduler
 */
class Scheduler extends \CApplicationComponent
{

    /** @var string */
    public static $table = 'schedule';

    /** @var string */
    public $taskClass = Task::class;

    /**
     * @return TaskInterface
     */
    protected function getTask()
    {

        return new $this->taskClass;
    }

    /**
     * Add new trigger in schedule that will be run in selected time at UTC
     * @param ScheduleCallableInterface $object
     * @param string $action
     * @param array $scope
     * @param mixed $time Time in seconds from day start or time in 24-h format like '11:55:34'
     * @param array|string $days [1...31]
     * @param array|string $weekDays [monday, tuesday, wednesday, thursday, friday, saturday, sunday]
     * @param array|string $months [january, february, march, april, may, june, july, august, september, october, november, december]
     * @return bool
     * @throws BadTimeException
     * @throws InvalidMethodException
     * @throws SaveFailedException
     */
    public function addTrigger(
        ScheduleCallableInterface $object,
        $action,
        $scope = [],
        $time = null,
        $days = '*',
        $weekDays = '*',
        $months = '*')
    {
        // If time is empty, set current time
        if (!$time) {
            $time = gmdate('H:i:s', time());
        }
        $timeAsInt = DateTimeRepresent::timeToSeconds($time);
        if (!$timeAsInt) {
            throw new BadTimeException('Time format is not valid');
        }

        // Check method name
        if (!method_exists($object, $action)) {
            throw new InvalidMethodException('Method not exist in class');
        }
        $reflected = new \ReflectionClass($object);
        if (!$reflected->getMethod($action)->isPublic()) {
            throw new InvalidMethodException('Method should be public');
        }

        $task = $this->getTask()
            ->setSubject(get_class($object))
            ->setIdentify($object->getIdentify())
            ->setAction($action)
            ->setScope($scope)
            ->setTime($timeAsInt)
            ->setDays($days)
            ->setWeekDays($weekDays)
            ->setMonths($months);

        if (!$task->save()) {
            throw new SaveFailedException('Save failed');
        }

        return true;
    }

    /**
     * Drop TASK or TASKS that suit for params if some params
     * @param ScheduleCallableInterface $object
     * @param string $action
     * @param array $scope
     * @param string|int $time Time in seconds from day start or time in 24-h format like '11:55:34'
     * @param array|string $days
     * @param array|string $weekDays
     * @param array|string $months
     * @return int Count of deleted records
     */
    public function dropTrigger(ScheduleCallableInterface $object,
                                $action,
                                $scope = null,
                                $time = null,
                                $days = null,
                                $weekDays = null,
                                $months = null)
    {
        $className = $this->taskClass;
        return $className::drop(
            get_class($object),
            $object->getIdentify(),
            $action,
            $scope,
            $time,
            $days,
            $weekDays,
            $months
        );
    }

    /**
     * Run schedule
     * @param int $limit
     * @return int
     */
    public function run($limit = 100)
    {
        $transaction = \Yii::app()->db->beginTransaction();

        try {
            $className = $this->taskClass;
            $tasks = $className::getTasksForRun(time(), $limit);
        } catch (\Exception $e) {
            $transaction->rollback();

            return 0;
        }
        $transaction->commit();
        unset($className, $transaction);

        $count = count($tasks);

        // Exec tasks
        /** @var TaskInterface $task */
        foreach ($tasks as &$task) {
            $task->run();
            unset($task);
        }
        unset($tasks);

        return $count;
    }
}