<?php
namespace artsoft\mailbox\widgets;

use Yii;
use yii\helpers\Html;

/**
 *  echo artsoft\widgets\PagerSelector::widget([
 *       'ulOptions' => ['class' => 'pager'],
 *       'link' => '/mailbox/default/view-sent',
 *       'next_id' => $next,
 *       'prev_id' => $prev,
 *   ]); 
 */

/**
 * Description of PagerSelector
 *
 * @author markov-av
 */
class PagerSelector extends \yii\base\Widget
{
     
    public $primaryKey = 'id';
    
    public $next_id;
    public $prev_id;
    
    public $path;
     
    public $ulOptions = ['class' => 'pager'];
    
    public $liOptionsNext = [];
    public $liOptionsPrev = [];
    
    public $next_icon = 'fa fa-chevron-right';
    public $prev_icon = 'fa fa-chevron-left';    
    
    public $next_options = ['data-toggle' => 'tooltip', 'data-container' => 'body', 'data-placement' => 'top', 'data-original-title' => 'Next'];
    public $prev_options = ['data-toggle' => 'tooltip', 'data-container' => 'body', 'data-placement' => 'top', 'data-original-title' => 'Previous'];


    private $rawPagerHtml;
    private $link_next;
    private $link_prev;    
    
    public function run() {
         $this->buildPager();
         
         return $this->getPagerHtml();
    }

    private function buildPager() {
        
       $this->link_next = Html::a('<i class="' . $this->next_icon . '"></i>', [$this->path, $this->primaryKey => $this->next_id], $this->next_options);
       $this->link_prev = Html::a('<i class="' . $this->prev_icon . '"></i>', [$this->path, $this->primaryKey => $this->prev_id], $this->prev_options);
       
        if (!isset($this->next_id)) {
            Html::addCssClass($this->liOptionsNext, 'disabled');
            $this->link_next = Html::a('<i class="' . $this->next_icon . '"></i>', null);
        }
        if (!isset($this->prev_id)) {
            Html::addCssClass($this->liOptionsPrev, 'disabled');
            $this->link_prev = Html::a('<i class="' . $this->prev_icon . '"></i>', null);
        }
        
        $this->rawPagerHtml = '';
        $this->rawPagerHtml .= Html::beginTag('ul', $this->ulOptions);
        $this->rawPagerHtml .= Html::beginTag('li', $this->liOptionsPrev);
        $this->rawPagerHtml .= $this->link_prev; 
        $this->rawPagerHtml .= Html::endTag('li');
        $this->rawPagerHtml .= Html::beginTag('li', $this->liOptionsNext);
        $this->rawPagerHtml .= $this->link_next; 
        $this->rawPagerHtml .= Html::endTag('li');
        $this->rawPagerHtml .= Html::endTag('ul');
    }

    public function getPagerHtml() {
        
        return $this->rawPagerHtml;
    }
}
