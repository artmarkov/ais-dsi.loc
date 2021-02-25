<?php
/**
 * @var artsoft\widgets\ActiveForm $form
 * @var array $childRoles
 * @var array $allRoles
 * @var array $routes
 * @var array $currentRoutes
 * @var array $permissionsByGroup
 * @var array $currentPermissions
 * @var yii\rbac\Role $role
 */

use artsoft\helpers\Html;
use artsoft\models\Role;
use artsoft\models\User;
use yii\helpers\ArrayHelper;

$this->title = Yii::t('art/user', '{permission} Role Settings', ['permission' => $role->description]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/user', 'Users'), 'url' => ['/user/default/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/user', 'Roles'), 'url' => ['/user/role/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

    <div class="row">
        <div class="col-sm-12">
            <h3 class="page-title"><?= Html::encode($this->title) ?></h3>
            <?= Html::a(Yii::t('art', 'Edit'), ['update', 'id' => $role->name], ['class' => 'btn btn-sm btn-primary']) ?>
            <?= Html::a(Yii::t('art', 'Create'), ['create'], ['class' => 'btn btn-sm btn-success']) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <strong>
                        <span
                            class="glyphicon glyphicon-th"></span> <?= Yii::t('art/user', 'Child roles') ?>
                    </strong>
                </div>
                <div class="panel-body">
                    <?= Html::beginForm(['set-child-roles', 'id' => $role->name]) ?>

                    <?= Html::checkboxList('child_roles',
                        ArrayHelper::map($childRoles, 'name', 'name'),
                        ArrayHelper::map($allRoles, 'name', 'description'),
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
                                    'style' => 'margin-bottom: 5px; padding: 0 5px',
                                    'class' => 'btn btn-sm btn-default role-help-btn',
                                ]);
                                $helpIcon .= '?';
                                $helpIcon .= Html::endTag('span');

                                $checkbox = Html::checkbox($name, $checked, ['label' => $label, 'value' => $value]);
                                return "<div><div class='pull-left' style='margin-right: 15px;'>{$checkbox}</div><div>{$helpIcon}</div></div>";
                            },
                            //'separator' => '<br>'
                        ]
                    ) ?>

                    <hr/>
                    <?php if (User::hasPermission('manageRolesAndPermissions')): ?>
                        <?= Html::submitButton(Yii::t('art', 'Save'), ['class' => 'btn btn-primary btn-sm']) ?>
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
                    <?= Html::beginForm(['set-child-permissions', 'id' => $role->name]) ?>

                    <div class="row">
                        <?php foreach ($permissionsByGroup as $groupName => $permissions): ?>
                            <div class="col-sm-6">
                                <fieldset>
                                    <legend><?= $groupName ?></legend>
                                    <?= Html::checkboxList(
                                        'child_permissions',
                                        ArrayHelper::map($currentPermissions, 'name', 'name'),
                                        ArrayHelper::map($permissions, 'name', 'description')
                                        
                                    ) ?>
                                </fieldset>
                                <br/>
                            </div>
                        <?php endforeach ?>
                    </div>

                    <hr/>

                    <?php if (User::hasPermission('manageRolesAndPermissions')): ?>
                        <?= Html::submitButton(Yii::t('art', 'Save'), ['class' => 'btn btn-primary btn-sm']) ?>
                    <?php endif; ?>

                    <?= Html::endForm() ?>

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