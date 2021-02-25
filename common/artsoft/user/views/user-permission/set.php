<?php
/**
 * @var yii\web\View $this
 * @var array $permissionsByGroup
 * @var artsoft\models\User $user
 */

use artsoft\models\Role;
use yii\bootstrap\BootstrapPluginAsset;
use yii\helpers\ArrayHelper;
use artsoft\helpers\Html;

$this->title = Yii::t('art/user', 'Roles and Permissions for "{user}"', ['user' => $user->username]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/user', 'Users'), 'url' => ['/user/default/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/user', $user->username), 'url' => ['/user/default/update', 'id' => $user->id]];
$this->params['breadcrumbs'][] = $this->title;

BootstrapPluginAsset::register($this);
?>
    <div class="row">
        <div class="col-sm-12">
            <h3 class="page-title"><?= Html::encode($this->title) ?></h3>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <strong>
                        <span class="glyphicon glyphicon-th"></span> <?= Yii::t('art/user', 'Roles') ?>
                    </strong>
                </div>
                <div class="panel-body">

                    <?= Html::beginForm(['set-roles', 'id' => $user->id]) ?>

                    <?= Html::checkboxList('roles',
                        ArrayHelper::map(Role::getUserRoles($user->id), 'name', 'name'),
                        ArrayHelper::map(Role::getAvailableRoles(), 'name', 'description'),
                        [
                            'item' => function ($index, $label, $name, $checked, $value) {
                                $list = '<ul style="padding-left: 10px">';
                                foreach (Role::getPermissionsByRole($value) as $permissionName => $permissionDescription) {
                                    $list .= $permissionDescription ? "<li>{$permissionDescription}</li>" : "<li>{$permissionName}</li>";
                                }
                                $list .= '</ul>';

                                $helpIcon = Html::beginTag('span', [
                                    'title' => Yii::t('art/user', 'Permissions for "{role}" role', ['role' => $label]),
                                    'data-content' => $list,
                                    'data-html' => 'true',
                                    'role' => 'button',
                                    'style' => 'margin: 0 30px 5px 0; padding: 0 5px;',
                                    'class' => 'btn btn-sm btn-default role-help-btn',
                                ]);
                                $helpIcon .= '?';
                                $helpIcon .= Html::endTag('span');
                                
                                $checkbox = Html::checkbox($name, $checked, ['label' => $label, 'value' => $value]);
                                return "<div><div class='pull-left' style='margin-right: 15px;'>{$checkbox}</div><div>{$helpIcon}</div></div>";
                            },
                        ]
                    ) ?>
                    <br/>

                    <?php if (Yii::$app->user->isSuperadmin OR Yii::$app->user->id != $user->id): ?>

                        <?= Html::submitButton(Yii::t('art', 'Save'), ['class' => 'btn btn-primary btn-sm']) ?>
                    <?php else: ?>
                        <div class="alert alert-warning well-sm text-center">
                            <?= Yii::t('art/user', "You can't update own permissions!") ?>
                        </div>
                    <?php endif; ?>

                    <?= Html::endForm() ?>
                </div>
            </div>
        </div>

        <div class="col-sm-8">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <strong>
                        <span class="glyphicon glyphicon-th"></span>
                        <?= Yii::t('art/user', 'Permissions') ?>
                    </strong>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <?php foreach ($permissionsByGroup as $groupName => $permissions): ?>
                            <div class="col-sm-6">
                                <fieldset>
                                    <legend><?= $groupName ?></legend>
                                    <ul>
                                        <?php foreach ($permissions as $permission): ?>
                                            <li><?= $permission->description ?></li>
                                        <?php endforeach ?>
                                    </ul>
                                </fieldset>
                                <br/>
                            </div>
                        <?php endforeach ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php
$this->registerJs(<<<JS

$('.role-help-btn').off('mouseover mouseleave')
	.on('mouseover', function(){
		var _t = $(this);
		_t.popover('show');
	}).on('mouseleave', function(){
		var _t = $(this);
		_t.popover('hide');
	});
JS
);
?>