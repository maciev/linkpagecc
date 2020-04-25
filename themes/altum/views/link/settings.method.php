<?php defined('ALTUMCODE') || die() ?>

<?php

/* Get some variables */
$biolink_backgrounds = require APP_PATH . 'includes/biolink_backgrounds.php';

/* Get the proper settings depending on the type of link */
$settings = require THEME_PATH . 'views/link/settings/settings.' . strtolower($data->link->type) . '.method.php';

?>

<?= $settings->html ?>

<?php ob_start() ?>
<script src="<?= url(ASSETS_URL_PATH . 'js/libraries/datepicker.min.js') ?>"></script>
<script src="<?= url(ASSETS_URL_PATH . 'js/libraries/i18n/datepicker.en.js') ?>"></script>
<script src="<?= url(ASSETS_URL_PATH . 'js/libraries/bootstrap-iconpicker.min.js') ?>"></script>

<?= $settings->javascript ?>

<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
