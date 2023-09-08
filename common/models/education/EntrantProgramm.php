<?php

namespace common\models\education;

use artsoft\models\User;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "entrant_programm".
 *
 * @property int $id
 * @property int $programm_id Учебная программа
 * @property int $subject_type_id
 * @property int $course
 * @property string $name Название программы для предварительной записи
 * @property int $age_in Ограничение по возрасту снизу
 * @property int $age_out Ограничение по возрасту сверху
 * @property int $qty_entrant Кол-во учеников для приема
 * @property int $qty_reserve Кол-во учеников для резерва
 * @property string|null $description
 * @property int $created_at
 * @property int|null $created_by
 * @property int $updated_at
 * @property int|null $updated_by
 * @property int $status
 *
 * @property EntrantPreregistrations[] $entrantPreregistrations
 * @property EducationProgramm $programm
 */
class EntrantProgramm extends \artsoft\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'entrant_programm';
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            BlameableBehavior::class,
            TimestampBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['programm_id', 'subject_type_id', 'course', 'name', 'age_in', 'age_out', 'qty_entrant', 'qty_reserve'], 'required'],
            [['programm_id', 'subject_type_id', 'course', 'age_in', 'age_out', 'qty_entrant', 'qty_reserve', 'status'], 'integer'],
            [['description'], 'string'],
            [['name'], 'string', 'max' => 255],
            [['programm_id'], 'exist', 'skipOnError' => true, 'targetClass' => EducationProgramm::class, 'targetAttribute' => ['programm_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['updated_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art/guide', 'ID'),
            'programm_id' => Yii::t('art/studyplan', 'Education Programm'),
            'subject_type_id' => Yii::t('art/guide', 'Subject Type'),
            'course' => Yii::t('art/guide', 'Course'),
            'name' => Yii::t('art/guide', 'Name'),
            'age_in' => Yii::t('art/student', 'Age In'),
            'age_out' => Yii::t('art/student', 'Age Out'),
            'qty_entrant' => Yii::t('art/student', 'Qty Entrant'),
            'qty_reserve' => Yii::t('art/student', 'Qty Reserve'),
            'description' => Yii::t('art', 'Description'),
            'created_at' => Yii::t('art', 'Created'),
            'created_by' => Yii::t('art', 'Created By'),
            'updated_at' => Yii::t('art', 'Updated'),
            'updated_by' => Yii::t('art', 'Updated By'),
            'status' => Yii::t('art', 'Status'),
        ];
    }

    /**
     * Gets query for [[EntrantPreregistrations]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEntrantPreregistrations()
    {
        return $this->hasMany(EntrantPreregistrations::class, ['entrant_programm_id' => 'id']);
    }

    /**
     * Gets query for [[Programm]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProgramm()
    {
        return $this->hasOne(EducationProgramm::class, ['id' => 'programm_id']);
    }

    /**
     * @return array
     */
    public static function getEntrantProgrammList($status = true)
    {
        $query = self::find()->select('id, name');
        if ($status == true) {
            $query = $query->where(['status' => self::STATUS_ACTIVE]);
        }
        $query = $query->orderBy('name')->asArray()->all();
        return ArrayHelper::map($query, 'id', 'name');
    }

    public static function getEntrantProgrammValue($val, $status = true)
    {
        $ar = self::getEntrantProgrammList($status);
        return isset($ar[$val]) ? $ar[$val] : $val;
    }
    /**
     * Получение списка программ для предварительной записи с учетом возраста ребенка и оставшихся мест
     * @param $age
     * @param $plan_year
     * @return array
     * @throws \yii\db\Exception
     */
    public static function getEntrantProgrammLimitList($age, $plan_year)
    {
        $query =\Yii::$app->db->createCommand('SELECT id, name, age_in, age_out, qty_entrant, qty_reserve, description, status, summ_entrant, summ_reserve
            FROM (SELECT id, name, age_in, age_out, qty_entrant, qty_reserve, description, status,
                    (SELECT COUNT(entrant_preregistrations.id) FROM entrant_preregistrations where entrant_preregistrations.entrant_programm_id = entrant_programm.id AND reg_vid = 1 AND plan_year = :plan_year) as summ_entrant,
                    (SELECT COUNT(entrant_preregistrations.id) FROM entrant_preregistrations where entrant_preregistrations.entrant_programm_id = entrant_programm.id AND reg_vid = 2 AND plan_year = :plan_year) as summ_reserve
            FROM entrant_programm) data
            WHERE (qty_entrant > summ_entrant OR qty_reserve > summ_reserve)  AND age_in <= :age AND age_out >= :age AND status = :status ',
            [
                'plan_year' => $plan_year,
                'age' => $age,
                'status' => self::STATUS_ACTIVE,
            ])->queryAll();

        return ArrayHelper::map($query, 'id', 'name');
    }

    /**
     * Получение колличества записей программы по виду(обучение,резерв) за учебный год
     * @param $entrant_programm_id
     * @param $reg_vid
     * @param $plan_year
     * @return false|int|string|null
     * @throws \yii\db\Exception
     */
    public static function getEntrantRegVidCount($entrant_programm_id, $reg_vid, $plan_year)
    {
        return \Yii::$app->db->createCommand('SELECT COUNT(id) 
            FROM entrant_preregistrations 
            where entrant_programm_id = :entrant_programm_id 
            AND reg_vid = :reg_vid AND plan_year = :plan_year',
            [
                'entrant_programm_id' => $entrant_programm_id,
                'reg_vid' => $reg_vid,
                'plan_year' => $plan_year,
            ])->queryScalar();
    }

    /**
     * Автоматическое определение вида записи (обучение,резерв)
     * @param $entrant_programm_id
     * @param $plan_year
     * @return int
     * @throws \yii\db\Exception
     */
    public static function getEntrantRegVid($entrant_programm_id, $plan_year){
        $qty_entrant = self::getEntrantRegVidCount($entrant_programm_id, EntrantPreregistrations::REG_ENTRANT, $plan_year);
        $model = self::findOne(['id' => $entrant_programm_id]);
        return $model->qty_entrant > $qty_entrant ? EntrantPreregistrations::REG_ENTRANT : EntrantPreregistrations::REG_RESERVE;
    }
}
