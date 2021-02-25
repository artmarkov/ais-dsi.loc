<?php

use artsoft\components\languageSelector\items\DropDownLanguageItem;
use artsoft\widgets\assets\LanguageSelectorAsset;
use yii\bootstrap\Nav;

LanguageSelectorAsset::register($this);

$languageItem = new DropDownLanguageItem([
    'languages' => $item,
    'options' => ['encode' => false],
        ]);

echo Nav::widget([
    'options' => $options,
    'items' => [
        $languageItem->toArray(),
    ],
]);
