<?php

namespace common\widgets\history;

use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\helpers\Json;

abstract class BaseHistory extends Model
{
    protected $model;
    protected $objId;

    public $type;
    public $attr_label;
    public $display_value_old;
    public $display_value_new;
    public $updated_at;
    public $updated_by_username;

    private $_filtered = false;

    public function __construct($objectId)
    {
        $this->objId = $objectId;
    }

    public function rules()
    {
        return [
            [['type', 'attr_label', 'display_value_old', 'display_value_new', 'updated_at', 'updated_by_username'], 'safe'],
        ];
    }

    public function search($params)
    {

        if ($this->load($params) && $this->validate()) {
            $this->_filtered = true;
        }
        return new \yii\data\ArrayDataProvider([
            'allModels' => $this->getData(),
            'sort' => [
                'attributes' => ['type', 'attr_label', 'display_value_old', 'display_value_new', 'updated_at', 'updated_by_username'],
            ],
            'pagination' => [
                'pageSize' => \Yii::$app->request->cookies->getValue('_grid_page_size', 20),
            ],
        ]);
    }

    protected function getData()
    {
        $data = $this->getHistory();

        if ($this->_filtered) {
            $data = array_filter($data, function ($value) {
                $conditions = [true];
                if (!empty($this->type)) {
                    $conditions[] = strpos($value['type'], $this->type) !== false;
                }
                if (!empty($this->attr_label)) {
                    $conditions[] = strpos($value['attr_label'], $this->attr_label) !== false;
                }
                if (!empty($this->display_value_old)) {
                    $conditions[] = strpos($value['display_value_old'], $this->display_value_old) !== false;
                }
                if (!empty($this->display_value_new)) {
                    $conditions[] = strpos($value['display_value_new'], $this->display_value_new) !== false;
                }
                if (!empty($this->updated_at)) {
                    $conditions[] = strpos($value['updated_at'], $this->updated_at) !== false;
                }
                if (!empty($this->updated_by_username)) {
                    $conditions[] = strpos($value['updated_by_username'], $this->updated_by_username) !== false;
                }
                return array_product($conditions);
            });
        }

        return $data;
    }

    abstract public static function getTableName();

    abstract public static function getModelName();

    abstract protected function getFields();

    protected static function getDisplayValue($model, $name, $value)
    {
        if ('group_id' == $name) {
            return $model->group ? $model->group->name : null;
        } else {
            return $value;
        }
    }

    public static function getLinkedIdList($linkFiledName, $id)
    {
        $res = array_reduce((new \yii\db\Query)->select('id')->distinct()->from(static::getTableName())->where([$linkFiledName => $id])->all(), function ($result, $item) {
            $result[] = $item['id'];
            return $result;
        });
        // var_dump($result);
        // exit;
        return $res ? $res : [];
    }

    /**
     * Возвращает исторические версии объекта
     * @return \common\models\BaseModel[]
     */
    protected function getModelRevisions()
    {
        $result = [];
        $hist = (new \yii\db\Query)->from(static::getTableName())->where(['id' => $this->objId])->orderBy('hist_id')->all();
        foreach ($hist as $data) {
            $key = $data['hist_id'] . '.' . $data['op'];
            unset($data['hist_id'], $data['op']);
            try {
                $data['class'] = static::getModelName();
                $m = \Yii::createObject($data);
                $m->afterFind();
                $result[$key] = $m;
            } catch (InvalidConfigException $e) {
            }
        }
        return $result;
    }

    public function getHistory()
    {
        $previous = null;
        $list = $this->getModelRevisions();
        $data = [];
        foreach ($list as $k => $m) {

            list($histId, $op) = explode('.', $k);
            if ('I' == $op) {
                $this->buildHistItems($data, $histId, null, $m);
            } elseif ('D' == $op) {
                $this->buildHistItems($data, $histId, $previous, $m);
                $this->buildHistItems($data, $histId, $m, null);
            } else {
                $this->buildHistItems($data, $histId, $previous, $m);
            }
            $previous = $m;
        }
        //var_dump($data);exit;
        krsort($data);

        return $data;
    }

    protected function buildHistItems(&$data, $histId, $modelOld, $modelNew)
    {
        $attrList = $this->getFields();
        if (null === $modelNew) {
            foreach ($attrList as $attr) {
                if (is_array($modelOld->{$attr})) {
                    $modelOld->{$attr} = Json::encode($modelOld->{$attr});
                }
                try {
                    $item = \Yii::createObject([
                        'class' => Item::class,
                        'updated_at' => $modelOld->updated_at,
                        'updated_by' => $modelOld->updated_by,
                        'attr_name' => $attr,
                        'attr_label' => $modelOld->getAttributeLabel($attr),
                        'value_old' => $modelOld->{$attr},
                        'value_new' => null,
                        'display_value_old' => static::getDisplayValue($modelOld, $attr, $modelOld->{$attr}),
                        'display_value_new' => null,
                        'group' => $histId,
                        'type' => 'Удаление'
                    ]);
                    $data[$modelOld->updated_at . $histId . $attr] = $item->toArray();
                } catch (InvalidConfigException $e) {
                }
            };
        } else {
            $type = $modelOld == null ? 'Создание' : 'Изменение';
            $modelOld = $modelOld ? $modelOld : \Yii::createObject(static::getModelName());
            foreach ($attrList as $attr) {
                if (is_array($modelOld->{$attr})) {
                    $modelOld->{$attr} = Json::encode($modelOld->{$attr});
                }
                if (is_array($modelNew->{$attr})) {
                    $modelNew->{$attr} = Json::encode($modelNew->{$attr});
                }
                if ($modelOld->{$attr} == $modelNew->{$attr}) {
                    continue;
                }
                try {
                    $item = \Yii::createObject([
                        'class' => Item::className(),
                        'updated_at' => $modelNew->updated_at,
                        'updated_by' => $modelNew->updated_by,
                        'attr_name' => $attr,
                        'attr_label' => $modelNew->getAttributeLabel($attr),
                        'value_old' => $modelOld->{$attr},
                        'value_new' => $modelNew->{$attr},
                        'display_value_old' => static::getDisplayValue($modelOld, $attr, $modelOld->{$attr}),
                        'display_value_new' => static::getDisplayValue($modelNew, $attr, $modelNew->{$attr}),
                        'group' => $histId,
                        'type' => $type
                    ]);
                    $data[$modelNew->updated_at . $histId . $attr] = $item->toArray();
                } catch (InvalidConfigException $e) {
                }
            }
        }
    }
}