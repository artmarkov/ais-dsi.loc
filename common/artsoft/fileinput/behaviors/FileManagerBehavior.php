<?php

namespace artsoft\fileinput\behaviors;

use artsoft\fileinput\models\FileManager;
use yii\helpers\ArrayHelper;
use yii\db\ActiveRecord;

/**
 * Class FileManagerBehavior
 * @package artsoft\fileinput\behaviors
 *
 * Usage:
 * In your model, add the behavior and configure it:
 * owner_id - primary key owner your model (default - id)
 * form_name - Model name (if there are inheritances)
 *
 * public function behaviors()
 * {
 *     return [
 *             'fileManager' => [
 *                  'class' => \artsoft\fileinput\behaviors\FileManagerBehavior::class,
 *               // 'owner_id' => 'id',
 *               // 'form_name' => 'ModelName',
 *             ],
 *     ];
 * }
 */
class FileManagerBehavior extends \yii\base\Behavior
{

    public $owner_id = 'id';
    public $form_name;

    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_DELETE => 'beforeDelete'
        ];
    }

    public function getFiles()
    {
        return $this->owner->hasMany(FileManager::class, ['item_id' => $this->owner_id])
            ->onCondition(['class' => $this->form_name ?? $this->owner->formName()])
            ->orderBy('sort');
    }

    public function getFilesLinks()
    {
        return ArrayHelper::getColumn($this->owner->files, 'fileUrl');
    }

    public function getFilesLinksData()
    {
        return ArrayHelper::toArray($this->owner->files, [
                FileManager::class => [
                    'type' => 'type',
                    'filetype' => 'filetype',
                    'downloadUrl' => 'fileUrl',
                    'caption' => 'name',
                    'size' => 'size',
                    'key' => 'id',
                    'frameAttr' => [
                        'title' => 'orig_name',
                    ]
                ]]
        );
    }

    public function getFilesCount()
    {
        return count($this->getFileColumn());
    }

    public function getFileColumn()
    {
        return ArrayHelper::getColumn($this->owner->files, 'id');
    }

    public function beforeDelete()
    {
        foreach ($this->getFileColumn() as $id) {
            FileManager::findOne($id)->delete();
        }
    }

}
