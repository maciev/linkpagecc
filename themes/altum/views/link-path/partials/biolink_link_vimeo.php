<?php defined('ALTUMCODE') || die() ?>

<?php ob_start() ?>

<div class="my-3 embed-responsive embed-responsive-16by9 link-iframe-round">
    <iframe class="embed-responsive-item" scrolling="no" frameborder="no" src="https://player.vimeo.com/video/<?= $embed ?>"></iframe>
</div>

<?php $html = ob_get_clean(); ?>

<?php return (object) ['html' => $html] ?>

