<?php

namespace common\models\teachers;

use artsoft\dbmanager\models\Db;
use artsoft\helpers\ArtHelper;
use common\models\schoolplan\SchoolplanPerform;
use common\models\schoolplan\SchoolplanView;
use yii\helpers\ArrayHelper;
use Yii;
use yii\db\Query;

/**
 * This is the model class for table "portfolio_view".
 *
 */
class PortfolioTeachers
{
    protected $date_in;
    protected $date_out;
    protected $teachers_id;

    public function __construct($model_date, $teachers_id)
    {
        $timestamp = ArtHelper::getStudyYearParams($model_date->plan_year);

        $this->date_in = $timestamp['timestamp_in'];
        $this->date_out = $timestamp['timestamp_out'];
        $this->teachers_id = $teachers_id;
        //        echo '<pre>' . print_r($this->category_list, true) . '</pre>';
//        die();
    }

    /**
     * @return array
     * @throws \yii\db\Exception
     */
    public function getData()
    {
        $models =  (new Query())->from('portfolio_teachers_view')
            ->where(new \yii\db\Expression("{$this->teachers_id} = any (string_to_array(executors_list, ',')::int[])"))
            ->andWhere(['between', 'datetime_in', $this->date_in, $this->date_out])
            ->andWhere(['not in', 'category_id', [1,2,3,4,5,6,7,8,9]])
            ->all();
//        echo '<pre>' . print_r($models, true) . '</pre>';
//        die();
        $attributes = [
            'title' => 'Название мероприятия',
            'category_id' => Yii::t('art/guide', 'category_id'),
            'winner_id' => 'Звание/Диплом',
            'resume' => 'Результат',
            'sect_name' => Yii::t('art/guide', 'Sect Name'),
            'subject' => Yii::t('art/guide', 'Subject'),
        ];

        $data = [];
        foreach ($models as $i => $model) {

                $data[$i]['resource'] = $model['resource'];
                $data[$i]['id'] = $model['id'];
                $data[$i]['schoolplan_id'] = $model['schoolplan_id'];
                $data[$i]['title'] = Yii::$app->formatter->asDatetime($model['datetime_in']) . ' - ' . Yii::$app->formatter->asDatetime($model['datetime_out']) . '<br/>' . $model['title'];
                $data[$i]['winner_id'] = SchoolplanPerform::getWinnerValue($model['winner_id']);
                $data[$i]['category_id'] = $model['category_id'];
                $data[$i]['resume'] = $model['resource'] == 'schoolplan' ? 'Ответственный' : $model['resume'];
                $data[$i]['sect_name'] = $model['sect_name'];
                $data[$i]['subject'] = $model['subject'];

        }
        return ['data' => $data, 'attributes' => $attributes];
    }
}
