<?php

namespace artsoft\dbmanager;

use Yii;
use yii\helpers\StringHelper;
use yii\base\InvalidConfigException;
/**
 * db module definition class
 */
class DbmanagerModule extends \yii\base\Module
{
     /**
     * Path for backup directory.
     *
     * @var string $dumpPath
     */
    public $dumpPath = '@frontend/web/db/';
    
    /**
     * @var array
     */
    protected $files = [];
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'artsoft\dbmanager\controllers';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
         $this->dumpPath = Yii::getAlias($this->dumpPath);
        if (!StringHelper::endsWith($this->dumpPath, '/', false)) {
            $this->dumpPath .= '/';
        }
         if (!file_exists($this->dumpPath)) {
            mkdir($this->dumpPath);
            chmod($this->dumpPath, 0755);
        }
        if (!is_dir($this->dumpPath)) {
            throw new InvalidConfigException('Path is not directory');
        }
        if (!is_writable($this->dumpPath)) {
            throw new InvalidConfigException('Path is not writable! Check chmod!');
        }
        parent::init();

        // custom initialization code goes here
    }
}
