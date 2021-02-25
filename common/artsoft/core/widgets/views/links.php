<?php

use artsoft\components\languageSelector\items\MenuLanguageItems;
use artsoft\widgets\assets\LanguageSelectorAsset;
use yii\widgets\Menu;

LanguageSelectorAsset::register($this);

$languageItem = new MenuLanguageItems([
    'languages' => $item,
    'options' => ['encode' => false],
        ]);

echo Menu::widget([
    'options' => $options,
    'items' => $languageItem->toArray(),
]);
