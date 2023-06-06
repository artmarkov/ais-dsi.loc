<?php

namespace artsoft\traits;

/**
 * ParamsTrimable trait
 */
trait ParamsTrimable
{
    public function trimParams($params, $modelClass)
    {
        $modelClass = basename(str_replace('\\', '/', $modelClass));
        if (isset($params[$modelClass])) {
            $params[$modelClass] = array_filter($params[$modelClass], function ($value) {
                return ($value !== '');
            });
        }

        return $params;
    }
}
