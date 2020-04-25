<?php defined('ALTUMCODE') || die() ?>

<header class="header">
    <div class="container">

        <div class="d-flex justify-content-between">
            <div class="d-flex">
                <img src="<?= get_gravatar($this->user->email) ?>" class="d-none d-md-block mr-3 user-avatar" />

                <div class="d-flex flex-column">
                    <h1><span class="underline"><?= $this->language->account->header->header ?></span></h1>

                    <p class="text-muted"><?= sprintf($this->language->account->header->last_activity, $this->user->last_activity) ?></p>
                </div>
            </div>

            <div>
                <a href="<?= url('account-details') ?>" class="btn btn-default rounded-pill"><i class="fa fa-list-ul"></i> <?= $this->language->account_details->menu ?></a>
            </div>
        </div>

    </div>
</header>

<?php require THEME_PATH . 'views/partials/ads_header.php' ?>

<section class="container">

    <?php display_notifications() ?>

    <div class="margin-top-3 d-flex justify-content-between">
        <h2><?= sprintf($this->language->account->package->header, $this->user->package->name) ?></h2>

        <?php if($this->settings->payment->is_enabled): ?>
        <div>
            <?php if($this->user->package_id == 'free'): ?>
                <a href="<?= url('package/upgrade') ?>" class="btn btn-success rounded-pill"><i class="fa fa-arrow-up"></i> <?= $this->language->account->package->upgrade_package ?></a>
            <?php elseif($this->user->package_id == 'trial'): ?>
                <a href="<?= url('package/renew') ?>" class="btn btn-success rounded-pill"><i class="fa fa-sync-alt"></i> <?= $this->language->account->package->renew_package ?></a>
            <?php else: ?>
                <a href="<?= url('package/renew') ?>" class="btn btn-success rounded-pill"><i class="fa fa-sync-alt"></i> <?= $this->language->account->package->renew_package ?></a>
            <?php endif ?>
        </div>
        <?php endif ?>
    </div>
    <span class="text-muted"><?= $this->language->account->package->subheader ?></span>

    <?php if($this->user->package_id != 'free'): ?>
        <p class="text-muted">
            <?= sprintf($this->language->account->package->subheader2, \Altum\Date::get($this->user->package_expiration_date, true)) ?>
            <?php if($this->user->payment_subscription_id): ?>
                ( <a href="<?= url('account/cancelsubscription' . \Altum\Middlewares\Csrf::get_url_query()) ?>" class="text-muted"><?= $this->language->account->package->cancel_subscription ?></a> )
            <?php endif ?>
        </p>


    <?php endif ?>

    <form action="" method="post" role="form" class="margin-top-3">
        <input type="hidden" name="token" value="<?= \Altum\Middlewares\Csrf::get() ?>" />

        <h2><?= $this->language->account->settings->header ?></h2>

        <div class="form-group">
            <label for="name"><?= $this->language->account->settings->name ?></label>
            <input type="text" id="name" name="name" class="form-control" value="<?= $this->user->name ?>" />
        </div>

        <div class="form-group">
            <label for="email"><?= $this->language->account->settings->email ?></label>
            <input type="text" id="email" name="email" class="form-control" value="<?= $this->user->email ?>" />
        </div>

        <div class="margin-top-6"></div>

        <h2><?= $this->language->account->change_password->header ?></h2>
        <p class="text-muted"><?= $this->language->account->change_password->subheader ?></p>

        <div>
            <div class="form-group">
                <label for="old_password"><?= $this->language->account->change_password->current_password ?></label>
                <input type="password" id="old_password" name="old_password" class="form-control" />
            </div>

            <div class="form-group">
                <label for="new_password"><?= $this->language->account->change_password->new_password ?></label>
                <input type="password" id="new_password" name="new_password" class="form-control" />
            </div>

            <div class="form-group">
                <label for="repeat_password"><?= $this->language->account->change_password->repeat_password ?></label>
                <input type="password" id="repeat_password" name="repeat_password" class="form-control" />
            </div>

            <div class="form-group text-center mt-3">
                <button type="submit" name="submit" class="btn btn-primary"><?= $this->language->global->submit ?></button>
            </div>
        </div>
    </form>

    <div class="margin-top-6 d-flex justify-content-between align-items-center">
        <div>
            <h2><?= $this->language->account->delete->header ?></h2>
            <p class="text-muted"><?= $this->language->account->delete->subheader ?></p>
        </div>

        <a href="<?= url('account/delete' . \Altum\Middlewares\Csrf::get_url_query()) ?>" class="btn btn-danger" data-confirm="<?= $this->language->global->info_message->confirm_delete ?>"><?= $this->language->global->delete ?></a>
    </div>

</section>



