<?php

namespace common\models\question;

use kartik\date\DatePicker;
use kartik\datetime\DateTimePicker;
use Yii;
use yii\base\DynamicModel;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
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
        $this->attributes = array_values($this->attributes());
        $this->addRules();
        $this->setAttributeLabels($this->labels());

        //echo '<pre>' . print_r($this->attributes, true) . '</pre>';
        parent::__construct($this->attributes, $config);
    }

    public function getModel()
    {
        return QuestionAttribute::find()->where(['=', 'question_id', $this->id])->orderBy('sort_order');
    }

    public function attributes()
    {
        return ArrayHelper::map($this->getModel()->asArray()->all(), 'id', 'name');
    }

    private function addRules()
    {
        foreach ($this->getModel()->asArray()->all() as $model) {
            if ($model['required'] == 1) {
                $this->addRule($model['name'], 'required');
            }
            switch ($model['type_id']) {
                case QuestionAttribute::TYPE_STRING :
                case QuestionAttribute::TYPE_TEXT :
                    $this->addRule($model['name'], 'string', ['max' => 1024]);
                    break;
                case QuestionAttribute::TYPE_DATE :
                    $this->addRule($model['name'], 'date');
                    break;
                case QuestionAttribute::TYPE_DATETIME :
                    $this->addRule($model['name'], 'datetime');
                    break;
                case QuestionAttribute::TYPE_EMAIL :
                    $this->addRule($model['name'], 'email');
                    break;
                case QuestionAttribute::TYPE_PHONE :
                case QuestionAttribute::TYPE_RADIOLIST :
                case QuestionAttribute::TYPE_CHECKLIST :
                    $this->addRule($model['name'], 'safe');
                    break;
                case QuestionAttribute::TYPE_FILE :
                    $this->addRule($model['name'], 'file', ['skipOnEmpty' => false, 'extensions' => $this->fileExt, 'maxSize' => $this->fileSize]);
                    break;
                default:
                    $this->addRule($model['name'], 'safe');
            }
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

    private function loadQuery()
    {
        return QuestionValue::find()->select(
            'question_attribute.question_id as question_id,
                     question_attribute.type_id as type_id,
                     question_attribute.name as name,
                     question_users.users_id as users_id, 
                     question_users.id as question_users_id,
                     question_value.value_string as value_string,
                     question_value.question_option_list as question_option_list,
                     question_value.value_file as value_file'
        )
            ->innerJoin('question_users', 'question_users.id = question_value.question_users_id')
            ->innerJoin('question_attribute', 'question_attribute.id = question_value.question_attribute_id')
            ->where(['=', 'question_attribute.question_id', $this->id]);
    }

    private function loadValues()
    {
        return $this->loadQuery()->asArray()->all();
    }

    public function loadValue($objectId)
    {
        return $this->loadQuery()->andWhere(['=', 'question_users.id', $objectId])->asArray()->all();
    }

    public function getDataAll()
    {
        $data = [];
//        echo '<pre>' . print_r($this->loadValues(), true) . '</pre>'; die();
        foreach ($this->loadValues() as $model) {
            $data[$model['question_users_id']] = isset($data[$model['question_users_id']]) ? array_merge($data[$model['question_users_id']], $this->addData($model)) : $this->addData($model);
        }
        return ['data' => $data, 'attributes' => $this->labels()];
    }

    public function getDataOne($objectId)
    {
        foreach ($this->loadValue($objectId) as $item => $model) {
            $name = $model['name'];
            $this->$name = $this->getValue($model);;
            }
        return $this;
    }

    public function addData($model)
    {
        $data['question_id'] = $model['question_id'];
        $data['users_id'] = $model['users_id'];
        $data['id'] = $model['question_users_id'];
        $data[$model['name']] = $this->getValue($model);
        return $data;
    }

    protected function getValue($model)
    {
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
                $value = $model['question_option_list']; // TODO
                break;
            case QuestionAttribute::TYPE_FILE :
                $value = $model['value_file'];
                break;
            default:
                $value = $model['value_string'];
        }
        return $value;
    }
    public function getForm($form, $item, $options = ['readonly' => false])
    {
        $form = $form->field($this, $item['name']);
        switch ($item['type_id']) {
            case QuestionAttribute::TYPE_STRING :
            case QuestionAttribute::TYPE_EMAIL :
                $form = $form->textInput(['maxlength' => true]);
                break;
            case QuestionAttribute::TYPE_TEXT :
                $form = $form->textarea(['rows' => 4]);
                break;
            case QuestionAttribute::TYPE_DATE :
                $form = $form->widget(MaskedInput::class, ['mask' => Yii::$app->settings->get('reading.date_mask')])->widget(DatePicker::class, ['disabled' => $options['readonly']]);
                break;
            case QuestionAttribute::TYPE_DATETIME :
                $form = $form->widget(MaskedInput::class, ['mask' => Yii::$app->settings->get('reading.date_time_mask')])->widget(DateTimePicker::class, ['disabled' => $options['readonly']]);
                break;
            case QuestionAttribute::TYPE_PHONE :
                $form = $form->widget(MaskedInput::class, ['mask' => Yii::$app->settings->get('reading.phone_mask')])->textInput();
                break;
            case QuestionAttribute::TYPE_RADIOLIST :
                $form = $form->radioList($this->getOptionsList($item['id']));
                break;
            case QuestionAttribute::TYPE_CHECKLIST :
                $form = $form->checkboxList($this->getOptionsList($item['id']));
                break;
            case QuestionAttribute::TYPE_FILE :
                $form = $form->fileInput();
                break;
            default:
                $form = $form->textInput(['maxlength' => true]);
        }
        return $form->hint($item['hint']);
    }

    public function getOptionsList($id)
    {
        $modelOptions = QuestionOptions::find()->select(['id', 'name'])->where(['=', 'attribute_id', $id])->asArray()->all();
        return ArrayHelper::map($modelOptions, 'id', 'name');
    }

    public function save()
    {
        $data = Yii::$app->request->post();
        $user = new QuestionUsers();
        $user->question_id = $this->id;
        $user->users_id = Yii::$app->getUser()->getId();

        $valid = $user->validate();
        if ($valid) {
            $transaction = \Yii::$app->db->beginTransaction();
            try {
                if ($flag = $user->save(false)) {
                    // print_r($data['QuestionAnswers']); die();
                    foreach ($this->attributes() as $id => $attribute) {
                        $modelAttribute = new QuestionValue();
                        $modelAttribute->question_users_id = $user->id;
                        $modelAttribute->question_attribute_id = $id;
                        $modelAttribute->value_string = $data[StringHelper::basename($this::className())][$attribute];
                        if (!($flag = $modelAttribute->save(false))) {
                            $transaction->rollBack();
                            break;
                        }
                    }
                }
                if ($flag) {
                    $transaction->commit();
                    $this->getSubmitAction();
                    return true;
                }
            } catch (\Exception $e) {
                $transaction->rollBack();
                return false;
            }
        }
        return false;
    }

    public function delete($id)
    {
        return QuestionUsers::deleteAll(['id' => $id, 'question_id' => $this->id]);
    }

}
