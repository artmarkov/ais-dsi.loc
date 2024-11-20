<?php

namespace common\models\question;

use artsoft\helpers\DocTemplate;
use artsoft\models\User;
use common\widgets\qrcode\QRcode;
use common\widgets\qrcode\widgets\Link;
use kartik\date\DatePicker;
use kartik\datetime\DateTimePicker;
use Yii;
use yii\base\DynamicModel;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
use yii\web\UploadedFile;
use yii\widgets\MaskedInput;

class QuestionAnswers extends DynamicModel
{
    public $id;
    public $model;
    public $objectId;
    public $models;
    private $attributes;
    private $attributesTypes;
    private $attributesUnique;
    private $optionsValues;
    private $fileSize = 1024 * 1024 * 5; // Допустимый размер файла
    private $fileExt = 'png, jpg, JPEG, doc, docx, pdf'; // Допустимые расширения

    public function __construct($config = [])
    {
        $this->id = $config['id'];
        $this->objectId = $config['objectId'] ?? 0;
        $this->models = $this->getModels();
        $this->model = $this->getModelQuestion();
        $this->attributes = array_merge(array_values($this->attributes()), ['question_users_id', 'users_id', 'read_flag']);
        $this->attributesTypes = $this->getAttributesType();
        $this->attributesUnique = $this->getAttributesUnique();
        $this->optionsValues = $this->getOptionsValue();
        $this->addRules();
        $this->setAttributeLabels($this->labels());
//        echo '<pre>' . print_r($this->model, true) . '</pre>';

        parent::__construct($this->attributes, $config);
    }

    /**
     *  Нахождение id модели QuestionValue для имени атрибута
     * @param $id
     * @return |null
     */
    public function getValueId($attribute)
    {
        $values = ArrayHelper::map($this->loadValue(), 'name', 'question_value_id');
        return $values[$attribute];
    }

    public function getModels()
    {
        return QuestionAttribute::find()->where(['question_id' => $this->id])->orderBy('sort_order');
    }

    public function getModelQuestion()
    {
        return Question::findOne(['id' => $this->id]);
    }

    public function attributes()
    {
        return ArrayHelper::map($this->models->asArray()->all(), 'id', 'name');
    }

    public function getAttributesType()
    {
        return ArrayHelper::map($this->models->asArray()->all(), 'name', 'type_id');
    }

    public function getAttributesUnique()
    {
        return QuestionAttribute::find()->select('name')->where(['question_id' => $this->id])->where(['unique_flag' => 1])->column();
    }

    public function getOptionsValue()
    {
        $models = QuestionOptions::find()->innerJoin('question_attribute', 'question_attribute.id = question_options.attribute_id')
            ->where(['=', 'question_attribute.question_id', $this->id])->orderBy('question_options.name')->asArray()->all();
        return ArrayHelper::map($models, 'id', 'name');
    }

