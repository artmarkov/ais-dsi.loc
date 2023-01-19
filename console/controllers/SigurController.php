<?php

namespace console\controllers;

use Box\Spout\Common\Entity\Row;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use Yii;
use yii\console\Controller;
use yii\helpers\Console;

/**
 * Description of ObjectController
 *
 * Driver={PostgreSQL UNICODE};Server=192.168.0.205;Port=5432;Database=ais_dsi_test;UID=aisadmin;PWD=6Nr4fmmM;

   select * from users_card_view
   insert into users_card(user_common_id, key_hex) VALUES (%ID%, '%KEY_HEX%');
   insert into users_card_log(user_common_id, key_hex, datetime, deny_reason, dir_code, dir_name, evtype_code, evtype_name, name, position) VALUES (%ID%, '%KEY_HEX%', '%DATETIME%', '%DENY_REASON%', %DIR_CODE%, '%DIR_NAME%', %EVTYPE_CODE%, '%EVTYPE_NAME%', '%NAME%', '%POSITION%');
   select * from users_card_view where user_common_id = %ID%;
 *
 * run  console command:  php yii sigur
 *
 * @author markov-av
 */
class SigurController extends Controller
{
    public function actionIndex()
    {
        $reader = ReaderEntityFactory::createXLSXReader();
        $reader->open('data/sigur/tc-tmp5884886171455644337.xlsx');

        foreach ($reader->getSheetIterator() as $k => $sheet) {
            if (1 != $k) {
                continue;
            }
            foreach ($sheet->getRowIterator() as $i => $row) {
                if ($i < 4) {
                    continue; // skip header
                }
                /* @var $row Row */
                $v = $row->toArray();
                $name = explode(' ', $v['1']);
                $last_name = $this->getUcFirst(isset($name[0]) ? $name[0] : '');
                $first_name = $this->getUcFirst(isset($name[1]) ? $name[1] : '');
                $middle_name = $this->getUcFirst(isset($name[2]) ? $name[2] : '');

                $model = $this->findUser($last_name, $first_name, $middle_name);
                if ($model) {
                    \Yii::$app->db->createCommand('INSERT INTO users_card (user_common_id, key_hex, timestamp_deny, photo_bin, created_at, created_by, updated_at, updated_by) 
                                                        VALUES (:user_common_id, :key_hex, :timestamp_deny, :photo_bin, :created_at, :created_by, :updated_at, :updated_by)',
                        [
                            'user_common_id' => $model['id'],
                            'key_hex' => trim(str_replace(',','', $v[6])),
                            'timestamp_deny' => $v[8] != 'не ограничен' ? \Yii::$app->formatter->asDate($v[8], 'php:Y-m-d H:i:s') : null,
                            'photo_bin' => $model['id'] == 1020 ? base64_encode(file_get_contents(Yii::getAlias('data/sigur/test.jpg'))) : null,
                            'created_at' => \Yii::$app->formatter->asTimestamp($v[7]),
                            'created_by' => 1000,
                            'updated_at' => \Yii::$app->formatter->asTimestamp($v[7]),
                            'updated_by' => 1000,
                        ])->execute();

                    $this->stdout('Добавлен пропуск для записи user_common_id: ' . $model['id'], Console::FG_GREY);
                    $this->stdout("\n");
                }
            }
        }
    }

    protected function getUcFirst($str, $encoding = 'UTF-8')
    {
        return mb_convert_case($str, MB_CASE_TITLE, $encoding);
    }

    public function findUser($last_name, $first_name, $middle_name)
    {
        $user = \Yii::$app->db->createCommand('SELECT * 
                                                    FROM user_common 
                                                    WHERE last_name=:last_name 
                                                    AND first_name=:first_name 
                                                    AND middle_name=:middle_name',
            [
                'last_name' => $this->lat2cyr($last_name),
                'first_name' => $this->lat2cyr($first_name),
                'middle_name' => $this->lat2cyr($middle_name)
            ])->queryOne();
        return $user ?: false;
    }

    protected function lat2cyr($text) {
        $arr = array(
            'A' => 'А',
            'a' => 'а',
            'B' => 'В',
            'C' => 'С',
            'c' => 'с',
            'E' => 'Е',
            'e' => 'е',
            'H' => 'Н',
            'K' => 'К',
            'k' => 'к',
            'M' => 'М',
            'm' => 'м',
            'n' => 'п',
            'O' => 'О',
            'o' => 'о',
            'P' => 'Р',
            'p' => 'р',
            'T' => 'Т',
            'X' => 'Х',
            'x' =>'х'
        );
        return strtr($text, $arr);
    }
}
