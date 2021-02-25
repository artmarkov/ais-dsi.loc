<?php

namespace artsoft\settings\models;

use artsoft\behaviors\MultilingualSettingsBehavior;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * @author Taras Makitra <makitrataras@gmail.com>
 */
class GeneralSettings extends BaseSettingsModel
{
    const GROUP = 'general';

    public $title;
    public $description;
    public $email;
    public $phone;
    public $timezone;
    public $dateformat;
    public $timeformat;
    public $facebook;
    public $instagram;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(),
            [
                [['title', 'email', 'timezone', 'dateformat', 'timeformat'], 'required'],
                [['email'], 'email'],
                [['description', 'phone', 'facebook', 'instagram'], 'safe'],
                ['title', 'default', 'value' => 'Art Site'],
                ['timezone', 'default', 'value' => 'Europe/London'],
                ['dateformat', 'default', 'value' => 'F j, Y'],
                ['timeformat', 'default', 'value' => 'g:i a'],
            ]);
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'multilingualSettings' => [
                'class' => MultilingualSettingsBehavior::className(),
                'attributes' => [
                    'title', 'description'
                ]
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'title' => Yii::t('art/settings', 'Site Title'),
            'description' => Yii::t('art/settings', 'Site Description'),
            'email' => Yii::t('art/settings', 'Admin Email'),
            'phone' => Yii::t('art/settings', 'Admin Phone'),
            'facebook' => Yii::t('art/settings', 'Admin Facebook'),
            'instagram' => Yii::t('art/settings', 'Admin Instagram'),
            'timezone' => Yii::t('art/settings', 'Timezone'),
            'dateformat' => Yii::t('art/settings', 'Date Format'),
            'timeformat' => Yii::t('art/settings', 'Time Format'),
        ];
    }

    public static function getDateFormats()
    {
        $timestamp = strtotime(date("Y") . '-01-22');
        return [
            'medium' => Yii::$app->formatter->asDate($timestamp, "medium"),
            'long' => Yii::$app->formatter->asDate($timestamp, "long"),
            'full' => Yii::$app->formatter->asDate($timestamp, "full"),
            'yyyy-MM-dd' => Yii::$app->formatter->asDate($timestamp, "yyyy-MM-dd"),
            'dd/MM/yyyy' => Yii::$app->formatter->asDate($timestamp, "dd/MM/yyyy"),
            'MM/dd/yyyy' => Yii::$app->formatter->asDate($timestamp, "MM/dd/yyyy"),
            'dd.MM.yyyy' => Yii::$app->formatter->asDate($timestamp, "dd.MM.yyyy"),
        ];
    }

    public static function getTimeFormats()
    {
        $timestamp = strtotime('2019-01-01 09:45:59');
        return [
            'h:mm a' => Yii::$app->formatter->asTime($timestamp, "h:mm a"),
            'hh:mm a' => Yii::$app->formatter->asTime($timestamp, "hh:mm a"),
            'HH:mm' => Yii::$app->formatter->asTime($timestamp, "HH:mm"),
            'H:mm' => Yii::$app->formatter->asTime($timestamp, "H:mm"),
        ];
    }

    public static function getTimezones()
    {
        return [
            "Pacific/Midway" => "(GMT-11:00) Midway Island, Samoa",
            "Etc/GMT+10" => "(GMT-10:00) Hawaii",
            "Pacific/Marquesas" => "(GMT-09:30) Marquesas Islands",
            "America/Anchorage" => "(GMT-09:00) Alaska",
            "America/Los_Angeles" => "(GMT-08:00) Pacific Time (US & Canada)",
            "America/Denver" => "(GMT-07:00) Mountain Time (US & Canada)",
            "America/Chihuahua" => "(GMT-07:00) Chihuahua, La Paz, Mazatlan",
            "America/Dawson_Creek" => "(GMT-07:00) Arizona",
            "America/Belize" => "(GMT-06:00) Saskatchewan, Central America",
            "America/Cancun" => "(GMT-06:00) Guadalajara, Mexico City, Monterrey",
            "Chile/EasterIsland" => "(GMT-06:00) Easter Island",
            "America/Chicago" => "(GMT-06:00) Central Time (US & Canada)",
            "America/New_York" => "(GMT-05:00) Eastern Time (US & Canada)",
            "America/Havana" => "(GMT-05:00) Cuba",
            "America/Bogota" => "(GMT-05:00) Bogota, Lima, Quito, Rio Branco",
            "America/Caracas" => "(GMT-04:30) Caracas",
            "America/Santiago" => "(GMT-04:00) Santiago",
            "America/La_Paz" => "(GMT-04:00) La Paz",
            "America/Campo_Grande" => "(GMT-04:00) Brazil",
            "America/Goose_Bay" => "(GMT-04:00) Atlantic Time (Goose Bay)",
            "America/Glace_Bay" => "(GMT-04:00) Atlantic Time (Canada)",
            "America/St_Johns" => "(GMT-03:30) Newfoundland",
            "America/Araguaina" => "(GMT-03:00) UTC-3",
            "America/Montevideo" => "(GMT-03:00) Montevideo",
            "America/Godthab" => "(GMT-03:00) Greenland",
            "America/Argentina/Buenos_Aires" => "(GMT-03:00) Buenos Aires",
            "America/Sao_Paulo" => "(GMT-03:00) Brasilia",
            "America/Noronha" => "(GMT-02:00) Mid-Atlantic",
            "Atlantic/Cape_Verde" => "(GMT-01:00) Cape Verde Is.",
            "Atlantic/Azores" => "(GMT-01:00) Azores",
            "Europe/London" => "(GMT) Greenwich Mean Time : London",
            "Africa/Abidjan" => "(GMT) Monrovia, Reykjavik",
            "Europe/Amsterdam" => "(GMT+01:00) Western & Central Europe",
            "Africa/Algiers" => "(GMT+01:00) West Central Africa",
            "Africa/Windhoek" => "(GMT+01:00) Windhoek",
            "Africa/Cairo" => "(GMT+02:00) Kiev, Cairo, Pretoria, Jerusalem",
            "Europe/Moscow" => "(GMT+03:00) Nairobi, Moscow",
            "Asia/Tehran" => "(GMT+03:30) Tehran",
            "Asia/Dubai" => "(GMT+04:00) Abu Dhabi, Muscat",
            "Asia/Yerevan" => "(GMT+04:00) Yerevan",
            "Asia/Kabul" => "(GMT+04:30) Kabul",
            "Asia/Tashkent" => "(GMT+05:00) Tashkent",
            "Asia/Kolkata" => "(GMT+05:30) Chennai, Kolkata, Mumbai, New Delhi",
            "Asia/Katmandu" => "(GMT+05:45) Kathmandu",
            "Asia/Dhaka" => "(GMT+06:00) Astana, Dhaka",
            "Asia/Novosibirsk" => "(GMT+06:00) Novosibirsk",
            "Asia/Rangoon" => "(GMT+06:30) Yangon (Rangoon)",
            "Asia/Bangkok" => "(GMT+07:00) Bangkok, Hanoi, Jakarta",
            "Asia/Hong_Kong" => "(GMT+08:00) Beijing, Hong Kong",
            "Asia/Irkutsk" => "(GMT+08:00) Irkutsk, Ulaan Bataar",
            "Australia/Eucla" => "(GMT+08:45) Eucla",
            "Asia/Tokyo" => "(GMT+09:00) Osaka, Sapporo, Tokyo",
            "Asia/Seoul" => "(GMT+09:00) Seoul",
            "Australia/Adelaide" => "(GMT+09:30) Adelaide",
            "Australia/Brisbane" => "(GMT+10:00) Brisbane",
            "Australia/Hobart" => "(GMT+10:00) Hobart",
            "Asia/Vladivostok" => "(GMT+10:00) Vladivostok",
            "Australia/Lord_Howe" => "(GMT+10:30) Lord Howe Island",
            "Etc/GMT-11" => "(GMT+11:00) Solomon Is., New Caledonia",
            "Pacific/Norfolk" => "(GMT+11:30) Norfolk Island",
            "Asia/Anadyr" => "(GMT+12:00) Anadyr, Kamchatka",
            "Pacific/Auckland" => "(GMT+12:00) Auckland, Wellington",
            "Etc/GMT-12" => "(GMT+12:00) Fiji, Kamchatka, Marshall Is.",
            "Pacific/Chatham" => "(GMT+12:45) Chatham Islands",
            "Pacific/Tongatapu" => "(GMT+13:00) Nuku'alofa",
            "Pacific/Kiritimati" => "(GMT+14:00) Kiritimati",];
    }
}