<?php defined('ALTUMCODE') || die() ?>

<?php require THEME_PATH . 'views/partials/ads_header.php' ?>

<div class="container">
    <?php display_notifications() ?>

    <div class="d-flex flex-column align-items-center">
        <div class="card card-shadow col-xs-12 col-sm-10 col-md-6 col-lg-4">
            <div class="card-body">

                <h4 class="card-title d-flex justify-content-between"><?= $this->language->lost_password->header ?></h4>

                <form action="" method="post" class="mt-4" role="form">
                    <div class="form-group">
                        <label><?= $this->language->lost_password->form->email ?></label>
                        <input type="text" name="email" class="form-control" value="<?= $data->values['email'] ?>" placeholder="<?= $this->language->lost_password->form->email_placeholder ?>" required="required" />
                    </div>

                    <div class="form-group">
                        <?php $data->captcha->display() ?>
                    </div>

                    <div class="form-group mt-3">
                        <button type="submit" name="submit" class="btn btn-primary btn-block my-1"><?= $this->language->global->submit ?></button>
                    </div>
                </form>
            </div>
        </div>

        <div class="mt-4">
            <small><a href="login" class="text-muted"><?= $this->language->lost_password->return ?></a></small>
        </div>
    </div>
</div>

<?php ob_start() ?>
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<?php \Altum\Event::add_content(ob_get_clean(), 'head') ?>
