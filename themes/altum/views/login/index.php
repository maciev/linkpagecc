<?php defined('ALTUMCODE') || die() ?>

<?php require THEME_PATH . 'views/partials/ads_header.php' ?>

<div class="container">
    <?php display_notifications() ?>

    <div class="d-flex flex-column align-items-center">
        <div class="card card-shadow col-xs-12 col-sm-10 col-md-6 col-lg-4">
            <div class="card-body">

                <h4 class="card-title"><?= $this->language->login->header ?></h4>

                <form action="" method="post" class="mt-4" role="form">
                    <div class="form-group">
                        <label><?= $this->language->login->form->email ?></label>
                        <input type="text" name="email" class="form-control" placeholder="<?= $this->language->login->form->email_placeholder ?>" value="<?= $data->values['email'] ?>" required="required" />
                    </div>

                    <div class="form-group">
                        <label><?= $this->language->login->form->password ?></label>
                        <input type="password" name="password" class="form-control" placeholder="<?= $this->language->login->form->password_placeholder ?>"  required="required" />
                    </div>

                    <div class="form-check">
                        <label class="form-check-label">
                            <input type="checkbox" class="form-check-input" name="rememberme">
                            <small class="text-muted"><?= $this->language->login->form->remember_me ?></small>
                        </label>
                    </div>

                    <div class="form-group mt-3">
                        <button type="submit" name="submit" class="btn btn-primary btn-block my-1"><?= $this->language->login->form->login ?></button>
                    </div>

                    <div class="row">
                        <?php if($this->settings->facebook->is_enabled): ?>
                            <div class="col-sm mt-1">
                                <a href="<?= $data->facebook_login_url ?>" class="btn btn-light btn-block"><?= sprintf($this->language->login->display->facebook, "<i class=\"fab fa-facebook\"></i>") ?></a>
                            </div>
                        <?php endif ?>

                        <?php if($this->settings->instagram->is_enabled): ?>
                        <div class="col-sm mt-1">
                            <a href="<?= $data->instagram_login_url ?>" class="btn btn-light btn-block"><?= sprintf($this->language->login->display->instagram, "<i class=\"fab fa-instagram fa-lg\"></i>") ?></a>
                        </div>
                        <?php endif ?>
                    </div>

                    <div class="mt-4 text-center">
                        <small><a href="lost-password" class="text-muted" role="button"><?= $this->language->login->display->lost_password ?></a> / <a href="resend-activation" class="text-muted" role="button"><?= $this->language->login->display->resend_activation ?></a></small>
                    </div>
                </form>
            </div>
        </div>

        <?php if($this->settings->register_is_enabled): ?>
            <div class="mt-4">
                <?= sprintf($this->language->login->display->register, '<a href="' . url('register') . '" class="font-weight-bold">' . $this->language->login->display->register_help . '</a>') ?></a>
            </div>
        <?php endif ?>
    </div>
</div>
