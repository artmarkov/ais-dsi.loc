<?php

namespace artsoft\settings\controllers;

use artsoft\controllers\admin\BaseController;
use artsoft\helpers\ArtHelper;
use Yii;

/**
 * CacheController implements Flush Cache page.
 *
 * @author Taras Makitra <makitrataras@gmail.com>
 */
class CacheController extends BaseController {

    /**
     * @inheritdoc
     */
    public $enableOnlyActions = ['flush'];

    public function actionFlush() {
        
        $frontendAssetPath = Yii::getAlias('@frontend') . '/web/assets/';
        $backendAssetPath = Yii::getAlias('@backend') . '/web/assets/';

        $frontendRuntimePath = Yii::getAlias('@frontend') . '/runtime/cache/';
        $backendRuntimePath = Yii::getAlias('@backend') . '/runtime/cache/';

        ArtHelper::recursiveDelete($frontendAssetPath);
        ArtHelper::recursiveDelete($backendAssetPath);

        ArtHelper::recursiveDelete($frontendRuntimePath);
        ArtHelper::recursiveDelete($backendRuntimePath);

        if (!is_dir($frontendAssetPath)) {
            @mkdir($frontendAssetPath);
        }

        if (!is_dir($backendAssetPath)) {
            @mkdir($backendAssetPath);
        }

        if (!is_dir($frontendRuntimePath)) {
            @mkdir($frontendRuntimePath);
        }

        if (!is_dir($backendRuntimePath)) {
            @mkdir($backendRuntimePath);
        }

        if (Yii::$app->cache->flush()) {
            Yii::$app->session->setFlash('success', Yii::t('art', 'Cache has been flushed.'));
        } else {
            Yii::$app->session->setFlash('error', Yii::t('art', 'Failed to flush cache.'));
        }

        return Yii::$app->getResponse()->redirect(Yii::$app->getRequest()->referrer);
    }

}
