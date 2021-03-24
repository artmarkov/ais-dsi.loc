<?php

namespace artsoft\grid;
use artsoft\widgets\JumpPager as LinkPager;
use yii\helpers\ArrayHelper;

class GridView extends \yii\grid\GridView
{
    public $bulkActions;
    public $bulkActionOptions = [];
    public $filterPosition = self::FILTER_POS_HEADER;
    public $pager = [
        'maxButtonCount' => 5,
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
            $this->bulkActions = $this->bulkActionOptions ? GridBulkActions::widget($this->bulkActionOptions) : null;
        }
        return $this->bulkActions;
    }

    /**
     * Renders the pager.
     * @return string the rendering result
     */
    public function renderPager()
    {
        $pagination = $this->dataProvider->getPagination();
        if ($pagination === false || $this->dataProvider->getCount() <= 0) {
            return '';
        }
        /* @var $class LinkPager */
        $pager = $this->pager;
        $class = ArrayHelper::remove($pager, 'class', LinkPager::className());
        $pager['pagination'] = $pagination;
        $pager['view'] = $this->getView();

        return $class::widget($pager);
    }
}
