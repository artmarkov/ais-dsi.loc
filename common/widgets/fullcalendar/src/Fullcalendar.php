<?php

namespace common\widgets\fullcalendar\src;

/**
 * Class Fullcalendar ver.5.5.1
 * @package  common\widgets\fullcalendar\src
 */
class Fullcalendar extends \yii\base\Widget
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
        'left' => 'prev,next today',
        'center' => 'title',
        'right' => 'dayGridMonth,timeGridWeek,timeGridDay,listMonth',
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
     * Always make sure we have a valid id and class for the Fullcalendar widget
     */
    public function init()
    {
        if (!isset($this->options['id'])) {
            $this->options['id'] = $this->getId();
        }
        if (!isset($this->options['class'])) {
            $this->options['class'] = 'fullcalendar';
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
