<?php defined('ALTUMCODE') || die() ?>

<?php ob_start() ?>

<div class="my-3 embed-responsive embed-responsive-16by9 link-iframe-round">
    <iframe class="embed-responsive-item" scrolling="no" frameborder="no" src="https://player.twitch.tv/?channel=<?= $embed ?>&autoplay=false"></iframe>
</div>

<?php $html = ob_get_clean(); ?>

<?php return (object) ['html' => $html] ?>

