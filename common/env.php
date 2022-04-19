<?php
/**
 * Require helpers
 */
require_once(__DIR__ . '/helpers.php');

/**
 * Load application environment from .env file
 */
$dotenv = new \Dotenv\Dotenv(dirname(__DIR__));
$dotenv->load();

/**
 * Init application constants
 */
defined('YII_ENV') or define('YII_ENV', env('YII_ENV', 'prod'));
defined('YII_DEBUG') or define('YII_DEBUG', isset($_COOKIE['yii_debug']) || env('YII_DEBUG', false));

