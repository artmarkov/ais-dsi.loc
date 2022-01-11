<?php if (!empty($list)): ?>
    <?php foreach ($list as $v): ?>
        <div class="alert alert-<?= $v['type'] ?> alert-dismissible">
            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">&times;</button>
            <h4><i class="fa fa-<?= $v['icon'] ?>" aria-hidden="true" style="padding-right: 5px;"></i><?= $v['title'] ?></h4>
            <?= $v['message'] ?>
        </div>
    <?php endforeach; ?>
<?php endif; ?>