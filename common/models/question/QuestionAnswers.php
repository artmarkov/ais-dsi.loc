<?php

namespace common\models\question;

use kartik\date\DatePicker;
use kartik\datetime\DateTimePicker;
use Yii;
use yii\base\DynamicModel;
use yii\helpers\ArrayHelper;
use yii\widgets\MaskedInput;

class QuestionAnswers extends DynamicModel
{
    public $id;
    private $attributes;
    private $fileSize = 1024 * 1024 * 5; // Допустимый размер файла
    private $fileExt = 'png, jpg, pdf'; // Допустимые расширения

    public function __construct($config = [])
    {
        $this->id = $config['id'];
        $this->attributes = $this->attributes();
        $this->addRules();
        $this->setAttributeLabels($this->labels());

//        echo '<pre>' . print_r($models, true) . '</pre>';
        parent::__construct($this->attributes, $config);
    }

    public function getModel()
    {
        return QuestionAttribute::find()->where(['=', 'question_id', $this->id])->orderBy('sort_order');
    }

    public function attributes()
    {
        return array_values(ArrayHelper::map($this->getModel()->asArray()->all(), 'id', 'name'));
    }

    private function addRules()
    {
        if ($this->getModel()->andWhere(['=', 'required', 1])->exists()) {
            $this->addRule(array_values(ArrayHelper::map($this->getModel()->andWhere(['=', 'required', 1])->asArray()->all(), 'id', 'name')), 'required');
        }
        if ($this->getModel()->andWhere(['=', 'type_id', [QuestionAttribute::TYPE_STRING, QuestionAttribute::TYPE_TEXT]])->exists()) {
            $this->addRule(array_values(ArrayHelper::map($this->getModel()->andWhere(['=', 'type_id', [QuestionAttribute::TYPE_STRING, QuestionAttribute::TYPE_TEXT]])->asArray()->all(), 'id', 'name')), 'string', ['max' => 1024]);
        }
        if ($this->getModel()->andWhere(['=', 'type_id', QuestionAttribute::TYPE_EMAIL])->exists()) {
            $this->addRule(array_values(ArrayHelper::map($this->getModel()->andWhere(['=', 'type_id', QuestionAttribute::TYPE_EMAIL])->asArray()->all(), 'id', 'name')), 'email');
        }
        if ($this->getModel()->andWhere(['=', 'type_id', QuestionAttribute::TYPE_FILE])->exists()) {
            $this->addRule(array_values(ArrayHelper::map($this->getModel()->andWhere(['=', 'type_id', QuestionAttribute::TYPE_FILE])->asArray()->all(), 'id', 'name')), 'file', ['skipOnEmpty' => false, 'extensions' => $this->fileExt, 'maxSize' => $this->fileSize]);
        }
        if ($this->getModel()->andWhere(['=', 'type_id', [QuestionAttribute::TYPE_RADIOLIST, QuestionAttribute::TYPE_CHECKLIST]])->exists()) {
            $this->addRule(array_values(ArrayHelper::map($this->getModel()->andWhere(['=', 'type_id', [QuestionAttribute::TYPE_RADIOLIST, QuestionAttribute::TYPE_CHECKLIST]])->asArray()->all(), 'id', 'name')), 'safe');
        }
        if ($this->getModel()->andWhere(['=', 'type_id', QuestionAttribute::TYPE_DATE])->exists()) {
            $this->addRule(array_values(ArrayHelper::map($this->getModel()->andWhere(['=', 'type_id', QuestionAttribute::TYPE_DATE])->asArray()->all(), 'id', 'name')), 'date');
        }
        if ($this->getModel()->andWhere(['=', 'type_id', QuestionAttribute::TYPE_DATETIME])->exists()) {
            $this->addRule(array_values(ArrayHelper::map($this->getModel()->andWhere(['=', 'type_id', QuestionAttribute::TYPE_DATETIME])->asArray()->all(), 'id', 'name')), 'datetime');
        }
    }

    private function labels()
    {
        $labels = ['id' => '#'];
        $labels += ['users_id' => 'Пользователь'];
        $labels += ArrayHelper::map($this->getModel()->asArray()->all(), 'name', 'label');
        return $labels;
    }

