<?php
/**
 * This is the template for generating a CRUD controller class file.
 */

use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

$controllerClass = StringHelper::basename($generator->controllerClass);
$baseControllerClass = StringHelper::basename($generator->baseControllerClass);

echo "<?php\n";
?>

namespace <?= StringHelper::dirname(ltrim($generator->controllerClass, '\\')) ?>;

use Yii;
use <?= $generator->baseControllerClass ?>;

/**
 * <?= $controllerClass ?> implements the CRUD actions for <?= $generator->modelClass ?> model.
 */
class <?= $controllerClass ?> extends <?= $baseControllerClass ?> 
{
    public $modelClass       = '<?= $generator->modelClass ?>';
    public $modelSearchClass = '<?= $generator->searchModelClass ?>';

    protected function getRedirectPage($action, $model = null)
    {
        switch ($action) {
            case 'update':
                return ['update', 'id' => $model->id];
                break;
            case 'create':
                return ['update', 'id' => $model->id];
                break;
            default:
                return parent::getRedirectPage($action, $model);
        }
    }
}