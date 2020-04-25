<?php defined('ALTUMCODE') || die() ?>
<!DOCTYPE html>
<html lang="<?= $this->language->language_code ?>" class="link-html">
    <head>
        <title><?= \Altum\Title::get() ?></title>
        <base href="<?= SITE_URL; ?>">
        <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <?php if(!empty($this->settings->favicon)): ?>
            <link href="<?= url(UPLOADS_URL_PATH . 'favicon/' . $this->settings->favicon) ?>" rel="shortcut icon" />
        <?php endif ?>

        <link href="https://fonts.googleapis.com/css?family=Lato&display=swap" rel="stylesheet">

        <?php foreach(['bootstrap.min.css', 'custom.css', 'link-custom.css', 'animate.min.css'] as $file): ?>
            <link href="<?= url(ASSETS_URL_PATH . 'css/' . $file . '?v=' . PRODUCT_CODE) ?>" rel="stylesheet" media="screen">
        <?php endforeach ?>

        <?= \Altum\Event::get_content('head') ?>

        <?php if(!empty($this->settings->head_js)): ?>
            <?= $this->settings->head_js ?>
        <?php endif ?>
    </head>

    <?= $this->views['content'] ?>

    <input type="hidden" id="url" name="url" value="<?= url() ?>" />
    <input type="hidden" name="global_token" value="<?= \Altum\Middlewares\Csrf::get('global_token') ?>" />
    <input type="hidden" name="number_decimal_point" value="<?= $this->language->global->number->decimal_point ?>" />
    <input type="hidden" name="number_thousands_separator" value="<?= $this->language->global->number->thousands_separator ?>" />

    <?php foreach(['libraries/jquery.min.js', 'libraries/popper.min.js', 'libraries/bootstrap.min.js', 'main.js', 'functions.js', 'libraries/fontawesome.min.js'] as $file): ?>
        <script src="<?= SITE_URL . ASSETS_URL_PATH ?>js/<?= $file ?>?v=<?= PRODUCT_CODE ?>"></script>
    <?php endforeach ?>

    <?= \Altum\Event::get_content('javascript') ?>
</html>
