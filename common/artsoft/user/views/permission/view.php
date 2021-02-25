<?php
/**
 * @var $this yii\web\View
 * @var artsoft\widgets\ActiveForm $form
 * @var array $routes
 * @var array $childRoutes
 * @var array $permissionsByGroup
 * @var array $childPermissions
 * @var yii\rbac\Permission $item
 */

use artsoft\helpers\Html;
use artsoft\models\User;
use yii\helpers\ArrayHelper;

$this->title = Yii::t('art/user', '{permission} Permission Settings', ['permission' => $item->description]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/user', 'Users'), 'url' => ['/user/default/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/user', 'Permissions'), 'url' => ['/user/role/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

    <div class="row">
        <div class="col-sm-12">
            <h3 class="page-title"><?= Html::encode($this->title) ?></h3>
            <?= Html::a(Yii::t('art', 'Edit'), ['update', 'id' => $item->name], ['class' => 'btn btn-sm btn-primary']) ?>
            <?= Html::a(Yii::t('art', 'Create'), ['create'], ['class' => 'btn btn-sm btn-success']) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <strong>
                        <span class="glyphicon glyphicon-th"></span>
                        <?= Yii::t('art/user', 'Child permissions') ?>
                    </strong>
                </div>
                <div class="panel-body">

                    <?= Html::beginForm(['set-child-permissions', 'id' => $item->name]) ?>

                    <div class="row">
                        <?php foreach ($permissionsByGroup as $groupName => $permissions): ?>
                            <div class="col-sm-12">
                                <fieldset>
                                    <legend><?= $groupName ?></legend>
                                    <?= Html::checkboxList(
                                        'child_permissions',
                                        ArrayHelper::map($childPermissions, 'name', 'name'),
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

        <div class="col-sm-8">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <strong>
                        <span class="glyphicon glyphicon-th"></span> <?= Yii::t('art/user', 'Routes') ?>
                    </strong>
                </div>

                <div class="panel-body">

                    <?= Html::beginForm(['set-child-routes', 'id' => $item->name]) ?>

                    <div class="row">
                        <div class="col-sm-3">
                            <?php if (User::hasPermission('manageRolesAndPermissions')): ?>
                                <?= Html::submitButton(Yii::t('art', 'Save'), ['class' => 'btn btn-primary btn-sm']) ?>
                            <?php endif; ?>
                        </div>

                        <div class="col-sm-6">
                            <input id="search-in-routes" autofocus="on" type="text" class="form-control input-sm"
                                   placeholder="<?= Yii::t('art/user', 'Search route') ?>">
                        </div>

<!--                        <div class="col-sm-3 text-right">
                            <span id="show-only-selected-routes" class="btn btn-default btn-sm">
				<i class="fa fa-minus"></i> <?= Yii::t('art/user', 'Show only selected') ?>
                            </span>
                            <span id="show-all-routes" class="btn btn-default btn-sm hide">
				<i class="fa fa-plus"></i> <?= Yii::t('art/user', 'Show all') ?>
                            </span>
                        </div>-->
                    </div>

                    <hr/>

                    <?= Html::checkboxList(
                        'child_routes',
                        ArrayHelper::map($childRoutes, 'name', 'name'),
                        ArrayHelper::map($routes, 'name', 'name'),
                        [
                            'id' => 'routes-list',
                            'separator' => '<div class="separator"></div>',
                            'item' => function ($index, $label, $name, $checked, $value) {
                                return Html::checkbox($name, $checked, [
                                    'value' => $value,
                                    'label' => '<span class="route-text">' . $label . '</span>',
                                    'labelOptions' => ['class' => 'route-label'],
                                    'class' => 'route-checkbox',
                                ]);
                            },
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
    </div>

<?php
$js = <<<JS

var routeCheckboxes = $('.route-checkbox');
var routeText = $('.route-text');

// For checked routes
var backgroundColor = '#D6FFDE';

function showAllRoutesBack() {
	$('#routes-list').find('.hide').each(function(){
		$(this).removeClass('hide');
	});
}

//Make tree-like structure by padding controllers and actions
routeText.each(function(){
	var _t = $(this);

	var chunks = _t.html().split('/').reverse();
	var margin = chunks.length * 30 - 30;

	if ( chunks[0] == '*' )
	{
		margin -= 30;
	}

	_t.closest('label').closest('div.checkbox').css('margin-left', margin);

});

// Highlight selected checkboxes
routeCheckboxes.each(function(){
	var _t = $(this);

	if ( _t.is(':checked') )
	{
		_t.closest('label').css('background', backgroundColor);
	}
});

// Change background on check/uncheck
routeCheckboxes.on('change', function(){
	var _t = $(this);

	if ( _t.is(':checked') )
	{
		_t.closest('label').css('background', backgroundColor);
	}
	else
	{
		_t.closest('label').css('background', 'none');
	}
});


// Hide on not selected routes
$('#show-only-selected-routes').on('click', function(){
	$(this).addClass('hide');
	$('#show-all-routes').removeClass('hide');

	routeCheckboxes.each(function(){
		var _t = $(this);

		if ( ! _t.is(':checked') )
		{
			_t.closest('label').addClass('hide');
			_t.closest('div.separator').addClass('hide');
		}
	});
});

// Show all routes back
$('#show-all-routes').on('click', function(){
	$(this).addClass('hide');
	$('#show-only-selected-routes').removeClass('hide');

	showAllRoutesBack();
});

// Search in routes and hide not matched
$('#search-in-routes').on('change keyup', function(){
	var input = $(this);

	if ( input.val() == '' )
	{
		showAllRoutesBack();
		return;
	}

	routeText.each(function(){
		var _t = $(this);

		if ( _t.html().indexOf(input.val()) > -1 )
		{
			_t.closest('label').removeClass('hide');
			_t.closest('div.separator').removeClass('hide');
		}
		else
		{
			_t.closest('label').addClass('hide');
			_t.closest('div.separator').addClass('hide');
		}
	});
});

JS;

$this->registerJs($js);
?>