<?php defined('ALTUMCODE') || die() ?>

<?php require THEME_PATH . 'views/partials/ads_header.php' ?>

<div class="container">
    <div class="d-flex flex-column justify-content-center">

        <?php display_notifications() ?>

        <?php if($this->user->package_is_expired && $this->user->package_id != 'free'): ?>
            <div class="alert alert-info" role="alert">
                <?= $this->language->global->info_message->user_package_is_expired ?>
            </div>
        <?php endif ?>

        <?php if($data->type == 'new'): ?>

            <h1><?= $this->language->package->header_new ?></h1>
            <span class="text-muted"><?= $this->language->package->subheader_new ?></span>

        <?php elseif($data->type == 'upgrade'): ?>

            <h1><?= $this->language->package->header_upgrade ?></h1>
            <span class="text-muted"><?= $this->language->package->subheader_upgrade ?></span>

        <?php elseif($data->type == 'renew'): ?>

            <h1><?= $this->language->package->header_renew ?></h1>
            <span class="text-muted"><?= $this->language->package->subheader_renew ?></span>

        <?php endif ?>


        <div class="margin-top-3 col-12">
            <?= $this->views['packages'] ?>
        </div>

    </div>
</div>
