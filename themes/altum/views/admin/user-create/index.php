<?php defined('ALTUMCODE') || die() ?>

<div class="d-flex justify-content-between">
    <div class="d-flex align-items-center">
        <h1><span class="underline mr-3"><?= $this->language->admin_user_create->header ?></span></h1>
    </div>

    <div><?= get_back_button('admin/users') ?></div>
</div>

<?php display_notifications() ?>

<div class="card border-0 shadow-sm mt-5">
    <div class="card-body">

        <form action="" method="post" class="mt-4" role="form">
            <input type="hidden" name="token" value="<?= \Altum\Middlewares\Csrf::get() ?>" />

            <div class="form-group">
                <label><?= $this->language->admin_user_create->form->name ?></label>
                <input type="text" name="name" class="form-control" value="<?= $data->values['name'] ?>" placeholder="<?= $this->language->admin_user_create->form->name_placeholder ?>" required="required" />
            </div>

            <div class="form-group">
                <label><?= $this->language->admin_user_create->form->email ?></label>
                <input type="text" name="email" class="form-control" value="<?= $data->values['email'] ?>" placeholder="<?= $this->language->admin_user_create->form->email_placeholder ?>" required="required" />
            </div>

            <div class="form-group">
                <label><?= $this->language->admin_user_create->form->password ?></label>
                <input type="password" name="password" class="form-control" value="<?= $data->values['password'] ?>" placeholder="<?= $this->language->admin_user_create->form->password_placeholder ?>" required="required" />
            </div>

            <div class="form-group text-center mt-3">
                <button type="submit" name="submit" class="btn btn-primary"><?= $this->language->admin_user_create->form->create ?></button>
            </div>
        </form>

    </div>
</div>

