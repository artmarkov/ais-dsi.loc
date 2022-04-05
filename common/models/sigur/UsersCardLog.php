<?php

namespace common\models\sigur;

use Yii;

/**
 * This is the model class for table "users_card_log".
 *
 * @property int $id
 * @property string|null $user_common_id
 * @property string|null $key_hex Пропуск (в формате HEX)
 * @property string|null $datetime Дата и время события в формате ГГГГ-ММ-ДД ЧЧ:ММ:СС
 * @property string|null $deny_reason Код причины запрета доступа
 * @property int|null $dir_code Код направления прохода (1=выход, 2=вход, 3=неизвестное).
 * @property string|null $dir_name Наименование направления прохода (OUT, IN, UNKNOWN)
 * @property int|null $evtype_code Тип события (1=проход, 2=запрет)
 * @property string|null $evtype_name Наименование типа события (PASS, DENY)
 * @property string|null $name Имя сотрудника
 * @property string|null $position Должность сотрудника
 */
class UsersCardLog extends \artsoft\db\ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'users_card_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['datetime'], 'safe'],
            [['dir_code', 'evtype_code'], 'default', 'value' => null],
            [['dir_code', 'evtype_code', 'isUserAis'], 'integer'],
            [['user_common_id'], 'string', 'max' => 4],
            [['key_hex'], 'string', 'max' => 8],
            [['deny_reason'], 'string', 'max' => 32],
            [['dir_name', 'evtype_name'], 'string', 'max' => 16],
            [['name', 'position'], 'string', 'max' => 127],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art/guide', 'ID'),
            'user_common_id' => Yii::t('art/guide', 'User Common ID'),
            'key_hex' => 'Код пропуска',
            'datetime' => 'Дата и время события',
            'deny_reason' => 'Код причины запрета доступа',
            'dir_code' => 'Направление',
            'dir_name' => 'Направление прохода',
            'evtype_code' => 'Тип события',
            'evtype_name' => 'Наименование типа события',
            'name' => 'ФИО посетителя',
            'position' => 'Должность',
        ];
    }
}
