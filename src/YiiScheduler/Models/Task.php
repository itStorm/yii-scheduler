<?php
namespace YiiScheduler\Models;

use YiiScheduler\Interfaces\ScheduleCallableInterface;
use YiiScheduler\Interfaces\TaskInterface;
use YiiScheduler\Scheduler;

/**
 * Class Task
 * @package YiiScheduler\Models
 * @property string $guid
 * @property int $time
 * @property int $days
 * @property int $week_days
 * @property int $months
 * @property string $subject
 * @property string $identify
 * @property string $action
 * @property array $scope
 * @property string $scope_hash
 * @property int $last_execution
 */
class Task extends \CActiveRecord implements TaskInterface
{
    /** @var array */
    protected $humanReadableScope = null;
    /** @var array */
    protected $humanReadableDays = null;
    /** @var array */
    protected $humanReadableWeekDays = null;
    /** @var array */
    protected $humanReadableMonths = null;

    /** @inheritdoc */
    public function tableName()
    {
        return Scheduler::$table;
    }

    /**
     * Returns the static model of the class.
     * @param string $className Active record class name.
     * @return Task he static Model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /** @inheritdoc */
    public function rules()
    {
        return [
            ['guid, days, week_days, months, time, subject, identify, action, scope, scope_hash', 'required'],
            ['guid, scope_hash', 'length', 'max' => 32],
            ['guid', 'unique'],
            ['subject', 'unique',
                'criteria' => [
                    'condition' => '    identify = :identify
                                AND action = :action
                                AND scope_hash = :scope_hash
                                AND time = :time
                                AND days = :days
                                AND week_days = :week_days
                                AND months = :months',
                    'params'    => [
                        ':identify'   => $this->identify,
                        ':action'     => $this->action,
                        ':scope_hash' => $this->scope_hash,
                        ':time'       => $this->time,
                        ':days'       => $this->days,
                        ':week_days'  => $this->week_days,
                        ':months'     => $this->months,

                    ],
                ],
                'message'  => 'Keys(subject, identify, action, scope_hash, time, days, week_days, months) should be unique',

            ],
            ['subject, identify, action', 'length', 'max' => 64],
            ['time', 'numerical', 'min' => 0, 'max' => DateTimeRepresent::SECONDS_IN_DAY, 'integerOnly' => true],
            ['days', 'validateDays'],
            ['week_days', 'validateWeekDays',],
            ['months', 'validateMonths'],
            ['scope', 'safe'],
        ];
    }

    /** @inheritdoc */
    public function primaryKey()
    {
        return 'guid';
    }

    /**
     * @param string $attribute
     */
    public function validateDays($attribute)
    {
        $days = $this->getDays();
        if (!$days) {
            $this->addError($attribute, 'Cannot be blank');

            return;
        }

        foreach ($days as $day) {
            if (!in_array($day, DateTimeRepresent::getDays())) {
                $this->addError($attribute, 'Not valid');

                return;
            }
        }
    }

    /**
     * @param string $attribute
     */
    public function validateWeekDays($attribute)
    {
        $weekDays = $this->getWeekDays();
        if (!$weekDays) {
            $this->addError($attribute, 'Cannot be blank');

            return;
        }

        foreach ($weekDays as $weekDay) {
            if (!in_array($weekDay, DateTimeRepresent::getWeekDaysNames())) {
                $this->addError($attribute, 'Not valid');

                return;
            }
        }
    }

    /**
     * @param string $attribute
     */
    public function validateMonths($attribute)
    {
        $months = $this->getMonths();
        if (!$months) {
            $this->addError($attribute, 'Cannot be blank');

            return;
        }

        foreach ($months as $month) {
            if (!in_array($month, DateTimeRepresent::getMonthsNames())) {
                $this->addError($attribute, 'Not valid');

                return;
            }
        }
    }

    /**
     * Generate guid for PK
     * @return string
     */
    protected function generateGUID()
    {
        return md5(microtime() . rand(0, 9999));
    }