    public function attributeHints()
    {
        return ArrayHelper::map($this->getModel()->asArray()->all(), 'name', 'hint');
    }

    private function getQuestionValue()
    {
        return QuestionValue::find()->select('*')
            ->innerJoin('question_attribute', 'question_attribute.id = question_value.question_attribute_id')
            ->innerJoin('question_users', 'question_users.id = question_value.question_users_id')
            ->where(['=', 'question_attribute.question_id', $this->id])
            ->asArray()
            ->all();
    }

    public function getData()
    {
        $data = [];
        foreach ($this->getQuestionValue() as $model) {
            $data[$model['question_users_id']]['question_id'] = $model['question_id'];
            $data[$model['question_users_id']]['users_id'] = $model['users_id'];
            $data[$model['question_users_id']]['id'] = $model['question_users_id'];
            switch ($model['type_id']) {
                case QuestionAttribute::TYPE_STRING :
                case QuestionAttribute::TYPE_TEXT :
                case QuestionAttribute::TYPE_DATE :
                case QuestionAttribute::TYPE_DATETIME :
                case QuestionAttribute::TYPE_EMAIL :
                case QuestionAttribute::TYPE_PHONE :
                    $value = $model['value_string'];
                    break;
                case QuestionAttribute::TYPE_RADIOLIST :
                case QuestionAttribute::TYPE_CHECKLIST :
                    $value = $model['question_option_list']; // TODO развернуть в цикле
                    break;
                case QuestionAttribute::TYPE_FILE :
                    $value = $model['value_file'];
                    break;
                default:
                    $value = $model['value_string'];
            }
            $data[$model['question_users_id']][$model['name']] = $value;
        }
        return ['data' => $data, 'attributes' => $this->labels()];
    }

    public function getForm($form, $item, $options = ['readonly' => false])
    {
        switch ($item['type_id']) {
            case QuestionAttribute::TYPE_STRING :
            case QuestionAttribute::TYPE_EMAIL :
                return $form->field($this, $item['name'])->textInput(['maxlength' => true])->hint($item['hint']);
                break;
            case QuestionAttribute::TYPE_TEXT :
                return $form->field($this, $item['name'])->textarea(['rows' => 4])->hint($item['hint']);
                break;
            case QuestionAttribute::TYPE_DATE :
                return $form->field($this, $item['name'])->widget(MaskedInput::class, ['mask' => Yii::$app->settings->get('reading.date_mask')])->widget(DatePicker::class, ['disabled' => $options['readonly']])->hint($item['hint']);
                break;
            case QuestionAttribute::TYPE_DATETIME :
                return $form->field($this, $item['name'])->widget(MaskedInput::class, ['mask' => Yii::$app->settings->get('reading.date_time_mask')])->widget(DateTimePicker::class, ['disabled' => $options['readonly']])->hint($item['hint']);
                break;
            case QuestionAttribute::TYPE_PHONE :
                return $form->field($this, $item['name'])->widget(MaskedInput::class, ['mask' => Yii::$app->settings->get('reading.phone_mask')])->textInput()->hint($item['hint']);
                break;
            case QuestionAttribute::TYPE_RADIOLIST :
                return $form->field($this, $item['name'])->radioList($this->getOptionsList($item['id']))->hint($item['hint']);
                break;
            case QuestionAttribute::TYPE_CHECKLIST :
                return $form->field($this, $item['name'])->checkboxList($this->getOptionsList($item['id']))->hint($item['hint']);
                break;
            case QuestionAttribute::TYPE_FILE :
                return $form->field($this, $item['name'])->fileInput()->hint($item['hint']);
                break;
            default:
                return $form->field($this, $item['name'])->textInput(['maxlength' => true]);
        }
    }

    public function getOptionsList($id)
    {
        $modelOptions = \common\models\question\QuestionOptions::find()->select(['id', 'name'])->where(['=', 'attribute_id', $id])->asArray()->all();
        return ArrayHelper::map($modelOptions, 'id', 'name');
    }

    public function save()
    {

        return true;// TODO
    }

    public static function findModel($id, $objectId)
    {

        return true;// TODO
    }
}
