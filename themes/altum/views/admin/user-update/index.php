<?php defined('ALTUMCODE') || die() ?>

<div class="d-flex justify-content-between">
    <div class="d-flex align-items-center">
        <h1><span class="underline mr-3"><?= $this->language->admin_user_update->header ?></span></h1>

        <?= get_admin_options_button('user', $data->user->user_id) ?>
    </div>

    <div><?= get_back_button('admin/users') ?></div>
</div>

<?php display_notifications() ?>

<div class="card border-0 shadow-sm mt-5">
    <div class="card-body">

        <form action="" method="post" role="form" enctype="multipart/form-data">
            <input type="hidden" name="token" value="<?= \Altum\Middlewares\Csrf::get() ?>" />

            <div class="form-group">
                <label><?= $this->language->admin_user_update->main->name ?></label>
                <input type="text" name="name" class="form-control" value="<?= $data->user->name ?>" />
            </div>

            <div class="form-group">
                <label><?= $this->language->admin_user_update->main->email ?></label>
                <input type="text" name="email" class="form-control" value="<?= $data->user->email ?>" />
            </div>

            <div class="form-group">
                <label><?= $this->language->admin_user_update->main->status ?></label>

                <select class="custom-select" name="status">
                    <option value="1" <?php if($data->user->active == 1) echo 'selected' ?>><?= $this->language->admin_user_update->main->status_active ?></option>
                    <option value="0" <?php if($data->user->active == 0) echo 'selected' ?>><?= $this->language->admin_user_update->main->status_disabled ?></option>
                </select>
            </div>

            <div class="form-group">
                <label><?= $this->language->admin_user_update->main->type ?></label>

                <select class="custom-select" name="type">
                    <option value="1" <?php if($data->user->type == 1) echo 'selected' ?>><?= $this->language->admin_user_update->main->type_admin ?></option>
                    <option value="0" <?php if($data->user->type == 0) echo 'selected' ?>><?= $this->language->admin_user_update->main->type_user ?></option>
                </select>

                <small class="text-muted"><?= $this->language->admin_user_update->main->type_help ?></small>
            </div>

            <h2 class="mt-5"><?= $this->language->admin_user_update->package->header ?></h2>
            <p class="text-muted"><?= $this->language->admin_user_update->package->header_help ?></p>

            <div class="form-group">
                <label><?= $this->language->admin_user_update->package->package_id ?></label>

                <select class="custom-select" name="package_id">
                    <option value="free" <?php if($data->user->package->package_id == 'free') echo 'selected' ?>><?= $this->settings->package_free->name ?></option>
                    <option value="trial" <?php if($data->user->package->package_id == 'trial') echo 'selected' ?>><?= $this->settings->package_trial->name ?></option>
                    <option value="custom" <?php if($data->user->package->package_id == 'custom') echo 'selected' ?>><?= $this->settings->package_custom->name ?></option>

                    <?php while($row = $data->packages_result->fetch_object()): ?>
                        <option value="<?= $row->package_id ?>" <?php if($data->user->package->package_id == $row->package_id) echo 'selected' ?>><?= $row->name ?></option>
                    <?php endwhile ?>
                </select>
            </div>

            <div class="form-group">
                <label><?= $this->language->admin_user_update->package->package_trial_done ?></label>

                <select class="custom-select" name="package_trial_done">
                    <option value="1" <?= $data->user->package_trial_done ? 'selected="selected"' : null ?>><?= $this->language->global->yes ?></option>
                    <option value="0" <?= !$data->user->package_trial_done ? 'selected="selected"' : null ?>><?= $this->language->global->no ?></option>
                </select>
            </div>

            <div id="package_expiration_date_container" class="form-group">
                <label><?= $this->language->admin_user_update->package->package_expiration_date ?></label>
                <input type="text" class="form-control" name="package_expiration_date" autocomplete="off" value="<?= $data->user->package_expiration_date ?>">
            </div>

            <div id="package_settings" style="display: none">
                <div class="form-group">
                    <label for="projects_limit"><?= $this->language->admin_packages->input->projects_limit ?></label>
                    <input type="number" id="projects_limit" name="projects_limit" min="-1" class="form-control" value="<?= $data->user->package->settings->projects_limit ?>" />
                    <small class="text-muted"><?= $this->language->admin_packages->input->projects_limit_help ?></small>
                </div>

                <div class="form-group">
                    <label for="biolinks_limit"><?= $this->language->admin_packages->input->biolinks_limit ?></label>
                    <input type="number" id="biolinks_limit" name="biolinks_limit" min="-1" class="form-control" value="<?= $data->user->package->settings->biolinks_limit ?>" />
                    <small class="text-muted"><?= $this->language->admin_packages->input->biolinks_limit_help ?></small>
                </div>

                <div class="form-group">
                    <label for="links_limit"><?= $this->language->admin_packages->input->links_limit ?></label>
                    <input type="number" id="links_limit" name="links_limit" min="-1" class="form-control" value="<?= $data->user->package->settings->links_limit ?>" />
                    <small class="text-muted"><?= $this->language->admin_packages->input->links_limit_help ?></small>
                </div>

                <div class="custom-control custom-switch">
                    <input id="no_ads" name="no_ads" type="checkbox" class="custom-control-input" <?= $data->user->package->settings->no_ads ? 'checked="true"' : null ?>>
                    <label class="custom-control-label" for="no_ads"><?= $this->language->admin_packages->input->no_ads ?></label>
                    <div><small class="text-muted"><?= $this->language->admin_packages->input->no_ads_help ?></small></div>
                </div>

                <div class="custom-control custom-switch">
                    <input id="removable_branding" name="removable_branding" type="checkbox" class="custom-control-input" <?= $data->user->package->settings->removable_branding ? 'checked="true"' : null ?>>
                    <label class="custom-control-label" for="removable_branding"><?= $this->language->admin_packages->input->removable_branding ?></label>
                    <div><small class="text-muted"><?= $this->language->admin_packages->input->removable_branding_help ?></small></div>
                </div>

                <div class="custom-control custom-switch">
                    <input id="custom_branding" name="custom_branding" type="checkbox" class="custom-control-input" <?= $data->user->package->settings->custom_branding ? 'checked="true"' : null ?>>
                    <label class="custom-control-label" for="custom_branding"><?= $this->language->admin_packages->input->custom_branding ?></label>
                    <div><small class="text-muted"><?= $this->language->admin_packages->input->custom_branding_help ?></small></div>
                </div>

                <div class="custom-control custom-switch">
                    <input id="custom_colored_links" name="custom_colored_links" type="checkbox" class="custom-control-input" <?= $data->user->package->settings->custom_colored_links ? 'checked="true"' : null ?>>
                    <label class="custom-control-label" for="custom_colored_links"><?= $this->language->admin_packages->input->custom_colored_links ?></label>
                    <div><small class="text-muted"><?= $this->language->admin_packages->input->custom_colored_links_help ?></small></div>
                </div>

                <div class="custom-control custom-switch">
                    <input id="statistics" name="statistics" type="checkbox" class="custom-control-input" <?= $data->user->package->settings->statistics ? 'checked="true"' : null ?>>
                    <label class="custom-control-label" for="statistics"><?= $this->language->admin_packages->input->statistics ?></label>
                    <div><small class="text-muted"><?= $this->language->admin_packages->input->statistics_help ?></small></div>
                </div>

                <div class="custom-control custom-switch">
                    <input id="google_analytics" name="google_analytics" type="checkbox" class="custom-control-input" <?= $data->user->package->settings->google_analytics ? 'checked="true"' : null ?>>
                    <label class="custom-control-label" for="google_analytics"><?= $this->language->admin_packages->input->google_analytics ?></label>
                    <div><small class="text-muted"><?= $this->language->admin_packages->input->google_analytics_help ?></small></div>
                </div>

                <div class="custom-control custom-switch">
                    <input id="facebook_pixel" name="facebook_pixel" type="checkbox" class="custom-control-input" <?= $data->user->package->settings->facebook_pixel ? 'checked="true"' : null ?>>
                    <label class="custom-control-label" for="facebook_pixel"><?= $this->language->admin_packages->input->facebook_pixel ?></label>
                    <div><small class="text-muted"><?= $this->language->admin_packages->input->facebook_pixel_help ?></small></div>
                </div>

                <div class="custom-control custom-switch">
                    <input id="custom_backgrounds" name="custom_backgrounds" type="checkbox" class="custom-control-input" <?= $data->user->package->settings->custom_backgrounds ? 'checked="true"' : null ?>>
                    <label class="custom-control-label" for="custom_backgrounds"><?= $this->language->admin_packages->input->custom_backgrounds ?></label>
                    <div><small class="text-muted"><?= $this->language->admin_packages->input->custom_backgrounds_help ?></small></div>
                </div>

                <div class="custom-control custom-switch">
                    <input id="verified" name="verified" type="checkbox" class="custom-control-input" <?= $data->user->package->settings->verified ? 'checked="true"' : null ?>>
                    <label class="custom-control-label" for="verified"><?= $this->language->admin_packages->input->verified ?></label>
                    <div><small class="text-muted"><?= $this->language->admin_packages->input->verified_help ?></small></div>
                </div>

                <div class="custom-control custom-switch">
                    <input id="scheduling" name="scheduling" type="checkbox" class="custom-control-input" <?= $data->user->package->settings->scheduling ? 'checked="true"' : null ?>>
                    <label class="custom-control-label" for="scheduling"><?= $this->language->admin_packages->input->scheduling ?></label>
                    <div><small class="text-muted"><?= $this->language->admin_packages->input->scheduling_help ?></small></div>
                </div>
            </div>

            <h2 class="mt-5"><?= $this->language->admin_user_update->change_password->header ?></h2>
            <p class="text-muted"><?= $this->language->admin_user_update->change_password->header_help ?></p>

            <div class="form-group">
                <label><?= $this->language->admin_user_update->change_password->new_password ?></label>
                <input type="password" name="new_password" class="form-control" />
            </div>

            <div class="form-group">
                <label><?= $this->language->admin_user_update->change_password->repeat_password ?></label>
                <input type="password" name="repeat_password" class="form-control" />
            </div>

            <div class="text-center mt-3">
                <button type="submit" name="submit" class="btn btn-default"><?= $this->language->global->submit ?></button>
            </div>
        </form>
    </div>
