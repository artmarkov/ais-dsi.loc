<?php

namespace common\widgets;

class SortableInput extends \kartik\sortinput\SortableInput
{
    protected function arrangeItems()
    {
        if (empty($this->value)) {
            return;
        }
        $keys = explode($this->delimiter, $this->value);
        if (!is_array($keys) || count($keys) == 0) {
            return;
        }
        $items = [];
        foreach ($keys as $key) {
            if(isset($this->items[$key])) {
                $items[$key] = $this->items[$key];
            }
        }
        $this->items = $items;
    }
}