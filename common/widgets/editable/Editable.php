<?php

namespace common\widgets\editable;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use kartik\popover\PopoverX;

class Editable extends \kartik\editable\Editable
{

    public $buttonsTemplate = "{reset}{submit}{remove}";

    public $defaultRemoveBtnIcon;

    public $submitButton = ['class' => 'btn btn-sm btn-primary'];

    public $resetButton = ['class' => 'btn btn-sm btn-default'];

    public $removeButton = ['class' => 'btn btn-sm btn-danger'];

    public $preHeader = '';

    protected static $_icons = [
        'defaultEditableBtnIcon' => ['pencil', 'pencil-alt'],
        'defaultSubmitBtnIcon' => ['ok', 'check'],
        'defaultResetBtnIcon' => ['ban-circle', 'ban'],
        'defaultRemoveBtnIcon' => ['remove', 'remove'],
        'defaultPreHeaderIcon' => ['edit', 'edit'],
    ];

    public $showButtonLabels = true;

    public $size = PopoverX::SIZE_MEDIUM;

    public $placement = PopoverX::ALIGN_AUTO;

    public $dataAttributes = [];
    /**
     * Generates the editable action buttons
     *
     * @return string
     */
    protected function renderActionButtons()
    {
        $submitOpts = $this->submitButton;
        $resetOpts = $this->resetButton;
        $removeOpts = $this->removeButton;
        $submitIcon = ArrayHelper::remove($submitOpts, 'icon', $this->defaultSubmitBtnIcon);
        $resetIcon = ArrayHelper::remove($resetOpts, 'icon', $this->defaultResetBtnIcon);
        $removeIcon = ArrayHelper::remove($removeOpts, 'icon', $this->defaultRemoveBtnIcon);
        $submitLabel = ArrayHelper::remove($submitOpts, 'label', Yii::t('kveditable', 'Apply'));
        $resetLabel = ArrayHelper::remove($resetOpts, 'label', Yii::t('kveditable', 'Reset'));
        $removeLabel = ArrayHelper::remove($removeOpts, 'label', Yii::t('art', 'Delete'));
        if ($this->showButtonLabels === false) {
            if (empty($submitOpts['title'])) {
                $submitOpts['title'] = $submitLabel;
            }
            if (empty($resetOpts['title'])) {
                $resetOpts['title'] = $resetLabel;
            }
            if (empty($removeOpts['title'])) {
                $removeOpts['title'] = $removeLabel;
            }
            $submitLabel = $submitIcon;
            $resetLabel = $resetIcon;
            $removeLabel = $removeIcon;
        } else {
            $submitLabel = $submitIcon . ' ' . Html::encode($submitLabel);
            $resetLabel = $resetIcon . ' ' . Html::encode($resetLabel);
            $removeLabel = $removeIcon . ' ' . Html::encode($removeLabel);
        }
        $submitOpts['type'] = 'button';
        $resetOpts['type'] = 'button';
        $removeOpts['type'] = 'button';
       // $removeOpts['data'] = Html::renderTagAttributes($this->dataAttributes);
        Html::addCssClass($submitOpts, 'kv-editable-submit');
        Html::addCssClass($resetOpts, 'kv-editable-reset');
        Html::addCssClass($removeOpts, 'kv-editable-remove');
        $params = [
            '{reset}' => Html::button($resetLabel, $resetOpts),
            '{submit}' => Html::button($submitLabel, $submitOpts),
            '{remove}' => Html::button($removeLabel, $removeOpts),
        ];
        return strtr($this->buttonsTemplate, $params);
    }

}
