<?php

namespace common\models\concourse;

use artsoft\models\User;
use common\widgets\editable\Editable;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;

class ConcourseAnswers
{
    public $id;
    public $model;
    public $modelItem;
    public $objectId;
    public $modelsCriteria;
    public $models;
    const mark_list = [1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6, 7 => 7, 8 => 8, 9 => 9, 10 => 10]; // веса оценок

    public function __construct($config = [])
    {
        $this->id = $config['id'];
        $this->objectId = $config['objectId'];
        $this->model = $this->getModelConcourse(); // модель конкурса
        $this->modelItem = $this->getModelConcourseItem(); // модель работы
        $this->modelsCriteria = $this->getModelsCriteria(); // все критерии конкурса
    }

    public function getAnswersConcourseUsers($userId)
    {
        $modelsItems = [];
        if (!$userId) {
            throw new NotFoundHttpException("Отсутствует обязательный параметр userId.");
        }
        $models = $this->modelsCriteria->all();

        foreach ($models as $item => $model) {
            $m = ConcourseValue::find()->where(['users_id' => $userId])
                ->andWhere(['concourse_item_id' => $this->objectId])
                ->andWhere(['concourse_criteria_id' => $model->id])
                ->one();
            if (!$m) {
                $m = new ConcourseValue();
                $m->users_id = $userId;
                $m->concourse_item_id = $this->objectId;
                $m->concourse_criteria_id = $model->id;
                $m->save(false);
            }
            $modelsItems[] = $m;
        }
//        echo '<pre>' . print_r($modelsItems, true) . '</pre>'; die();
        return $modelsItems;

    }

    public function getModelConcourse()
    {
        return Concourse::findOne($this->id);
    }

    public function getModelConcourseItem()
    {
        return ConcourseItem::findOne($this->objectId);
    }

    public function getMarkList()
    {
        return self::mark_list;
    }

    public function getModelsCriteria()
    {
        return ConcourseCriteria::find()->where(['concourse_id' => $this->id])->orderBy('sort_order');
    }

    public function getUsers()
    {
        $diff = array_diff($this->model->users_list, $this->modelItem->authors_list); // авторы не участвуют в оценке
        return User::getUsersByIds($diff);
    }

    public function attributesCriteria()
    {
        return ArrayHelper::map($this->modelsCriteria->asArray()->all(), 'id', 'name');
    }

    public function attributesCriteriaDev()
    {
        return ArrayHelper::map($this->modelsCriteria->asArray()->all(), 'id', 'name_dev');
    }

    public function getData()
    {
        $data = [];
        $root = $this->attributesCriteriaDev();

        $attributes = ['name' => 'Участник'];
        $attributes += $root;
        $attributes += ['summ' => 'Сумма'];
        $attributes += ['mid' => 'Среднее'];

        $models = ConcourseValue::find()->where(['concourse_item_id' => $this->objectId])->asArray()->all();
        $res = [];
        $all_summ = 0;
        foreach ($models as $model) {
            $res[$model['users_id']][$model['concourse_criteria_id']] = self::getEditableForm($model['concourse_mark'], $model['users_id'], $this->objectId, $model['concourse_criteria_id']);
            $res[$model['users_id']]['summ'] = isset($res[$model['users_id']]['summ']) ? $res[$model['users_id']]['summ'] + $model['concourse_mark'] : $model['concourse_mark'];
        }

        foreach ($this->getUsers() as $id => $name) {
            $data[$id] = $res[$id] ?? ['summ' => null];
            $data[$id]['modelId'] = $this->id;
            $data[$id]['objectId'] = $this->objectId;
            $data[$id]['id'] = $id;
            $data[$id]['name'] = $name;
            $data[$id]['mid'] = $data[$id]['summ'] / count($root);
            $all_summ += isset($res[$id]['summ']) ? $res[$id]['summ'] : null;
        }
//        echo '<pre>' . print_r($data, true) . '</pre>'; die();
        usort($data, function ($a, $b) {
            return $b['summ'] <=> $a['summ'];
        });

        return ['data' => $data, 'all_mid' => $all_summ / count($root), 'attributes' => $attributes, 'root' => $root];
    }

    public static function getEditableForm($mark, $users_id, $concourse_item_id, $concourse_criteria_id)
    {
        $mark_list = self::mark_list;

        return Editable::widget([
            'buttonsTemplate' => "{reset}{submit}",
            'name' => 'concourse_mark',
            'asPopover' => true,
            'disabled' => /*$users_id !== null ? (\artsoft\Art::isFrontend() && !Teachers::isOwnTeacher($teachers_id)) :*/ false,
            'value' => $mark,
            'header' => 'Выберите оценку',
            'displayValueConfig' => $mark_list,
            'format' => Editable::FORMAT_LINK,
            'inputType' => Editable::INPUT_DROPDOWN_LIST,
            'data' => $mark_list,
            'size' => 'md',
            'options' => ['class' => 'form-control', 'placeholder' => Yii::t('art', 'Select...')],
            'formOptions' => [
                'action' => Url::toRoute(['/concourse/default/set-mark', 'users_id' => $users_id, 'concourse_item_id' => $concourse_item_id, 'concourse_criteria_id' => $concourse_criteria_id]),
            ],
        ]);
    }
}
