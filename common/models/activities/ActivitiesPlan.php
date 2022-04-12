<?php

namespace common\models\activities;

use artsoft\models\User;
use common\models\auditory\Auditory;
use common\models\user\UserCommon;
use Yii;

/**
 * This is the model class for table "activities_plan".
 *
 * @property int $id
 * @property int $author_id Автор записи
 * @property string|null $name Название мероприятия
 * @property int $datetime_in Дата и время начала
 * @property int $datetime_out Дата и время окончания
 * @property string|null $places Место проведения
 * @property int|null $auditory_id Аудитория
 * @property string|null $department_list Отделы
 * @property string|null $teachers_list Ответственные
 * @property int $category_id Категория мероприятия
 * @property int|null $form_partic Форма участия
 * @property string|null $partic_price Стоимость участия
 * @property int|null $visit_flag Возможность посещения
 * @property string|null $visit_content Комментарий по посещению
 * @property int|null $important_flag Значимость мероприятия
 * @property string|null $region_partners Зарубежные и региональные партнеры
 * @property string|null $site_url Ссылка на мероприятие (сайт/соцсети)
 * @property string|null $site_media Ссылка на медиаресурс
 * @property string|null $description Описание мероприятия
 * @property string|null $rider Технические требования
 * @property string|null $result Итоги мероприятия
 * @property int|null $num_users Количество участников
 * @property int|null $num_winners Количество победителей
 * @property int|null $num_visitors Количество зрителей
 * @property int $created_at
 * @property int|null $created_by
 * @property int $updated_at
 * @property int|null $updated_by
 * @property int $version
 *
 * @property Auditory $auditory
 * @property GuidePlanTree $category
 * @property UserCommon $author
 */
class ActivitiesPlan extends \artsoft\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'activities_plan';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['author_id', 'datetime_in', 'datetime_out', 'category_id', 'created_at', 'updated_at'], 'required'],
            [['author_id', 'datetime_in', 'datetime_out', 'auditory_id', 'category_id', 'form_partic', 'visit_flag', 'important_flag', 'num_users', 'num_winners', 'num_visitors', 'created_at', 'created_by', 'updated_at', 'updated_by', 'version'], 'default', 'value' => null],
            [['author_id', 'datetime_in', 'datetime_out', 'auditory_id', 'category_id', 'form_partic', 'visit_flag', 'important_flag', 'num_users', 'num_winners', 'num_visitors', 'created_at', 'created_by', 'updated_at', 'updated_by', 'version'], 'integer'],
            [['visit_content', 'region_partners', 'description', 'rider', 'result'], 'string'],
            [['name'], 'string', 'max' => 100],
            [['places'], 'string', 'max' => 512],
            [['department_list', 'teachers_list'], 'string', 'max' => 1024],
            [['partic_price', 'site_url', 'site_media'], 'string', 'max' => 255],
            [['auditory_id'], 'exist', 'skipOnError' => true, 'targetClass' => Auditory::className(), 'targetAttribute' => ['auditory_id' => 'id']],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => GuidePlanTree::className(), 'targetAttribute' => ['category_id' => 'id']],
            [['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => UserCommon::className(), 'targetAttribute' => ['author_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'author_id' => 'Автор записи',
            'name' => 'Название мероприятия',
            'datetime_in' => 'Дата и время начала',
            'datetime_out' => 'Дата и время окончания',
            'places' => 'Место проведения',
            'auditory_id' => 'Аудитория',
            'department_list' => 'Отделы',
            'teachers_list' => 'Ответственные',
            'category_id' => 'Категория мероприятия',
            'form_partic' => 'Форма участия',
            'partic_price' => 'Стоимость участия',
            'visit_flag' => 'Возможность посещения',
            'visit_content' => 'Комментарий по посещению',
            'important_flag' => 'Значимость мероприятия',
            'region_partners' => 'Зарубежные и региональные партнеры',
            'site_url' => 'Ссылка на мероприятие (сайт/соцсети)',
            'site_media' => 'Ссылка на медиаресурс',
            'description' => 'Описание мероприятия',
            'rider' => 'Технические требования',
            'result' => 'Итоги мероприятия',
            'num_users' => 'Количество участников',
            'num_winners' => 'Количество победителей',
            'num_visitors' => 'Количество зрителей',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
            'version' => 'Version',
        ];
    }

    /**
     * Gets query for [[Auditory]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAuditory()
    {
        return $this->hasOne(Auditory::className(), ['id' => 'auditory_id']);
    }

    /**
     * Gets query for [[Category]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(GuidePlanTree::className(), ['id' => 'category_id']);
    }

    /**
     * Gets query for [[Author]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAuthor()
    {
        return $this->hasOne(UserCommon::className(), ['id' => 'author_id']);
    }


}
