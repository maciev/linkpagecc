<?php defined('ALTUMCODE') || die() ?>

<div class="d-flex justify-content-between">
    <div class="d-flex align-items-center">
        <h1><span class="underline mr-3"><?= $this->language->admin_domain_update->header ?></span></h1>

        <?= get_admin_options_button('domain', $data->domain->user_id) ?>
    </div>

    <div><?= get_back_button('admin/domains') ?></div>
</div>
<p class="text-muted"><?= $this->language->admin_domain_update->subheader ?></p>

<?php display_notifications() ?>

<div class="card border-0 shadow-sm mt-5">
    <div class="card-body">

        <form action="" method="post" class="mt-4" role="form">
            <input type="hidden" name="token" value="<?= \Altum\Middlewares\Csrf::get() ?>" />

            <div class="form-group">
                <label><i class="fa fa-network-wired text-gray-700"></i> <?= $this->language->admin_domain_create->form->host ?></label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <select name="scheme" class="appearance-none select-custom-altum form-control input-group-text">
                            <option value="https://" <?= $data->domain->scheme == 'https://' ? 'selected="selected"' : null ?>>https://</option>
                            <option value="http://" <?= $data->domain->scheme == 'http://' ? 'selected="selected"' : null ?>>http://</option>
                        </select>
                    </div>

                    <input type="text" class="form-control" name="host" placeholder="<?= $this->language->admin_domain_create->form->host_placeholder ?>" value="<?= $data->domain->host ?>" required="required" />
                </div>
                <small class="text-muted"><i class="fa fa-info-circle"></i> <?= $this->language->admin_domain_create->form->host_help ?></small>
            </div>

            <div class="form-group text-center mt-3">
                <button type="submit" name="submit" class="btn btn-primary"><?= $this->language->global->submit ?></button>
            </div>
        </form>

    </div>
</div>
