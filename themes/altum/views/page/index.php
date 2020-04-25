<?php defined('ALTUMCODE') || die() ?>

<?php require THEME_PATH . 'views/partials/ads_header.php' ?>

<div class="container">
    <div class="d-flex justify-content-center">
        <div class="col-md-10 col-lg-8">

            <div class="d-flex align-items-center">
                <h1><span class="underline mr-3"><?= $data->page->title ?></span></h1>

                <?php if(\Altum\Middlewares\Authentication::is_admin()): ?>
                    <?= get_admin_options_button('page', $data->page->page_id) ?>
                <?php endif ?>
            </div>


            <?= $data->page->description ?>
        </div>
    </div>
</div>
