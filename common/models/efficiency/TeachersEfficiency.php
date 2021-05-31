<?php

namespace common\models\efficiency;

use artsoft\behaviors\DateFieldBehavior;
use artsoft\helpers\ExcelObjectList;
use artsoft\models\User;
use artsoft\traits\DateTimeTrait;
use common\models\creative\CreativeWorks;
use Yii;
use common\models\teachers\Teachers;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "teachers_efficiency".
 *
 * @property int $id
 * @property int $efficiency_id
 * @property int $teachers_id
 * @property int $item_id
 * @property string|null $bonus
 * @property int $date_in
 * @property int $created_at
 * @property int|null $created_by
 * @property int $updated_at
 * @property int|null $updated_by
 * @property int $version
 * @property string $class
 *
 * @property GuideEfficiencyTree $efficiency
 * @property Teachers $teachers
 */
class TeachersEfficiency extends \artsoft\db\ActiveRecord
{
    use DateTimeTrait;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'teachers_efficiency';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            BlameableBehavior::class,
            [
                'class' => DateFieldBehavior::class,
                'attributes' => ['date_in'],
                'timeFormat' => 'd.m.Y',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['efficiency_id', 'teachers_id', 'date_in', 'bonus'], 'required'],
            [['efficiency_id', 'created_at', 'created_by', 'updated_at', 'updated_by', 'version', 'item_id'], 'integer'],
            [['date_in', 'teachers_id'], 'safe'],
            [['version'], 'default', 'value' => 0],
            [['bonus'], 'string', 'max' => 127],
            ['class', 'string'],
            [['efficiency_id'], 'exist', 'skipOnError' => true, 'targetClass' => EfficiencyTree::class, 'targetAttribute' => ['efficiency_id' => 'id']],
            // [['teachers_id'], 'exist', 'skipOnError' => true, 'targetClass' => Teachers::class, 'targetAttribute' => ['teachers_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art/guide', 'ID'),
            'efficiency_id' => Yii::t('art/guide', 'Efficiency'),
            'class' => Yii::t('art/guide', 'Class'),
            'item_id' => Yii::t('art/guide', 'Item'),
            'teachers_id' => Yii::t('art/teachers', 'Teachers'),
            'bonus' => Yii::t('art/guide', 'Bonus'),
            'date_in' => Yii::t('art/guide', 'Date Bonus In'),
            'created_at' => Yii::t('art', 'Created'),
            'created_by' => Yii::t('art', 'Created By'),
            'updated_at' => Yii::t('art', 'Updated'),
            'updated_by' => Yii::t('art', 'Updated By'),
            'version' => Yii::t('art', 'Version'),
        ];
    }

    public function optimisticLock()
    {
        return 'version';
    }

    /**
     * Gets query for [[Efficiency]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEfficiency()
    {
        return $this->hasOne(EfficiencyTree::class, ['id' => 'efficiency_id']);
    }


    /* Геттер для названия категории */
    public function getEfficiencyName()
    {
        return $this->efficiency->name;
    }

    public function getTeachersName()
    {
        return $this->teachers->getFullName();
    }

    /**
     * Gets query for [[Teachers]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTeachers()
    {
        return $this->hasOne(Teachers::class, ['id' => 'teachers_id']);
    }

    /**
     * Gets query for [[CreatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * Gets query for [[UpdatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }

    /**
     * Связи с моделями где применяется надбавка
     *
     * @return array|bool
     */
    public function getDependence()
    {
        switch ($this->class) {
            case 'CreativeWorks':
                return [
                    'model' => \common\models\creative\CreativeWorks::findOne($this->item_id),
                    'attributes' => [
                        'name',
                        'description',
                        'published_at',
                    ],
                    'link' => ['/creative/default/view', 'id' => $this->item_id],
                    'title' => 'Сведения о работе',
                ];
            default:
                return false;
        }
    }

    /**
     * @param $model_date
     * @return array
     */
    public static function getSummaryData($model_date)
    {
//        echo '<pre>' . print_r($model_date, true) . '</pre>';
        $timestamp_in = Yii::$app->formatter->asTimestamp($model_date->date_in);
        $timestamp_out = Yii::$app->formatter->asTimestamp($model_date->date_out) + 86399;

        $tree = EfficiencyTree::getEfficiencyRoots();
        $root = EfficiencyTree::getEfficiencyLiaves();

        $attributes = ['name' => 'Фамилия И.О.'];
        $attributes += $root;
        $attributes += ['stake' => 'Ставка руб.'];
        $attributes += ['total' => 'Надбавка %'];
        $attributes += ['total_sum' => 'Сумма руб.'];

        $models = self::find()
            ->where(['between', 'date_in', $timestamp_in, $timestamp_out])
            ->asArray()->all();

        $res = [];
        $all_summ = 0;
        foreach ($models as $model) {
            $res[$model['teachers_id']][$tree[$model['efficiency_id']]] = isset($res[$model['teachers_id']][$tree[$model['efficiency_id']]]) ? $res[$model['teachers_id']][$tree[$model['efficiency_id']]] + $model['bonus'] : $model['bonus'];
            $res[$model['teachers_id']]['total'] = isset($res[$model['teachers_id']]['total']) ? $res[$model['teachers_id']]['total'] + $model['bonus'] : $model['bonus'];
        }
        $data = [];
        foreach (\artsoft\helpers\RefBook::find('teachers_fio', \common\models\user\UserCommon::STATUS_ACTIVE)->getList() as $id => $name) {
            if (!($model_date->hidden_flag && !isset($res[$id]))) { // скрываем пустые строки
                $data[$id] = $res[$id] ?? ['total' => null];
                $data[$id]['id'] = $id;
                $data[$id]['name'] = $name;
                $data[$id]['stake'] = \artsoft\helpers\RefBook::find('teachers_stake')->getValue($id);
                $data[$id]['total_sum'] = $data[$id]['total'] * $data[$id]['stake'] * 0.01;
                $all_summ += $data[$id]['total_sum'];
                $data[$id]['date_in'] = $timestamp_in;
                $data[$id]['date_out'] = $timestamp_out;
            }
        }
        return ['data' => $data, 'all_summ' => $all_summ, 'attributes' => $attributes, 'root' => $root];
    }

    /**
     * @param $data
     * @return bool
     */
    public static function sendXlsx($data)
    {
        ini_set('memory_limit', '512M');
        try {
            $x = new ExcelObjectList($data['attributes']);
            foreach ($data['data'] as $item) { // данные
                $x->addData($item);
            }
            $x->addData(['total' => 'Итого', 'total_sum' => $data['all_summ']]);

            \Yii::$app->response
                ->sendContentAsFile($x, strtotime('now') . '_' . Yii::$app->getSecurity()->generateRandomString(6) . '_teachers_efficiency.xlsx', ['mimeType' => 'application/vnd.ms-excel'])
                ->send();
            exit;
        } catch (\PhpOffice\PhpSpreadsheet\Exception | \yii\web\RangeNotSatisfiableHttpException $e) {
            \Yii::error('Ошибка формирования xlsx: ' . $e->getMessage());
            \Yii::error($e);
            Yii::$app->session->setFlash('error', 'Ошибка формирования xlsx-выгрузки');
            return true;
        }
    }
}
