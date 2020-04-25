<?php defined('ALTUMCODE') || die() ?>
<!DOCTYPE html>
<html class="admin" lang="<?= $this->language->language_code ?>">
    <head>
        <title><?= \Altum\Title::get() ?></title>
        <base href="<?= SITE_URL; ?>">
        <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <?php if(!empty($this->settings->favicon)): ?>
            <link href="<?= url(UPLOADS_URL_PATH . 'favicon/' . $this->settings->favicon) ?>" rel="shortcut icon" />
        <?php endif ?>

        <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600,700&display=swap" rel="stylesheet">

        <?php foreach(['bootstrap.min.css', 'custom.css', 'admin-custom.css', 'animate.min.css'] as $file): ?>
            <link href="<?= SITE_URL . ASSETS_URL_PATH ?>css/<?= $file ?>?v=<?= PRODUCT_CODE ?>" rel="stylesheet" media="screen">
        <?php endforeach ?>

        <?= \Altum\Event::get_content('head') ?>
    </head>

    <body>

        <div class="w-100">
            <?= $this->views['side_menu'] ?>

            <main role="main" class="admin-content-wrapper animated fadeIn">

                <?= $this->views['top_menu'] ?>

                <div class="container-fluid p-5">
                    <?= $this->views['content'] ?>

                    <?= $this->views['footer'] ?>
                </div>
            </main>
        </div>

        <?= \Altum\Event::get_content('modals') ?>

        <input type="hidden" id="url" name="url" value="<?= url() ?>" />
        <input type="hidden" name="global_token" value="<?= \Altum\Middlewares\Csrf::get('global_token') ?>" />
        <input type="hidden" name="number_decimal_point" value="<?= $this->language->global->number->decimal_point ?>" />
        <input type="hidden" name="number_thousands_separator" value="<?= $this->language->global->number->thousands_separator ?>" />

        <?php foreach(['libraries/jquery.min.js', 'libraries/popper.min.js', 'libraries/bootstrap.min.js', 'main.js', 'functions.js', 'libraries/fontawesome.min.js'] as $file): ?>
            <script src="<?= SITE_URL . ASSETS_URL_PATH ?>js/<?= $file ?>?v=<?= PRODUCT_CODE ?>"></script>
        <?php endforeach ?>

        <?= \Altum\Event::get_content('javascript') ?>

        <script>
            /* Toggler for the sidebar */
            $('#admin_menu_toggler').on('click', event => {
                event.preventDefault();
                $('body').toggleClass('admin-menu-opened');
            });
        </script>
    </body>
</html>
