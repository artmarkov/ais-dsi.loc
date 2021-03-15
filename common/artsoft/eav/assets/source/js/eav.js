$(function () {

    //loadEavAttributes();
    loadEavCategories();

    $('#entityModel').change(function () {
        loadEavCategories();
    });
});


function loadEavAttributes() {
    var model_id = $('#entityModel option:selected').val();
    var category_id = $('#entityCategory option:selected').val();

    $('.eav-attributes.eav-available .content').html('');
    $('.eav-attributes.eav-selected .content').html('');

    $.ajax({
        type: "POST",
        url: '/admin/eav/default/get-attributes',
        data: {model_id: model_id, category_id: category_id},
        success: function (data) {
            $('.eav-attributes.eav-available .content').html(data.available);
            $('.eav-attributes.eav-selected .content').html(data.selected);
            bindSortableActions();
        },
        error: function (data) {
            $('.eav-link-alert').hide().filter('.alert-danger').show();
            setTimeout(function () {
                $('.eav-link-alert').hide();
            }, 1500);
        }
    });
}

function loadEavCategories() {
    var model_id = $('#entityModel option:selected').val();
    $('.eav-categories-wrapper').html('');

    $.ajax({
        type: "POST",
        url: '/admin/eav/default/get-categories',
        data: {model_id: model_id},
        success: function (data) {
            $('.eav-categories-wrapper').parent().show();
            $('.eav-categories-wrapper').html(data.list);

            loadEavAttributes();

            $('.eav-categories-wrapper select').change(function () {
                loadEavAttributes();
            });
        },
        error: function (data) {
            $('.eav-categories-wrapper').parent().hide();

            loadEavAttributes();
        }
    });
}

function saveEavAttributes() {
    $('.eav-link-alert').hide().filter('.alert-primary').show();
    var eavAttributes = new Array();
    var entityModelId = $('#entityModel option:selected').val();
    var entityCategoryId = $('#entityCategory option:selected').val();

    $('.eav-selected .sortable li').each(function (index, element) {
        eavAttributes.push($(this).find('.attribute-id').data('attribute-id'));
    });

    $.ajax({
        type: "POST",
        url: '/admin/eav/default/set-attributes',
        data: {
            attributes: eavAttributes,
            model_id: entityModelId,
            category_id: entityCategoryId
        },
        success: function (data) {
            $('.eav-link-alert').hide().filter('.alert-info').show();
            setTimeout(function () {
                $('.eav-link-alert').hide();
            }, 1500);
        },
        error: function (data) {
            $('.eav-link-alert').hide().filter('.alert-danger').show();
            setTimeout(function () {
                $('.eav-link-alert').hide();
            }, 1500);
        }
    });
}

function bindSortableActions() {
    $('.eav-attributes .sortable').sortable({
        connectWith: ".eav-attributes .sortable",
        delay: 250,
        stop: function (event, ui) {
            saveEavAttributes();
        }
    }).disableSelection();
}
