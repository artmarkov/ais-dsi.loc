<?php

namespace common\widgets\weeklyscheduler;

use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\JsExpression;

/**
 * https://github.com/ateliee/jquery.schedule
 * Chart renders a canvas WeeklyScheduler plugin widget.
 */
class WeeklyScheduler extends Widget
{
    public $readonly = false;
    public $options = [];
    public $clientOptions = [];
    public $data = [];
    public $startTime = '08:00';
    public $endTime = '21:00';
    public $widthTime = 60 * 5;
    public $timeLineY = 50;
    public $verticalScrollbar = 20;
    public $timeLineBorder = 1;
    public $bundleMoveWidth = 6;
    public $draggable = false; //isDraggable
    public $resizable = false; //'isResizable'
    public $resizableLeft = false;
    public $rows = [
        0 => ['title' => 'Понедельник'],
        1 => ['title' => 'Вторник'],
        2 => ['title' => 'Среда'],
        3 => ['title' => 'Четверг'],
        4 => ['title' => 'Пятница'],
        5 => ['title' => 'Суббота'],
        6 => ['title' => 'Воскресение']
    ];

    public $events = [];

    /**
     * Initializes the widget.
     * This method will register the bootstrap asset bundle. If you override this method,
     * make sure you call the parent implementation first.
     * @throws \Exception
     */
    public function init()
    {
        parent::init();

        if (!isset($this->options['id'])) {
            $this->options['id'] = $this->getId();
        }
        if ($this->readonly === true) {
            $this->draggable = false;
            $this->resizable = false;
            $this->events = [];
        }
        $this->getData();
    }

    /**
     * @return $this
     * @throws \Exception
     */
    public function getData()
    {
        foreach ($this->data as $item) {
            if (!isset($item['week_day'])) {
                throw new \Exception('week_day is required');
            }
            $this->rows[$item['week_day'] - 1]['schedule'][] = [
                'start' => $item['time_in'],
                'end' => $item['time_out'],
                'text' => $item['title'],
                'data' => $item['data'],
            ];
        }
        return $this;
    }

    /**
     * Renders the widget.
     */
    public function run()
    {
        echo Html::tag('div', '', $this->options);
        $this->registerClientScript();
    }

    /**
     * Registers the required js files and script to initialize WeeklyScheduler plugin
     */
    protected function registerClientScript()
    {
        $id = $this->options['id'];
        $view = $this->getView();
        WeeklySchedulerAsset::register($view);

        $client_option = array_merge($this->clientOptions,
            [
                'startTime' => $this->startTime,
                'endTime' => $this->endTime,
                'widthTime' => $this->widthTime,
                'timeLineY' => $this->timeLineY,
                'verticalScrollbar' => $this->verticalScrollbar,
                'timeLineBorder' => $this->timeLineBorder,
                'bundleMoveWidth' => $this->bundleMoveWidth,
                'draggable' => $this->draggable,
                'resizable' => $this->resizable,
                'resizableLeft' => $this->resizableLeft,
                'rows' => $this->rows,
            ],
            $this->events
        );

        $config = Json::encode($client_option ?: new JsExpression('{}'));
        $js = "$('#{$id}').timeSchedule($config);";
        $view->registerJs($js);
    }
}
