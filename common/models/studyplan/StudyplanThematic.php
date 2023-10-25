<?php

namespace common\models\studyplan;

use artsoft\Art;
use artsoft\behaviors\DateFieldBehavior;
use artsoft\helpers\RefBook;
use artsoft\models\User;
use common\models\teachers\Teachers;
use Yii;
use yii\base\ErrorException;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\AfterSaveEvent;

/**
 * This is the model class for table "studyplan_thematic".
 *
 * @property int $id
 * @property int|null $subject_sect_studyplan_id
 * @property int|null $studyplan_subject_id
 * @property int $thematic_category
 * @property int $half_year
 * @property int|null $template_flag
 * @property string|null $template_name
 * @property int $doc_status
 * @property int $author_id
 * @property int $doc_sign_teachers_id
 * @property int $doc_sign_timestamp
 * @property int $created_at
 * @property int|null $created_by
 * @property int $updated_at
 * @property int|null $updated_by
 *
 * @property StudyplanThematicItems[] $studyplanThematicItems
 */
class StudyplanThematic extends \artsoft\db\ActiveRecord
{
    const THEMATIC_PLAN = 1;
    const REPERTORY_PLAN = 2;

    public $thematic_list;
    public $thematic_flag;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'studyplan_thematic';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            BlameableBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['thematic_category', 'half_year'], 'required'],
            [['subject_sect_studyplan_id', 'studyplan_subject_id', 'thematic_category', 'template_flag', 'author_id'], 'integer'],
            [['doc_status', 'doc_sign_teachers_id', 'doc_sign_timestamp', 'half_year'], 'integer'],
            [['doc_status'], 'default', 'value' => self::DOC_STATUS_DRAFT],
            [['half_year'], 'default', 'value' => 0],
            [['template_name'], 'string', 'max' => 256],
            [['template_name'], 'unique'],
            [['thematic_list', 'thematic_flag'], 'safe'],
            [['template_name'], 'required', 'when' => function ($model) {
                return $model->template_flag == '1';
            },
                'whenClient' => "function (attribute, value) {
                                return $('input[id=\"studyplanthematic-template_flag\"]').prop('checked');
                            }"],
            [['thematic_list'], 'required', 'when' => function ($model) {
                return $model->thematic_flag == '1';
            },
                'whenClient' => "function (attribute, value) {
                                return $('input[id=\"studyplanthematic-thematic_flag\"]').prop('checked');
                            }"],
            [['doc_sign_teachers_id'], 'exist', 'skipOnError' => true, 'targetClass' => Teachers::class, 'targetAttribute' => ['doc_sign_teachers_id' => 'id']],
            [['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['author_id' => 'id']],

        ];
    }


    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art/studyplan', 'ID'),
            'subject_sect_studyplan_id' => Yii::t('art/guide', 'Sect_Name'),
            'studyplan_subject_id' => Yii::t('art/guide', 'Subject Name'),
            'thematic_category' => Yii::t('art/studyplan', 'Thematic Category'),
            'half_year' => Yii::t('art/guide', 'Half Year'),
            'template_flag' => Yii::t('art/studyplan', 'Template Flag'),
            'template_name' => Yii::t('art/studyplan', 'Template Name'),
            'doc_status' => Yii::t('art/guide', 'Doc Status'),
            'author_id' => Yii::t('art', 'Author'),
            'doc_sign_teachers_id' => Yii::t('art/guide', 'Sign Teachers'),
            'doc_sign_timestamp' => Yii::t('art/guide', 'Sign Time'),
            'created_at' => Yii::t('art', 'Created'),
            'created_by' => Yii::t('art', 'Created By'),
            'updated_at' => Yii::t('art', 'Updated'),
            'updated_by' => Yii::t('art', 'Updated By'),
        ];
    }

    /**
     * Gets query for [[Author]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAuthor()
    {
        return $this->hasOne(User::class, ['id' => 'author_id']);
    }


    /**
     * Gets query for [[Teachers]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTeachers()
    {
        return $this->hasOne(Teachers::class, ['id' => 'doc_sign_teachers_id']);
    }

    /**
     * Gets query for [[StudyplanThematicItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStudyplanThematicItems()
    {
        return $this->hasMany(StudyplanThematicItems::className(), ['studyplan_thematic_id' => 'id'])->orderBy('id');
    }

    public static function getCategoryList()
    {
        return array(
            self::THEMATIC_PLAN => Yii::t('art/studyplan', 'Thematic Plan'),
            self::REPERTORY_PLAN => Yii::t('art/studyplan', 'Repertory Plan'),
        );
    }

    public static function getCategoryValue($val)
    {
        $ar = self::getCategoryList();
        return isset($ar[$val]) ? $ar[$val] : $val;
    }

    public function getTemplateList()
    {
        $userId = Yii::$app->user->identity->getId();
        $models = self::find()->select(['id', 'template_name'])
            ->where(['=', 'author_id', $userId])
            ->where(['is not', 'template_name', null])
            ->orderBy('template_name')->all();

        return \yii\helpers\ArrayHelper::map($models, 'id', 'template_name');
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if ($this->author_id == '' and Art::isFrontend()) {
            $this->author_id = Yii::$app->user->identity->getId();
        }

        if ($this->template_flag == 0) {
            $this->template_name = null;
        }
        if ($this->isAttributeChanged('doc_status')) {
            if ($this->doc_status == self::DOC_STATUS_AGREED) {
                $this->doc_sign_timestamp = time();
            } else {
                $this->doc_sign_timestamp = null;
            }
        }
        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if ($this->thematic_flag) {
            $m = self::find()->where(['id' => $this->thematic_list])->one();
            $modelsItems = $m->studyplanThematicItems;
            $transaction = \Yii::$app->db->beginTransaction();
            $flag = false;
            try {
                foreach ($modelsItems as $modelItems) {
                    $modelNew = new StudyplanThematicItems();
                    $modelNew->studyplan_thematic_id = $this->id;
                    $modelNew->piece_category_id = $modelItems->piece_category_id;
                    $modelNew->author = $modelItems->author;
                    $modelNew->piece_name = $modelItems->piece_name;
                    $modelNew->task = $modelItems->task;
                    if (!($flag = $modelNew->save(false))) {
                        $transaction->rollBack();
                        break;
                    }
                }

                if ($flag) {
                    $transaction->commit();
                }
            } catch (\Exception $e) {
                $transaction->rollBack();
            }
        }

    }

}
