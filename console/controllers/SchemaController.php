<?php

namespace console\controllers;

use yii\console\Controller;
use yii\db\Connection;
use yii\di\Instance;

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use yii\helpers\Console;

/**
 * console: php yii schema - схема из моделей
 * console: php yii schema/tables - схема из таблиц
 * output: data/schema.xlsx
 */
class SchemaController extends Controller
{

    public $db = 'db';
    public $data = [];

    /**
     * @param \yii\base\Action $action
     * @return bool
     * @throws \yii\base\InvalidConfigException
     */
    public function beforeAction($action): bool
    {
        if (!parent::beforeAction($action)) {
            return false;
        }

        $this->db = Instance::ensure($this->db, Connection::class);

        return true;
    }

    /**
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function actionIndex()
    {
        $data = null;
        $models = scandir('common/models');
        foreach ($models as $model) {
            if (preg_match('/\.(?:php)$/i', $model)) {
                $this->getLabelsInfo($model);
                $this->stdout("reading: - $model\n");

            }
        }
        $this->stdout("\n");
        $this->stdout("waiting...\n", Console::FG_RED);
        $this->getArray2Excel(['table_name', 'table_comment', 'type', 'name', 'comment'], array_values($this->data), 'data/labels.xlsx');
        $this->stdout("full complete!\n", Console::FG_GREEN);
    }

    /**
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     * @throws \yii\db\Exception
     */
    public function actionTables()
    {
        $data = null;
        $tables = $this->db->schema->getTableNames();
        foreach ($tables as $table) {
            if (!preg_match('/_hist$/', $table)) {
                $this->generateData($table);
                $this->stdout("reading: - $table\n");
            }
        }
        $this->stdout("\n");
        $this->stdout("waiting...\n", Console::FG_RED);
        $this->getArray2Excel(['table_name', 'table_comment', 'type', 'name', 'comment'], array_values($this->data), 'data/schema.xlsx');
        $this->stdout("full complete!\n", Console::FG_GREEN);
    }

    /**
     * @param $table
     * @return bool
     * @throws \yii\db\Exception
     */
    protected function generateData($table)
    {
        $tableSchema = $this->db->getTableSchema($table);

        if (!$tableSchema) {
            return false;
        }
        foreach ($tableSchema->columns as $column) {
            $columnPhpType = $column->phpType;
            if ($columnPhpType === 'integer') {
                $type = 'int';
            } elseif ($columnPhpType === 'boolean') {
                $type = 'bool';
            } else {
                $type = $columnPhpType;
            }
            $this->data[] = [
                'table_name' => $table,
                'table_comment' => $this->getTableComment($table),
                'type' => $type,
                'name' => $column->name,
                'comment' => $column->comment,
            ];
        }

        return true;
    }

    protected function getLabelsInfo($model)
    {
        $model = preg_replace('/\.[^.]+$/', '', $model);
        $model = 'common\models\\' . $model;
        $table = is_callable([$model, 'tableName']) ? $model::tableName() : false;
        $tableSchema = $this->db->getTableSchema($table);

        if ($tableSchema) {
            $model = new $model;
            $attr = is_callable([$model, 'attributeLabels']) ? $model->attributeLabels() : false;

            foreach ($attr as $name => $comment) {

                $columnPhpType = isset($tableSchema->columns[$name]) ? $tableSchema->columns[$name]->phpType : null;
                if ($columnPhpType === 'integer') {
                    $type = 'int';
                } elseif ($columnPhpType === 'boolean') {
                    $type = 'bool';
                } else {
                    $type = $columnPhpType;
                }
                $this->data[] = [
                    'table_name' => $table,
                    'table_comment' => $this->getTableComment($table),
                    'type' => $type,
                    'name' => $name,
                    'comment' => $comment,
                ];
            }
        }

        return true;
    }

    /**
     * @param $table
     * @return mixed
     * @throws \yii\db\Exception
     */
    protected function getTableComment($table)
    {
        return \Yii::$app->db->createCommand('select obj_description(\'' . $table . '\'::regclass)')->queryScalar() ?: $table;
    }

    /**
     * @param $columns
     * @param $data
     * @param $filePath
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    protected function getArray2Excel($columns, $data, $filePath)
    {
        ini_set('memory_limit', '4096M');
        $spreadsheet = new Spreadsheet();
        $cc = range('A', 'Z');

        foreach (array_keys($columns) as $k => $name) {
            $spreadsheet->getActiveSheet()->setCellValue($cc[$k] . '1', $columns[$name]);
        }
        $rowIndex = 2;
        foreach ($data as $i => $value) {
            foreach (array_keys($columns) as $k => $name) {
                isset($value[$columns[$name]]) ? $spreadsheet->getActiveSheet()->setCellValueExplicit($cc[$k] . $rowIndex, $value[$columns[$name]], DataType::TYPE_STRING) : null;
            }
            $rowIndex++;
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);
    }
}