</div>

<?php ob_start() ?>
<link href="<?= url(ASSETS_URL_PATH . 'css/datepicker.min.css') ?>" rel="stylesheet" media="screen">
<?php \Altum\Event::add_content(ob_get_clean(), 'head') ?>

<?php ob_start() ?>
<script src="<?= url(ASSETS_URL_PATH . 'js/libraries/datepicker.min.js') ?>"></script>
<script src="<?= url(ASSETS_URL_PATH . 'js/libraries/i18n/datepicker.en.js') ?>"></script>
<script>
    let check_package_id = () => {
        let selected_package_id = $('[name="package_id"]').find(':selected').attr('value');

        if(selected_package_id == 'free') {
            $('#package_expiration_date_container').hide();
        } else {
            $('#package_expiration_date_container').show();
        }

        if(selected_package_id == 'custom') {
            $('#package_settings').show();
        } else {
            $('#package_settings').hide();
        }
    };

    /* Initial check */
    check_package_id();

    $('[name="package_expiration_date"]').datepicker({
        classes: 'datepicker-modal',
        language: 'en',
        dateFormat: 'yyyy-mm-dd',
        autoClose: true,
        timepicker: false,
        toggleSelected: false,
        minDate: new Date()
    });

    /* Dont show expiration date when the chosen package is the free one */
    $('[name="package_id"]').on('change', check_package_id);
</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
