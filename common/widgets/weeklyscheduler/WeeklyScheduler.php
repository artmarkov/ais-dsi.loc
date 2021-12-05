<?php

namespace common\widgets\weeklyscheduler;

use yii\base\InvalidConfigException;
use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\JsExpression;

/**
 * Chart renders a canvas WeeklyScheduler plugin widget.
 */
class WeeklyScheduler extends Widget
{
    /**
     * @var array the HTML attributes for the widget container tag.
     */
    public $options = [];
    /**
     * @var array the options for the underlying jQuery Schedule JS plugin.
     *            Please refer to the corresponding jQuery Schedule type plugin Web page https://github.com/Yehzuna/jquery-schedule for possible options.
     *            For example, [this page](https://yehzuna.github.io/jquery-schedule/)
     */
    public $clientOptions = [];

    public $data = [];
    /**
     * @var string
     * Default: edit
     * Options: read, edit
     */
    public $mode = 'edit';

    /**
     * @var int
     * Default: 24
     * Options: 12 24
     * Define the time format.
     */
    public $hour = 24;

    /**
     * @var int
     * Default : 30
     * Options: 15 30 60
     * Define the period duration interval.
     */
    public $periodDuration = 15;
    /**
     * @var array
     * Default : ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"]
     * Define list of days labels.
     */
    public $days = ['Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота', 'Воскресение'];

    /**
     * @var
     * onInit(jqs)
     * jqs Schedule container
     * A callback fire after the schedule init.
     *
     * onAddPeriod(period, jqs)
     * period The new period added
     * jqs Schedule container
     * A callback fire after a new period is added.
     *
     * onRemovePeriod(period, jqs)
     * period The period to remove
     * jqs Schedule container
     * A callback fire before a period is removed.
     *
     * onDuplicatePeriod(period, jqs)
     * period The period to duplicate
     * jqs Schedule container
     * A callback fire before a period is duplicated.
     *
     * onPeriodClicked(event, period, jqs)
     * event click event
     * period The period target
     * jqs Schedule container
     * A callback fire on a period click.
     */
    public $events;

    /**
     * Initializes the widget.
     * This method will register the bootstrap asset bundle. If you override this method,
     * make sure you call the parent implementation first.
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();

        if (!isset($this->options['id'])) {
            $this->options['id'] = $this->getId();
        }
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
            ['data' => $this->data],
            ['mode' => $this->mode],
            ['hour' => $this->hour],
            ['days' => $this->days],
            ['periodDuration' => $this->periodDuration],
            $this->events
        );

        $config = Json::encode($client_option ?: new JsExpression('{}'));
        $js = "$('#{$id}').jqs($config);";
        $view->registerJs($js);
    }
}
