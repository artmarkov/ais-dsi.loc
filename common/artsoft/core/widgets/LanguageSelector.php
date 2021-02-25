<?php

namespace artsoft\widgets;

use Yii;
use asinfotrack\yii2\flagicons\Flag;
use yii\helpers\Html;

/**
 *  echo artsoft\widgets\LanguageSelector::widget([
 *       'display' => 'label', 
 *       'flag_visible' => true, 
 *       'options' => ['class' => 'navbar-nav navbar-left'],
 *       'code_redirect' => ['en-US' => 'us'],
 *   ]); 
 */

class LanguageSelector extends \yii\base\Widget
{
    /**
     *
     * @var string  links | dropdown
     */
    public $view = 'dropdown';
    
    /**
     *
     * @var string  code | label
     */
    public $display = 'code';

    /**
     * Allows viewing flag icon
     * 
     * @var boolean 
     */
    public $flag_visible = true;
    
    /**
     *
     * @var type array
     */
    public $code_redirect = ['en-US' => 'gb'];
    
    /**
     *
     * @var type array
     */
    public $options = ['class' => 'navbar-nav navbar-right']; // navbar-nav navbar-right nav
    
    /**
     * Returns language shortcode from its redirect.
     * 
     * @param string $language
     * @return string
     */
    private function getCountryCode($language) {
        
        if (!isset($this->code_redirect)) {
            return $language;
        }

        return (isset($this->code_redirect[$language])) ? $this->code_redirect[$language] : $language;
    }

    public function run()
    {
        if (!Yii::$app->art->isMultilingual) {
            return;
        }
       
        foreach (Yii::$app->art->languages as $key => $label) :

            $item[$key] = $this->flag_visible ? Flag::icon($this->getCountryCode($key)) : NULL;
            $item[$key] .= Html::tag('span', ($this->display == 'code') ? $key : $label);

        endforeach;

        return $this->render($this->view, [
            'item' => $item,
            'options' => $this->options,
        ]);
    }
}