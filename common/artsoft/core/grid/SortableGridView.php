<?php
namespace artsoft\grid;

/**
 * 
 * SortableGridView extends \himiklab\sortablegrid\SortableGridView
 * and add yeesoft\grid\GridBulkActions
 * 
 */

use artsoft\grid\GridBulkActions;

class SortableGridView extends \himiklab\sortablegrid\SortableGridView 
{
    public $bulkActions;
    public $bulkActionOptions = [];
    public $filterPosition = self::FILTER_POS_HEADER;
    public $pager = [
        'options' => ['class' => 'pagination pagination-sm'],
        'hideOnSinglePage' => true,
        'firstPageLabel' => '<<',
        'prevPageLabel' => '<',
        'nextPageLabel' => '>',
        'lastPageLabel' => '>>',
    ];
    public $tableOptions = ['class' => 'table table-striped'];
    public $layout = '<div class="table-responsive">{items}</div>'
                    . '<div class="row">'
                    . '<div class="col-xs-4 col-md-3">{bulkActions}</div>'
                    . '<div class="col-xs-8 col-md-9 text-right">{summary}</div>'
                    . '</div>'
                    . '<div class="row">'
                    . '<div class="col-xs-12 text-center">{pager}</div>'
                    . '</div>';

    public function renderSection($name)
    {
        switch ($name) {
            case '{bulkActions}':
                return $this->renderBulkActions();
            default:
                return parent::renderSection($name);
        }
    }

    public function renderBulkActions()
    {
        if (!$this->bulkActions) {
            $this->bulkActions = GridBulkActions::widget($this->bulkActionOptions);
        }
        return $this->bulkActions;
    }
}
