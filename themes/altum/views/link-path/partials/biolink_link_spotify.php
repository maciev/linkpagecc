<?php defined('ALTUMCODE') || die() ?>

<?php ob_start() ?>

<?php if(in_array($embed_type, ['show', 'episode'])): ?>
    <div class="my-3 link-iframe-round">
        <iframe class="embed-responsive-item" src="https://open.spotify.com/embed/<?= $embed_type ?>/<?= $embed_value ?>" width="100%" height="232" frameborder="0" allowtransparency="true" allow="encrypted-media"></iframe>
    </div>
<?php else: ?>
    <div class="my-3 embed-responsive embed-responsive-16by9 link-iframe-round">
        <iframe class="embed-responsive-item" scrolling="no" frameborder="no" src="https://open.spotify.com/embed/<?= $embed_type ?>/<?= $embed_value ?>"></iframe>
    </div>
<?php endif ?>

<?php $html = ob_get_clean(); ?>

<?php return (object) ['html' => $html] ?>

