<?php

namespace artsoft\helpers;

use yii\helpers\ArrayHelper;

/**
 * Class RefBook
 * @package artsoft\helpers
 *
 * add migration
 *
 * $this->createTable('refbooks', [
 * 'name' => $this->string(50)->notNull(),
 * 'table_name' => $this->string(30)->notNull(),
 * 'key_field' => $this->string(30)->notNull(),
 * 'value_field' => $this->string(30)->notNull(),
 * 'sort_field' => $this->string(30)->notNull(),
 * 'ref_field' => $this->string(30),
 * 'group_field' => $this->string(30),
 * 'note' => $this->string(100)
 * ]);
 * $this->addPrimaryKey('refbooks_pkey', 'refbooks', 'name');
 *
 * usage
 *
 * RefBook::find('teachers_fio')->getList();
 * RefBook::find('teachers_fio')->getValue($id);
 */
class RefBook
{
    protected $name;
    protected $refId;
    protected $grouping;
    protected $list;

    /**
     * Возвращает экземпляр справочника
     * @param string $name имя справочника
     * @param string $refId опциональное значение для зависимого справочника
     * @param bool $grouping атрибут группировки
     * @return RefBook
     * @throws RuntimeException
     */
    public static function find($name, $refId = null, $grouping = false)
    {
        return new self($name, $refId, $grouping);
    }

    /**
     * @param $name
     * @param null $refId
     * @param bool $grouping
     * @throws RuntimeException
     */
    public function __construct($name, $refId = null, $grouping = false)
    {
        $this->name = $name;
        $this->refId = $refId;
        $this->grouping = $grouping;
        $this->load();
    }

    public function getList()
    {
        return $this->list;
    }

    public function keyExists($key)
    {
        return array_key_exists($key, $this->list);
    }

    public function lookupKey($value)
    {
        return array_search($value, $this->list);
    }

    public function getValue($key)
    {
        if (!$this->keyExists($key)) {
            return null; //throw new Exception('key not found: key='.$key);
        }
        return $this->list[$key];
    }

    /**
     * @throws RuntimeException
     */
    protected function load()
    {
        $r = (new \yii\db\Query)->from('refbooks')->where(['name' => $this->name])->one();
        if (false === $r) {
            throw new \RuntimeException('refbook "' . $this->name . '" was not found');
        }
        $query = (new \yii\db\Query)->from($r['table_name'])->orderBy($r['sort_field']);
        if ($r['ref_field'] && $this->refId) {
            $query->where([$r['ref_field'] => $this->refId]);
        }
        $this->list = ArrayHelper::map($query->all(), $r['key_field'], $r['value_field'], $this->grouping ? $r['group_field'] : null);
    }

    /**
     * @param $str |array Строка со значениями для массива
     * @param string $delimeter разделитель
     * @param bool $emptyValue
     * @param string $emptyValueName
     * @return array
     */
    public static function makeList($str, $delimeter = ';', $emptyValue = false, $emptyValueName = '- не указано -')
    {
        $str = is_array($str) ? implode($delimeter, $str) : $str;
        $list = array_map('trim', explode($delimeter, $str));
        return ($emptyValue ? ['' => $emptyValueName] : []) + array_combine($list, $list);
    }
}