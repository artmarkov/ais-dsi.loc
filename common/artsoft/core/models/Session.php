<?php

namespace artsoft\models;

use artsoft\helpers\SessionDecoder;
use Yii;

/**
 * This is the model class for table "session".
 *
 * @property string $id
 * @property integer $expire
 * @property resource $data
 */
class Session extends \artsoft\db\ActiveRecord
{
    public $status;
    public $ip;
    public $run_at;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%session}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['expire'], 'integer'],
            [['data','status','ip','run_at'], 'string'],
            [['id'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art', 'ID'),
            'expire' => Yii::t('art/user', 'Expire'),
            'status' => Yii::t('art', 'Status'),
            'last_attempt' => Yii::t('art/user', 'Last Attempt'),
        ];
    }

    public function getSessionVars()
    {
        return SessionDecoder::unserialize($this->getAttribute('data'));
    }

    public function getStatus()
    {
        $vars = $this->getSessionVars();
        $user = isset($vars['__id']) ? User::findOne($vars['__id']) : null;
        $expired = time() - $this->expire > 0 || $user === null;
        $current = $this->id == Yii::$app->session->getId();
        return $expired ? 'waiting' : ($current ? 'current' : 'active');
    }

    public function getStatusLabel($status)
    {
        switch ($status) {
            case 'waiting':
                $label = '<span class="label label-warning">' . Yii::t('art/user', 'Waiting') . '</span>';
                break;
            case 'current':
                $label = '<span class="label label-info">' . Yii::t('art/user', 'Current') . '</span>';
                break;
            case 'active':
                $label = '<span class="label label-success">' . Yii::t('art/user', 'Active') . '</span>';
                break;
            default:
                $label = '';
        }
        return $label;

    }

    public function getUsername()
    {
        $vars = $this->getSessionVars();
        $user = isset($vars['__id']) ? User::findOne($vars['__id']) : null;

        return $user ? $user->username : '';
    }

    public function getIP()
    {
        $vars = $this->getSessionVars();
        return isset($vars['__ipaddr']) ? $vars['__ipaddr'] : null;
    }
    public function getRunAt()
    {
        $vars = $this->getSessionVars();
        return isset($vars['__run_at']) ? $vars['__run_at'] : null;
    }

}