    private function addRules()
    {
        foreach ($this->models->asArray()->all() as $model) {
            if ($model['required'] == 1) {
                $this->addRule($model['name'], 'required');
            }
            if ($this->model->vid_id == Question::VID_OPEN) {
                $this->addRule('users_id', 'required');
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
        $labels = ['question_users_id' => '#'];
        $labels += ['users_id' => 'Пользователь'];
        $labels += ['read_flag' => 'Статус'];
        $labels += ArrayHelper::map($this->models->asArray()->all(), 'name', 'label');
        return $labels;
    }

    public function attributeHints()
    {
        return ArrayHelper::map($this->models->asArray()->all(), 'name', 'hint');
    }

    private function loadQuery()
    {
        return QuestionValue::find()->select(
            'question_attribute.question_id as question_id,
                     question_attribute.type_id as type_id,
                     question_attribute.name as name,
                     question_users.users_id as users_id, 
                     question_users.read_flag as read_flag, 
                     question_users.id as question_users_id,
                     question_value.id as question_value_id,
                     question_value.question_attribute_id as question_attribute_id,
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

    public function loadValue()
    {
        return $this->objectId ? $this->loadQuery()->andWhere(['=', 'question_users.id', $this->objectId])->asArray()->all() : [];
    }

    public function getDataArrayAll()
    {
        $data = [];
//        echo '<pre>' . print_r($this->loadValues(), true) . '</pre>'; die();
        foreach ($this->loadValues() as $model) {
            $data[$model['question_users_id']] = isset($data[$model['question_users_id']]) ? array_merge($data[$model['question_users_id']], $this->getDataManager($model)) : $this->getDataManager($model);
        }
        return ['data' => $data, 'attributes' => $this->labels(), 'types' => $this->attributesTypes];
    }

    public function getDataOne()
    {
        foreach ($this->loadValue() as $model) {
            $this->question_users_id = $model['question_users_id'];
            $this->users_id = $model['users_id'];
            $name = $model['name'];
            $this->$name = $this->getValue($model);
        }

        return $this;
    }

    public function getDataManager($model)
    {
        $user = User::findOne($model['users_id']);
        $data['question_id'] = $model['question_id'];
        $data['users_id'] = isset($user->userCommon) ? $user->userCommon->getFullName() : 'Гость';
        $data['read_flag'] = QuestionUsers::getReadValue($model['read_flag']);
        $data['question_users_id'] = $model['question_users_id'];
        $data[$model['name']] = $this->getValueManager($model);
        return $data;
    }

    protected function getValueManager($model) 
    {
        switch ($model['type_id']) {
            case QuestionAttribute::TYPE_TEXT :
                $value = mb_strlen($model['value_string'], 'UTF-8') > 200 ? mb_substr($model['value_string'], 0, 200, 'UTF-8') . '...' : $model['value_string'];
                break;
            case QuestionAttribute::TYPE_RADIOLIST :
            case QuestionAttribute::TYPE_RADIOLIST_UNIQUE :
                $value = $this->optionsValues[$model['question_option_list']] ?? $model['question_option_list'];
                break;
            case QuestionAttribute::TYPE_CHECKLIST :
            case QuestionAttribute::TYPE_CHECKLIST_UNIQUE :
                $values = [];
                foreach (explode(',', $model['question_option_list']) as $option_id) {
                    $values[] = $this->optionsValues[$option_id] ?? $option_id;
                }
                $value = implode(',', $values);
                break;
            case QuestionAttribute::TYPE_FILE :
                $value = self::getFileContent($model['value_file']);
                break;
            default:
                $value = $model['value_string'];
        }
        return $value;
    }

    protected function getValue($model)
    {
        switch ($model['type_id']) {
            case QuestionAttribute::TYPE_RADIOLIST :
            case QuestionAttribute::TYPE_RADIOLIST_UNIQUE :
                $value = $model['question_option_list'];
                break;
            case QuestionAttribute::TYPE_CHECKLIST :
            case QuestionAttribute::TYPE_CHECKLIST_UNIQUE :
                $value = explode(',', $model['question_option_list']);
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
                $form = $form->radioList($this->getOptionsList($item['id']), ['itemOptions' => ['disabled' => $options['readonly']]]);
                break;
            case QuestionAttribute::TYPE_RADIOLIST_UNIQUE :
                $form = $form->radioList($this->getOptionsListUnique($item['id']), ['itemOptions' => ['disabled' => $options['readonly']]]);
                break;
            case QuestionAttribute::TYPE_CHECKLIST :
                $form = $form->checkboxList($this->getOptionsList($item['id']), ['itemOptions' => ['disabled' => $options['readonly']]]);
                break;
            case QuestionAttribute::TYPE_CHECKLIST_UNIQUE :
                $form = $form->checkboxList($this->getOptionsListUnique($item['id']), ['itemOptions' => ['disabled' => $options['readonly']]]);
                break;
            case QuestionAttribute::TYPE_FILE :
                $form = $form->fileInput(['disabled' => $options['readonly']]);
                break;
            default:
                $form = $form->textInput(['maxlength' => true]);
        }
        return $form->hint($item['hint']);
    }

    public function getOptionsList($id)
    {
        $modelOptions = QuestionOptions::find()->select(['id', 'name'])->where(['=', 'attribute_id', $id])->orderBy('name')->asArray()->all();
        return ArrayHelper::map($modelOptions, 'id', 'name');
    }

    public function getOptionsListUnique($id)
    {
        $opt_available = $this->getOptionsList($id);
        $query = $this->loadQuery()->andWhere(['question_attribute_id' => $id]);
        if ($this->objectId) $query = $query->andWhere(['!=', 'question_users_id', $this->objectId]);
        $query = $query->asArray()->all();
        $opt_okuppied = implode(',', ArrayHelper::getColumn($query, 'question_option_list'));

        foreach (explode(',', $opt_okuppied) as $key) {
            unset($opt_available[$key]);
        }
        return $opt_available;
    }

    public function save()
    {
        $modelName = StringHelper::basename($this::className());
        $data = Yii::$app->request->post();
        $user = QuestionUsers::findOne(['id' => $this->objectId, 'question_id' => $this->id]) ?? new QuestionUsers();
        $user->question_id = $this->id;
        $user->users_id = $data[$modelName]['users_id'] ?? Yii::$app->getUser()->getId();
        $valid = $user->validate();
        if ($valid) {
            $transaction = \Yii::$app->db->beginTransaction();
            try {
                if ($flag = $user->save(false)) {
                    foreach ($this->attributes() as $id => $attribute) {
                        $modelAttribute = QuestionValue::findOne(['question_users_id' => $user->id, 'question_attribute_id' => $id]) ?? new QuestionValue();
                        $modelAttribute->question_users_id = $user->id;
                        $modelAttribute->question_attribute_id = $id;

                        switch ($this->attributesTypes[$attribute]) {
                            case QuestionAttribute::TYPE_RADIOLIST :
                            case QuestionAttribute::TYPE_RADIOLIST_UNIQUE :
                                $modelAttribute->question_option_list = $data[$modelName][$attribute];
                                break;
                            case QuestionAttribute::TYPE_CHECKLIST :
                            case QuestionAttribute::TYPE_CHECKLIST_UNIQUE :
                                $modelAttribute->question_option_list = is_array($data[$modelName][$attribute]) ? implode(',', $data[$modelName][$attribute]) : $data[$modelName][$attribute];
                                break;
                            case QuestionAttribute::TYPE_FILE :
                                $file = UploadedFile::getInstanceByName('QuestionAnswers[' . $attribute . ']');
                                $file->saveAs(Yii::getAlias('@runtime/cache') . DIRECTORY_SEPARATOR . $file->name);
                                $modelAttribute->value_string = $file->name;
                                $modelAttribute->value_file = base64_encode(file_get_contents(Yii::getAlias('@runtime/cache') . DIRECTORY_SEPARATOR . $file->name));
                                break;
                            default:
                                $modelAttribute->value_string = $data[$modelName][$attribute];
                        }
                        $flag = $this->validateAttribute($id, $attribute, $modelAttribute); // Валидация на уникальность
//                        echo '<pre>' . print_r($modelAttribute, true) . '</pre>';
                        if (!($flag && $flag = $modelAttribute->save(false))) {
                            $transaction->rollBack();
                            break;
                        }
                    }
                }
                if ($flag) {
                    $transaction->commit();
                    $this->objectId = $user->id;
                    $this->sendAuthorMessage();
                    $this->sendUserMessage();
                    return true;
                }
            } catch (\Exception $e) {
                $transaction->rollBack();
                print_r($e->getMessage());
                return false;
            }
        }
        return false;
    }

    public function delete($id)
    {
        return QuestionUsers::deleteAll(['id' => $id, 'question_id' => $this->id]);
    }

    protected function validateAttribute($id, $attribute, $modelAttribute)
    {
        $flag = true;
        if (in_array($attribute, $this->attributesUnique)) {
            $query = QuestionValue::find()->where(['question_attribute_id' => $id, 'value_string' => $modelAttribute->value_string]);
            if (isset($modelAttribute->id)) {
                $query = $query->andWhere(['!=', 'id', $modelAttribute->id]);
            }
            if ($query->exists()) {
                $flag = false;
                $this->addError($attribute, 'Запись с такими данными уже была введена.');
            }
        }
        return $flag;
    }

    /**
     * @return string
     */
    public static function getFileContent($valueFileBin)
    {
        return is_resource($valueFileBin) ? 'data:image/png;base64,' . stream_get_contents($valueFileBin) : '';
    }

    public function sendMessage($user_id, $email)
    {
        $sender = false;

        if ($email) {
            $sender = Yii::$app->mailqueue->compose();

            $textBody = 'Сообщение модуля "Формы и заявки" ' . PHP_EOL;
            $htmlBody = '<p><b>Сообщение модуля "Формы и заявки"</b></p>';

            $textBody .= 'Вы успешно заполнили форму: ' . strip_tags($this->model->name) . PHP_EOL;
            $htmlBody .= '<p>Вы успешно заполнили форму: ' . strip_tags($this->model->name) . '</p>';

            if ($this->model->category_id == Question::CAT_TICKET) {
                $textBody .= 'Распечатайте или покажите на телефоне QR-код при посещении мероприятия.';
                $htmlBody .= '<p>Распечатайте или покажите на телефоне QR-код при посещении мероприятия.</p>';
                $sender = $this->addQrTicket($user_id, $sender);
            }
            $textBody .= '--------------------------' . PHP_EOL;
            $textBody .= 'Сообщение создано автоматически. Отвечать на него не нужно.';
            $htmlBody .= '<hr>';
            $htmlBody .= '<p>Сообщение создано автоматически. Отвечать на него не нужно.</p>';

            $sender->setFrom([Yii::$app->params['adminEmail'] => Yii::$app->name])
                ->setTo($email)
                ->setSubject('Сообщение с сайта ' . Yii::$app->name)
                ->setTextBody($textBody)
                ->setHtmlBody($htmlBody)
                ->queue();
        }
        return $sender;
    }

    public function addQrTicket($user_id, $sender)
    {
        $template = 'document/ticket.docx';
        $output_file_name = Yii::getAlias('@runtime/cache') . DIRECTORY_SEPARATOR . Yii::$app->getSecurity()->generateRandomString() . '_' . basename($template);

        $token = base64_encode(json_encode(['id' => $this->model->id, 'version' => $this->model->version, 'user_id' => $user_id]));
        $link = Yii::$app->urlManager->createAbsoluteUrl(['/question/default/validate', 'token' => $token], 'https');
        $data[] = [
            'rank' => 'doc',
            'name' => $this->model->name,
            'num' => sprintf('#%03d%05d', $this->model->id, $user_id),
            'description' => strip_tags($this->model->description)
        ];

        $data_qr[] = [
            'rank' => 'qr',
            'qr_code' => Link::widget([
                'outputDir' => '@runtime/qrcode',
                'outputDirWeb' => '@runtime/qrcode',
                'ecLevel' => QRcode::QR_ECLEVEL_L,
                'text' => $link,
                'size' => 3
            ]),
        ];
        $tbs = DocTemplate::get($template)->setHandler(function ($tbs) use ($data, $data_qr) {
            /* @var $tbs clsTinyButStrong */
            $tbs->MergeBlock('doc', $data);
            $tbs->MergeBlock('qr', $data_qr);

        })->prepare();
        $tbs->Show(OPENTBS_STRING, $output_file_name);
        file_put_contents($output_file_name, $tbs->Source);
        $sender->attach($output_file_name);

        return $sender;
    }

    public function sendAuthorMessage()
    {
        if ($this->model->email_author_flag) {
            $title = 'Сообщение модуля "Формы и заявки"';
            $content = '<p>Заполнена новая форма: ' . strip_tags($this->model->name) . '</p>';
            return Yii::$app->mailbox->mailing($this->model->author_id, $content, $title);
        }
    }

    /**
     * Отправляет сообщение на Емайл пользователя, или на заполненный Емайл, если есть такое поле.
     * @return bool
     */
    public function sendUserMessage()
    {
        $userEmail = null;
        if ($this->model->email_flag) {
            $user = QuestionUsers::findOne(['id' => $this->objectId, 'question_id' => $this->id]);
            if ($user->users_id) {
                $userModel = User::findOne($user->users_id);
                $userEmail = $userModel->email;
            } else {
                foreach ($this->attributes() as $id => $attribute) {
                    if ($this->attributesTypes[$attribute] == QuestionAttribute::TYPE_EMAIL) {
                        $modelAttribute = QuestionValue::findOne(['question_users_id' => $user->id, 'question_attribute_id' => $id]);
                        $userEmail = $modelAttribute->value_string;
                        break;
                    }
                }
            }
            return $this->sendMessage($user->id, $userEmail);
        }
        return true;
    }
}
