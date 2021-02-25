<?php

namespace artsoft\web;

class ErrorAction extends \yii\web\ErrorAction
{
   
    protected function getViewRenderParams()
    {
        return [
            'name' => $this->getExceptionName(),
            'message' => $this->getExceptionMessage(),
            'code' => $this->getExceptionCode(),
            'exception' => $this->exception,
        ];
    }

    
}
