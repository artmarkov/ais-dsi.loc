<?php

namespace api\modules\v1\models;

use yii\helpers\Url;
use yii\web\Linkable;
use api\common\models\User;

/**
 * This is the model class for table "{{%post}}".
 *
 * @property integer $id
 * @property string $title
 * @property string $slug
 * @property string $content
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $created_by
 *
 * @property User $user
 */
class Post extends \artsoft\post\models\Post implements Linkable
{ 
    /**
     * 
     * @return type
     */
    public function fields()
    {
        return [
            'id' => 'id',
            'slug' => 'slug',
            'title' => 'title',
            'content' => 'content',            
            'created_at' => 'created_at',            
            'updated_at' => 'updated_at',            
            'created_by' => 'created_by',            
        ];
    }
    
    public function extraFields()
    {
        return [
            'translation' => 'translation',
            'author' => 'author',
            'category' => 'category',
            'tags' => 'tags',
        ];
    }

    public function getLinks()
    {
        return [
            'self' => Url::to(['post/view', 'id' => $this->id], true),
        ];
    }
    
    /**
     *
     * @inheritdoc
     */
    public static function getFullAccessPermission()
    {
        return 'fullApiPostAccess';
    }
}
