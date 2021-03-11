<?php

namespace backend\widgets\fullcalendarscheduler\src;

/**
 * Class FullcalendarScheduler ver.5.5.1
 * @package  backend\widgets\fullcalendarscheduler\src
 */
class FullcalendarScheduler extends \yii\base\Widget
{
    /**
     * @var array  Default options for the id and class HTML attributes
     */
    public $options = [
        'id' => null,
        'class' => null,
    ];

    /**
     * @var array $headerToolbar
     */
    public $headerToolbar = [
        'left' => 'today prev,next',
        'center' => 'title',
        'right' => 'resourceTimelineDay,resourceTimelineThreeDays,timeGridWeek,dayGridMonth,listWeek',
    ];

    /**
     * @var array $clientOptions
     */
    public $clientOptions = [

    ];

    /**
     * @var array  Array containing the events, can be JSON array,
     * PHP array or URL that returns an array containing JSON events
     */
    public $events = [];

    /**
     * @var array  Array containing the resources, can be JSON array,
     * PHP array or URL that returns an array containing JSON resources
     */
    public $resources = [];

    /**
     * Always make sure we have a valid id and class for the FullcalendarScheduler widget
     */
    public function init()
    {
        if (!isset($this->options['id'])) {
            $this->options['id'] = $this->getId();
        }
        if (!isset($this->options['class'])) {
            $this->options['class'] = 'fullcalendarscheduler';
        }

        if (!isset($this->clientOptions['locale'])) {
            $this->clientOptions['locale'] = \Yii::$app->language;
        }
        parent::init();
    }

    /**
     * Load the options and start the widget
     */
    public function run()
    {
        $this->wrapper();

        $assets = CoreAsset::register($this->view);
        if (isset($this->clientOptions['locale'])) {
            $assets->language = $this->clientOptions['locale'];
        }

        $this->clientOptions['headerToolbar'] = $this->headerToolbar;

        $js = <<<JS
    document.addEventListener('DOMContentLoaded', function() {
    var calendar = new FullCalendar.Calendar(document.getElementById('{$this->options['id']}'), {$this->getClientOptions()});
    calendar.render();
  });
JS;

        $this->view->registerJs($js, \yii\web\View::POS_HEAD);
    }

    /**
     * Echo the tags to show the calendar
     */
    private function wrapper()
    {
        echo \yii\helpers\Html::beginTag('div', $this->options);
        echo \yii\helpers\Html::endTag('div') . "\n";
    }

    /**
     * @return string
     * Returns an JSON array containing the fullcalendar options,
     * all available callbacks will be wrapped in JsExpressions objects if they're set
     */
    private function getClientOptions()
    {

        $options['events'] = $this->events;
        $options = array_merge($options, $this->clientOptions);

        return \yii\helpers\Json::encode($options);
    }
}
