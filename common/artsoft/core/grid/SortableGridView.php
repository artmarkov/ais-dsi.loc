<?php
namespace artsoft\grid;

/**
 * SortableGridView extends \himiklab\sortablegrid\SortableGridView
 */

use himiklab\sortablegrid\SortableGridAsset;
use yii\helpers\Url;

class SortableGridView extends GridView
{
    /** @var string|array Sort action */
    public $sortableAction = ['sort'];
    public $pjax = false;

    public function init()
    {
        parent::init();
        $this->sortableAction = Url::to($this->sortableAction);
    }

    public function run()
    {
        $this->registerWidget();
        parent::run();
    }

    protected function registerWidget()
    {
        $view = $this->getView();
        $view->registerJs("jQuery('#{$this->options['id']}').SortableGridView('{$this->sortableAction}');");
        SortableGridAsset::register($view);
    }
}
