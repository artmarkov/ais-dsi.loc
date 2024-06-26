<?php

namespace artsoft\fileinput\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use artsoft\models\OwnerAccess;
use artsoft\db\ActiveRecord;
use artsoft\models\User;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "file_manager".
 *
 * @property int $id
 * @property string $orig_name
 * @property string $name
 * @property string $class
 * @property int $item_id
 * @property int $sort
 * @property string $alt
 * @property string $type
 * @property string $filetype
 * @property string $size
 * @property int $created_at
 * @property int $created_by
 */

class FileManager extends ActiveRecord implements OwnerAccess {

    /**
     * array const
     */
    const TYPE = [
            'txt'  => ['type' => 'text'],
            'doc'  => ['type' => 'other'],
            'docx' => ['type' => 'other'],
            'xls'  => ['type' => 'other'],
            'xlsx' => ['type' => 'other'],
            'ppt'  => ['type' => 'other'],
            'pptx' => ['type' => 'other'],
            'zip'  => ['type' => 'other'],
            'rar'  => ['type' => 'other'],
            'pdf'  => ['type' => 'pdf'],
            'jpg'  => ['type' => 'image'],
            'png'  => ['type' => 'image', 'filetype' => 'image/png'],
            'mp4'  => ['type' => 'video', 'filetype' => 'video/mp4'],
            'mp3'  => ['type' => 'audio', 'filetype' => 'audio/mp3'],
        ];

      /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [         
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => NULL,
            ], 
            'blameable' => [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'created_by',
                'updatedByAttribute' => NULL,
            ],
        ];
    }
    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'file_manager';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['orig_name', 'name', 'type'], 'required'],
            [['created_at'], 'safe'],
            [['item_id', 'sort', 'size'], 'integer'],
            ['sort', 'default', 'value' => function($model) {
                $count = FileManager::find()->andWhere(['class' => $model->class, 'item_id' => $model->item_id])->count();
                return ($count > 0) ? $count++ : 0;
            }],
            [['type', 'filetype'], 'safe'],
            [['orig_name', 'name', 'class', 'alt'], 'string', 'max' => 256],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
        ];
    }
    
     /**
     * 
     * @param type model $file
     * @return model
     */
     public static function getFileAttribute($file) {
         
        $model = new FileManager();
        $name = $file->name;
       // $model->name = ArtHelper::slug($file->name) . '_' . strtotime('now') . '.' . $file->extension;
        $model->name = strtotime('now') . '_' . Yii::$app->getSecurity()->generateRandomString(6) . '.' . $file->extension;
        $model->orig_name = $name;
        $model->alt = '';
        $model->type = ArrayHelper::getValue(self::TYPE, $file->extension . '.type') ? ArrayHelper::getValue(self::TYPE, $file->extension . '.type') : 'image';
        $model->filetype = ArrayHelper::getValue(self::TYPE, $file->extension . '.filetype');
        $model->size = $file->size;

        return $model;
    }

     /**
     * 
     * @return boolean
     */
    public function beforeDelete() {
        if (parent::beforeDelete()) {
            FileManager::updateAllCounters(['sort' => -1], [
                'and', ['class' => $this->class, 'item_id' => $this->item_id], ['>', 'sort', $this->sort]
            ]);
            
            //удаляем физически, если нигде больше не используется          
            if ($this::getCountFileForName() == 1 && file_exists($this->getRoutes())) {
                @unlink($this->getRoutes());
            }

            return true;
        } else {
            return false;
        }
    }

    /**
     * 
     * @return type int
     */
    public function getCountFileForName() {
      return $this->find()->andWhere(['name' => $this->name])->count();
    }

    /**
     * 
     * @param type $class
     * @return type string
     */
    public static function getFolder($class){
        return strtolower($class);
    } 
    /**
     * 
     * @return type string
     */
    public static function getAbsoluteDir(){
        return Yii::$app->getModule('fileinput')->absolutePath;
    } 
    /**
     * 
     * @return type string
     */
    public static function getUploadDir(){
        return Yii::$app->getModule('fileinput')->uploadPath;
    }
    /**
     * 
     * @return type string
     */
    public function getRoutes(){
        return "{$this::getAbsoluteDir()}/{$this::getFolder($this->class)}/{$this->name}";
    }
    
    /**
     * 
     * @return string
     */
    public function getFileUrl() {

        $uploadDir = Url::to('/', 'https') . $this->getUploadDir();

        if ($this->name && file_exists($this->getRoutes())) {
            $path = "{$uploadDir}/{$this::getFolder($this->class)}/{$this->name}";
        } else {
            $path = "{$uploadDir}/_errors/nofile.png";
        }
        return $path;
    }
    /**
     *
     * @inheritdoc
     */
    public static function getOwnerField()
    {
        return 'created_by';
    }
    /**
     *
     * @inheritdoc
     */
    public static function getFullAccessPermission()
    {
        return 'fullFileinputAccess';
    }

}
