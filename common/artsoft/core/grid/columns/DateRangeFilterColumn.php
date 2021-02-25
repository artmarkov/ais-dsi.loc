<?php

namespace artsoft\grid\columns;

use yii\grid\DataColumn;
use yii\helpers\Html;
use kartik\date\DatePicker;

class DateRangeFilterColumn extends DataColumn {

    /**
     * @var string
     */
    public $attribute2;

    /**
     * @var array
     */
    public $contentOptions = ['style' => 'text-align:center; vertical-align: middle;'];

    /**
     * @var array
     */
    public $headerOptions = ['style' => 'text-align:center;'];

    /**
     *
     * @var array 
     */
    public $filterInputOptions = ['class' => 'form-control', 'id' => null, 'autocomplete' => 'off'];

    /**
     * Renders the filter cell content.
     * The default implementation simply renders a space.
     * This method may be overridden to customize the rendering of the filter cell (if any).
     * @return string the rendering result
     */
    protected function renderFilterCellContent()
    {
        if (is_string($this->filter))
        {
            return $this->filter;
        }

        $model = $this->grid->filterModel;

        if ($this->filter !== false && $this->attribute !== null && $model->isAttributeActive($this->attribute) 
                && $this->attribute2 !== null && $model->isAttributeActive($this->attribute2))
        {
            if ($model->hasErrors($this->attribute) || $model->hasErrors($this->attribute2))
            {
                Html::addCssClass($this->filterOptions, 'has-error');
                $error = ' ' . Html::error($model, $this->attribute, $this->grid->filterErrorOptions) . 
                         ' ' . Html::error($model, $this->attribute2, $this->grid->filterErrorOptions);
            }
            else
            {
                $error = '';
            }

            $field = DatePicker::widget(['model' => $model,
                'type' => DatePicker::TYPE_RANGE,
                'convertFormat' => true,
                'attribute' => $this->attribute,
                'attribute2' => $this->attribute2,
                'options' => $this->filterInputOptions,
                'options2' => $this->filterInputOptions,
                'separator' => '-',
                'pluginOptions' => [
                    'format' => 'dd.MM.yyyy',
                    'autoclose' => true,
                    'weekStart' => 1,
                    'startDate' => '01.01.1930',
                    'endDate' => '01.01.2030',
                    'todayBtn' => 'linked',
                    'todayHighlight' => true,
                ]
            ]);

            return $field . $error;
        }
        else
        {
            return parent::renderFilterCellContent();
        }
    }

}
