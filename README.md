# yii-scheduler for Yii 1

The scheduler module help you call specify method of your models by schedule

## Requirements
* PHP >= 5.3.0
* Yii 1.1.x
* Mysql 5

## Setup
Configure your composer.json with next lines

    {
      "require": {
        "itStorm/yii-scheduler":"dev-master"
      },
      "repositories":[
        {
          "type": "git",
          "url": "git@github.com:itStorm/yii-scheduler.git"
        }
      ]
    }

## Usage

1. Create table in your database for saving task for the scheduler. (see file CREATE_TABLE.SQL)
2. Add yii-scheduler in components block of the Yii config file

        'scheduler' => [
            'class' => 'YiiScheduler\Scheduler',
        ],
3. Also enable component in console configuration file and add comand mapping

        'commandMap' => [
            'run_tasks' => [
              'class' => 'YiiScheduler\Commands\RunTasks',
            ],
        ],

Now you can call component from your application.  
**Yii::app()->scheduler->addTrigger(...)** - create new trigger  
**Yii::app()->scheduler->dropTrigger(...)** - drop trigger(s)  
  
*For more details and setting see YiiScheduler\Scheduler*  

## Console command
**~: yiic run_tasks**  
  
*For more details and setting see YiiScheduler\Commands\RunTasks*