    /** @inheritdoc */
    public function save($runValidation = true, $attributes = null)
    {
        $this->days = DateTimeRepresent::daysToBinaryValue($this->getDays());
        $this->week_days = DateTimeRepresent::weekdaysBinaryValue($this->getWeekDays());
        $this->months = DateTimeRepresent::monthsBinaryValue($this->getMonths());

        $attempts = 0;
        do {
            $attempts++;
            // Try save three times if guid not unique
            if ($this->isNewRecord) {
                $this->guid = $this->generateGUID();
            }
            $result = parent::save($runValidation, $attributes);
        } while ($this->getError('guid') && $attempts < 3);

        return $result;
    }

    /**
     * @return string
     */
    public function getGiud()
    {
        return $this->guid;
    }

    /** @inheritdoc */
    public function getTime()
    {
        return $this->time;
    }

    /** @inheritdoc */
    public function setTime($time)
    {
        $this->time = $time;

        return $this;
    }

    /** @inheritdoc */
    public function getDays()
    {
        if (!isset($this->humanReadableDays)) {
            $this->humanReadableDays = $this->days ? explode(',', $this->days) : [];
        }

        return $this->humanReadableDays;
    }

    /** @inheritdoc */
    public function setDays($days)
    {
        if (is_array($days)) {
            $this->humanReadableDays = $days;
        } elseif ($days === '*') {
            $this->humanReadableDays = DateTimeRepresent::getDays();
        } else {
            $this->humanReadableDays = [$days];
        }

        return $this;
    }

    /** @inheritdoc */
    public function getWeekDays()
    {
        if (!isset($this->humanReadableWeekDays)) {
            $this->humanReadableWeekDays = $this->week_days ? explode(',', $this->week_days) : [];
        }

        return $this->humanReadableWeekDays;
    }

    /** @inheritdoc */
    public function setWeekDays($weekDays)
    {
        if (is_array($weekDays)) {
            $this->humanReadableWeekDays = $weekDays;
        } elseif ($weekDays === '*') {
            $this->humanReadableWeekDays = DateTimeRepresent::getWeekDaysNames();
        } else {
            $this->humanReadableWeekDays = [$weekDays];
        }

        return $this;
    }

    /** @inheritdoc */
    public function getMonths()
    {
        if (!isset($this->humanReadableMonths)) {
            $this->humanReadableMonths = $this->months ? explode(',', $this->months) : [];
        }

        return $this->humanReadableMonths;
    }

    /** @inheritdoc */
    public function setMonths($months)
    {
        if (is_array($months)) {
            $this->humanReadableMonths = $months;
        } elseif ($months === '*') {
            $this->humanReadableMonths = DateTimeRepresent::getMonthsNames();
        } else {
            $this->humanReadableMonths = [$months];
        }

        return $this;
    }

    /** @inheritdoc */
    public function getSubject()
    {
        return $this->subject;
    }

    /** @inheritdoc */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /** @inheritdoc */
    public function getIdentify()
    {
        return $this->identify;
    }

    /** @inheritdoc */
    public function setIdentify($identify)
    {
        $this->identify = $identify;

        return $this;
    }

    /** @inheritdoc */
    public function getAction()
    {
        return $this->action;
    }

    /** @inheritdoc */
    public function setAction($action)
    {
        $this->action = $action;

        return $this;
    }

    /** @inheritdoc */
    public function getScope()
    {
        if (!isset($this->humanReadableScope)) {
            $this->humanReadableScope = $this->scope ?
                unserialize($this->scope) : [];
        }

        return $this->humanReadableScope;
    }

    /** @inheritdoc */
    public function setScope(array $scope)
    {
        $this->humanReadableScope = $scope;
        $this->scope = serialize($this->getScope());
        $this->scope_hash = md5($this->scope);

        return $this;
    }

