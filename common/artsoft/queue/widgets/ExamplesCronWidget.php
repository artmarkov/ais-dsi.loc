<?php
namespace artsoft\queue\widgets;

use yii\base\Widget;
use Yii;

/**
 * Description of ExamplesCronWidget
 *
 * @author artmarkov
 */
class ExamplesCronWidget extends Widget {
    
    public $model;
    public $dataProvider;

    /**
     * getCronExample
     * @return array
     */
     public static function getCronExample() {
        return [
            ['cron' => '* * * * *', 'text' => Yii::t('art/queue', 'Run every minute.')],
            ['cron' => '0 0 * * *', 'text' => Yii::t('art/queue', 'Run every day in Midnight.')],
            ['cron' => '*/5 * * * *', 'text' => Yii::t('art/queue', 'Run every 5 minute.')],
            ['cron' => '0 0 */15 * *', 'text' => Yii::t('art/queue', 'Run 2 times a month.')],
            ['cron' => '5 10 */9 * *', 'text' => Yii::t('art/queue', 'Run in 10:05 every 9 days.')],
            ['cron' => '0 0-12/3 */15 * *', 'text' => Yii::t('art/queue', 'Run every 3 hours in period between 0 to 12 hours a day every 15 days.')],
            ['cron' => '0 0 * 5 4', 'text' => Yii::t('art/queue', 'Run in Midnight every Thursday in May.')],
            ['cron' => '0 0 * * 6,7', 'text' => Yii::t('art/queue', 'Run Midnight every Saturday and Sunday.')],
            ['cron' => '0 12 * * 0-4', 'text' => Yii::t('art/queue', 'Run at Noon every Sunday through Thursday.')],
            ['cron' => '0 0 * * 5L', 'text' => Yii::t('art/queue', 'Run every last Friday of the month.')],
            ['cron' => '0 0 * * 3#4', 'text' => Yii::t('art/queue', 'Run every fourth Wednesday of the month.')],
            ['cron' => '0 0 16W * *', 'text' => Yii::t('art/queue', 'Run on the next working day at Midnight of the 16th of each month.')],
        ];
    }  
    
    public function run() {
  
        $this->dataProvider = new \yii\data\ArrayDataProvider([
            'allModels' => $this->getCronExample(),
        ]);
        $this->dataProvider->pagination = false;
        
        return $this->render('examplesCron', [
                    'model' => $this->model,
                    'dataProvider' => $this->dataProvider,                    
        ]);
    }
}
