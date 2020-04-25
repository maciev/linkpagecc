<?php defined('ALTUMCODE') || die() ?>

<?php ob_start() ?>

<div class="my-3">
    <a href="<?= $link->location_url ?>" data-location-url="<?= $link->url ?>" class="btn btn-block btn-default link-btn <?= $link->design->link_class ?>" style="<?= $link->design->link_style ?>">

        <?php if($link->settings->icon): ?>
            <i class="<?= $link->settings->icon ?> mr-1"></i>
        <?php endif ?>

        <?= $link->settings->name ?>
    </a>
</div>

<?php $html = ob_get_clean(); ?>

<?php return (object) ['html' => $html] ?>