    /** @inheritdoc */
    public function getScopeHash()
    {
        return $this->scope_hash;
    }

    /** @inheritdoc */
    public function getLastExecution()
    {
        return $this->last_execution;
    }

    /** @inheritdoc */
    public function setLastExecution($last_execution)
    {
        $this->last_execution = $last_execution;

        return $this;
    }

    /**
     * Exec this task
     */
    public function run()
    {
        $className = '\\' . $this->getSubject();
        /** @var ScheduleCallableInterface $object */
        $object = $className::loadByIdentify($this->getIdentify());
        if (!$object) {
            $this->delete();

            return;
        }

        $method = new \ReflectionMethod($object, $this->getAction());
        $method->invokeArgs($object, $this->getScope());
    }

    /** @inheritdoc */
    public static function drop(
        $objectName,
        $identify,
        $action = null,
        $scope = null,
        $time = null,
        $days = null,
        $weekDays = null,
        $months = null)
    {
        $columnConditions = [
            'subject'  => $objectName,
            'identify' => $identify,
        ];

        if ($action) {
            $columnConditions['action'] = $action;
        }
        if ($scope) {
            $columnConditions['scope_hash'] = md5(serialize($scope));
        }
        if ($time) {
            $columnConditions['time'] = DateTimeRepresent::timeToSeconds($time);
        }
        if ($days) {
            $columnConditions['days'] = DateTimeRepresent::daysToBinaryValue($days);
        }
        if ($weekDays) {
            $columnConditions['weekDays'] = DateTimeRepresent::weekdaysBinaryValue($weekDays);
        }
        if ($months) {
            $columnConditions['months'] = DateTimeRepresent::monthsBinaryValue($months);
        }

        $criteria = new \CDbCriteria();
        $criteria->addColumnCondition($columnConditions);

        return self::model()->deleteAll($criteria);
    }

    /** @inheritdoc */
    public static function getTasksForRun($timestamp, $limit = null)
    {
        $table = Scheduler::$table;
        $dayStartTimestamp = strtotime(gmdate('Y-m-d 00:00:00 eP', $timestamp));
        $currentTimesInSecond = DateTimeRepresent::timeToSeconds(gmdate('H:i:s', $timestamp));

        $day = gmdate('j', $timestamp);
        $weekDay = DateTimeRepresent::getCompareListWeekDaysVsIndexes()[gmdate('N', $timestamp)];
        $month = DateTimeRepresent::getCompareListMonthsVsIndexes()[gmdate('n', $timestamp)];

        $dayAsBinaryValue = DateTimeRepresent::getCompareListDaysVsBinaryValues()[$day];
        $weekDayAsBinaryValue = DateTimeRepresent::getCompareListWeekDaysVsBinaryValues()[$weekDay];
        $monthAsBinaryValue = DateTimeRepresent::getCompareListMonthsVsBinaryValues()[$month];

        $findCondition = "      time <= {$currentTimesInSecond}
                            AND days&{$dayAsBinaryValue}
                            AND week_days&{$weekDayAsBinaryValue}
                            AND months&{$monthAsBinaryValue}
                            AND last_execution < {$dayStartTimestamp}";
        $limit = $limit ? 'LIMIT ' . $limit : '';

        $sql = <<<SQL
SELECT * FROM {$table}
WHERE  {$findCondition}
ORDER BY time ASC
{$limit}
FOR UPDATE
SQL;

        /** @var self[] $tasks */
        $tasks = self::model()->findAllBySql($sql);
        if (!$tasks) {
            return [];
        }

        if ($limit) {
            $ids = [];
            foreach ($tasks as $task) {
                $ids[] = $task->getGiud();
            }
            $updateCondition = "guid in('" . implode("','", $ids) . "')";
        } else {
            $updateCondition = $findCondition;
        }

        self::model()->updateAll([
            'last_execution' => $timestamp,
        ], $updateCondition);

        return $tasks;
    }
}