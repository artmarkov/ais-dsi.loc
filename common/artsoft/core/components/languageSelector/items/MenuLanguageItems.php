<?php

namespace artsoft\components\languageSelector\items;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class MenuLanguageItems is used for widgets, that implements the menu functionality.
 *
 *  "Author name": "Alexandr Cetvertacov",
 *  "source": "https://github.com/cetver/yii2-language-selector",
 *  "email": "cetver@gmail.com"
 * 
 * For example:
 *
 * ```php
 * $languageItems = new artsoft\LanguageSelector\items\MenuLanguageItems([
 *     'languages' => [
 *         'en' => '<span class="flag-icon flag-icon-us"></span> English',
 *         'ru' => '<span class="flag-icon flag-icon-ru"></span> Russian',
 *         'de' => '<span class="flag-icon flag-icon-de"></span> Deutsch',
 *     ],
 *     'options' => ['encode' => false],
 * ]);
 * echo \yii\widgets\Menu::widget([
 *     'options' => ['class' => 'list-inline'],
 *     'items' => $languageItems->toArray(),
 * ]);
 * ```
 *
 * @package artsoft\LanguageSelector\items
 * @property array $options the menu widget item options, excluding "label", "url" and "active"
 * @see \yii\widgets\Menu::$items
 */
class MenuLanguageItems extends AbstractLanguageItem
{
    /**
     * @inheritdoc
     */
    public function toArray()
    {
        $items = [];
        list($route, $params) = Yii::$app->getUrlManager()->parseRequest(Yii::$app->getRequest());
        $params = ArrayHelper::merge(Yii::$app->getRequest()->get(), $params);
        $url = isset($params['route']) ? $params['route'] : $route;
        foreach ($this->languages as $code => $name) {
        $code = Yii::$app->art->getDisplayLanguageShortcode($code);
        $link = Yii::$app->urlManager->createUrl(ArrayHelper::merge($params, [$url, $this->queryParam => $code]));
            $items[] = ArrayHelper::merge($this->options, [
                'label' => $name,
                'url' => $link,
                'active' => ($code === Yii::$app->language)
            ]);
        }
        return $items;
    }
}
