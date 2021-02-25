<?php

namespace artsoft\queue\jobs;

/**
 * Class TestJob.
 */
class TestJob extends \yii\base\BaseObject implements \yii\queue\JobInterface
{
    public $text;

    public $file;

    /**
     * @inheritdoc
     */
    public function execute($queue)
    {
        file_put_contents($this->file, $this->text, FILE_APPEND); 
    }
}
