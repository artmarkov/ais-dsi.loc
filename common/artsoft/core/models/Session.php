<?php

namespace artsoft\models;

use artsoft\helpers\SessionDecoder;
use Yii;

/**
 * This is the model class for table "session".
 *
 * @property string $id
 * @property integer $run_at
 * @property integer $expire
 * @property integer $user_id
 * @property string $user_ip
 * @property resource $data
 */
class Session extends \artsoft\db\ActiveRecord
{
    public $status;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'session';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['expire', 'user_id'], 'integer'],
            [['data'], 'safe'],
            [['status', 'user_ip', 'run_at'], 'string'],
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
            'run_at' => Yii::t('art', 'Run At'),
            'last_attempt' => Yii::t('art/user', 'Last Attempt'),
        ];
    }

    public function getStatus()
    {
        $user = isset($this->user_id) ? User::findOne($this->user_id) : null;
        $expired = time() - $this->expire > 0 || $user === null;
        $current = $this->user_id == Yii::$app->session->getId();
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
        $user = isset($this->user_id) ? User::findOne($this->user_id) : null;

        return $user ? $user->username : '';
    }

    public function getIp()
    {
        return isset($this->user_ip) ? $this->user_ip : null;
    }

    public function getRunAt()
    {
        return isset($this->run_at) ? $this->run_at : null;
    }

}
