<?php

namespace console\controllers;

use Box\Spout\Common\Entity\Row;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use yii\console\Controller;
use yii\helpers\Console;

/**
 * Description of ObjectController
 *
 * run  console command:  php yii import
 *
 * @author markov-av
 */
class ImportController extends Controller
{
    /**
     * @throws \Box\Spout\Common\Exception\IOException
     * @throws \Box\Spout\Reader\Exception\ReaderNotOpenedException
     * @throws \yii\db\Exception
     */
    public function actionIndex()
    {
        $reader = ReaderEntityFactory::createXLSXReader();
        $reader->open('data/teachers.xlsx');
        $this->stdout("\n");
        foreach ($reader->getSheetIterator() as $k => $sheet) {
            if (1 != $k) {
                continue;
            }
            foreach ($sheet->getRowIterator() as $i => $row) {
//                if ($i == 1) {
//                    continue; // skip header
//                }

                /* @var $row Row */
                $v = $row->toArray();
                print_r($v);
            }
        }
        $this->stdout("\n");
    }

}
