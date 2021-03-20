<?php

namespace artsoft\widgets;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\LinkPager;

class JumpPager extends LinkPager
{

    public $maxButtonCount = 1;
    public $hideOnSinglePage = true;
    public $firstPageLabel = '<i class="glyphicon glyphicon-chevron-left"></i><i style="margin-left: -8px" class="glyphicon glyphicon-chevron-left"></i>';
    public $prevPageLabel = '<i class="glyphicon glyphicon-chevron-left"></i>';
    public $nextPageLabel = '<i class="glyphicon glyphicon-chevron-right"></i>';
    public $lastPageLabel = '<i class="glyphicon glyphicon-chevron-right"></i><i style="margin-left: -8px" class="glyphicon glyphicon-chevron-right"></i>';

    public $type = 'select';

    public function init()
    {
        parent::init();
    }

    public function run()
    {
        parent::run();
    }

    protected function renderPageButton($label, $page, $class, $disabled, $active)
    {

        $options = $this->linkContainerOptions;
        $linkWrapTag = ArrayHelper::remove($options, 'tag', 'li');

        if ($active) {
            $linkOptions = $this->linkOptions;
            $linkOptions['data-page'] = $page;
            $query = Yii::$app->request->queryString;
            $query = explode('&', $query);
            if ($query) foreach ($query as $k => $v) {
                $arr = explode('=', $v);
                if (isset($arr[0]) && $arr[0] == $this->pagination->pageParam) unset($query[$k]);
            }
            $query = implode('&', $query);

            switch ($this->type) {
                case 'input' :
                    $form = Html::input('number', $this->pagination->pageParam, $page + 1, ['class' => 'form-control jumpager', 'min' => 1, 'data' => ['query' => $query]]);
                    $form .= Html::tag('span', Html::tag('i', '', ['class' => 'glyphicon glyphicon-random']), ['class' => 'btn input-group-addon jumpager', 'data' => ['query' => $query]]);
                    $jumper = Html::tag('div', $form, ['class' => 'input-group jumpager']);
                    break;
                default :
                    $pages = [];
                    $endPage = $this->pagination->getPageCount();
                    foreach (range(1, $endPage) as $v) $pages[$v] = $v;
                    $jumper = Html::dropDownList($this->pagination->pageParam, $page + 1, $pages, ['class' => 'form-control input-sm jumpager', 'data' => ['query' => $query]]);
            }


            $this->registerJSCss();
            return Html::tag($linkWrapTag, $jumper, $options);
        }

        Html::addCssClass($options, empty($class) ? $this->pageCssClass : $class);

        if ($active) {

            Html::addCssClass($options, $this->activePageCssClass);
        }
        if ($disabled) {
            Html::addCssClass($options, $this->disabledPageCssClass);
            $disabledItemOptions = $this->disabledListItemSubTagOptions;
            $tag = ArrayHelper::remove($disabledItemOptions, 'tag', 'span');

            return Html::tag($linkWrapTag, Html::tag($tag, $label, $disabledItemOptions), $options);
        }
        $linkOptions = $this->linkOptions;
        $linkOptions['data-page'] = $page;

        return Html::tag($linkWrapTag, Html::a($label, $this->pagination->createUrl($page), $linkOptions), $options);
    }

    protected function registerJSCss()
    {
        $view = $this->getView();
        $js = "$(function(){
            $('input.jumpager').keypress(function(e) {
                if(e.which == 13) {
                    $(this).blur();
                    var input = $(this);
                    if(input.val()<0) input.val(1);
                    var page = '&'+input.attr('name')+'='+input.val();
                    var href = window.location.pathname+'?'+$(this).data('query')+page+window.location.hash;
                    window.location = href;
                }
            });
            $('span.jumpager').click(function(){
               var input = $(this).parent().find('input');
               var page = '&'+input.attr('name')+'='+input.val();
               var href = window.location.pathname+'?'+$(this).data('query')+page+window.location.hash;
               window.location = href;
           });
           $('select.jumpager').on('change',function(){
               var input = $(this);
               var page = '&'+input.attr('name')+'='+input.val();
               var href = window.location.pathname+'?'+$(this).data('query')+page+window.location.hash;
               window.location = href;
           });
       });";
        $view->registerJs($js);
        $css = "
            .input-group.jumpager {
                width: 60px;
                display: inline-table;
                float: left;
                margin-left: -1px;
            }
            .input-group.jumpager input.jumpager {
                width: 60px;
                text-align: center;
                border-radius: 0;
                border-right: none;
                padding-left : 2px;
                padding-right : 2px;
            }
            .input-group.jumpager span.jumpager {
                border-radius: 0;
                border-left: none;
            }
            select.jumpager {
                width: 80px;
                display: inline;
                float: left;
                border-radius: 0;
                margin-left: -1px;
                border: 1px solid #ddd;
                text-align: center;
            }
        ";
        $view->registerCss($css);
    }

}