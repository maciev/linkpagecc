<?php defined('ALTUMCODE') || die() ?>

<?php require THEME_PATH . 'views/partials/ads_header.php' ?>

<div class="container">
    <?php display_notifications() ?>

    <div class="d-flex flex-column align-items-center">
        <div class="card card-shadow col-xs-12 col-sm-10 col-md-6 col-lg-4">
            <div class="card-body">

                <h4 class="card-title"><?= $this->language->reset_password->header ?></h4>

                <form action="" method="post" class="mt-4" role="form">
                    <input type="hidden" name="email" value="<?= $data->values['email'] ?>" class="form-control" />

                    <div class="form-group">
                        <label><?= $this->language->reset_password->form->new_password ?></label>
                        <input type="password" name="new_password" class="form-control"  required="required" />
                    </div>

                    <div class="form-group">
                        <label><?= $this->language->reset_password->form->repeat_password ?></label>
                        <input type="password" name="repeat_password" class="form-control" required="required" />
                    </div>

                    <div class="form-group mt-3">
                        <button type="submit" name="submit" class="btn btn-primary btn-block my-1"><?= $this->language->global->submit ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
