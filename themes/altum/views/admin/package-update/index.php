<?php defined('ALTUMCODE') || die() ?>

<div class="d-flex justify-content-between">
    <div class="d-flex align-items-center">
        <h1><span class="underline mr-3"><?= sprintf($this->language->admin_package_update->header, $data->package->name) ?></span></h1>

        <?= get_admin_options_button('package', $data->package->package_id) ?>
    </div>

    <div><?= get_back_button('admin/packages') ?></div>
</div>

<?php display_notifications() ?>

<div class="card border-0 shadow-sm mt-5">
    <div class="card-body">

        <form action="" method="post" role="form">
            <input type="hidden" name="token" value="<?= \Altum\Middlewares\Csrf::get() ?>" />
            <input type="hidden" name="type" value="update" />

            <div class="row">
                <?php if($data->package_id == 'free'): ?>

                    <div class="col-12">
                        <div class="form-group">
                            <label for="name"><?= $this->language->admin_packages->input->free->name ?></label>
                            <input type="text" id="name" name="name" class="form-control" value="<?= $data->package->name ?>" />
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-group">
                            <label for="is_enabled"><?= $this->language->admin_packages->input->is_enabled ?></label>
                            <select id="is_enabled" name="is_enabled" class="form-control">
                                <option value="1" <?= $data->package->is_enabled == '1' ? 'selected="true"' : null ?>><?= $this->language->global->yes ?></option>
                                <option value="0" <?= $data->package->is_enabled == '0' ? 'selected="true"' : null ?>><?= $this->language->global->no ?></option>
                            </select>
                        </div>
                    </div>

                <?php elseif($data->package_id == 'trial'): ?>

                    <div class="col-12">
                        <div class="form-group">
                            <label for="name"><?= $this->language->admin_packages->input->trial->name ?></label>
                            <input type="text" id="name" name="name" class="form-control" value="<?= $data->package->name ?>" />
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-group">
                            <label for="is_enabled"><?= $this->language->admin_packages->input->is_enabled ?></label>
                            <select id="is_enabled" name="is_enabled" class="form-control">
                                <option value="1" <?= $data->package->is_enabled == '1' ? 'selected="true"' : null ?>><?= $this->language->global->yes ?></option>
                                <option value="0" <?= $data->package->is_enabled == '0' ? 'selected="true"' : null ?>><?= $this->language->global->no ?></option>
                            </select>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-group">
                            <div class="form-group">
                                <label for="days"><?= $this->language->admin_packages->input->trial->days ?></label>
                                <input type="text" id="days" name="days" class="form-control" value="<?= $data->package->days ?>" />
                                <div><small class="text-muted"><?= $this->language->admin_packages->input->trial->days_help ?></small></div>
                            </div>
                        </div>
                    </div>

                <?php else: ?>

                    <div class="col-12">
                        <div class="form-group">
                            <label for="name"><?= $this->language->admin_packages->input->name ?></label>
                            <input type="text" id="name" name="name" class="form-control" value="<?= $data->package->name ?>" />
                        </div>
                    </div>

                    <div class="col-sm-12 col-md-6">
                        <div class="form-group">
                            <div class="form-group">
                                <label for="monthly_price"><?= sprintf($this->language->admin_packages->input->monthly_price, $this->settings->payment->currency) ?></label>
                                <input type="text" id="monthly_price" name="monthly_price" class="form-control" value="<?= $data->package->monthly_price ?>" />
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-12 col-md-6">
                        <div class="form-group">
                            <label for="annual_price"><?= sprintf($this->language->admin_packages->input->annual_price, $this->settings->payment->currency) ?></label>
                            <input type="text" id="annual_price" name="annual_price" class="form-control" value="<?= $data->package->annual_price ?>" />
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-group">
                            <label for="is_enabled"><?= $this->language->admin_packages->input->is_enabled ?></label>
                            <select id="is_enabled" name="is_enabled" class="form-control">
                                <option value="1" <?= $data->package->is_enabled == '1' ? 'selected="true"' : null ?>><?= $this->language->global->yes ?></option>
                                <option value="0" <?= $data->package->is_enabled == '0' ? 'selected="true"' : null ?>><?= $this->language->global->no ?></option>
                            </select>
                        </div>
                    </div>

                <?php endif ?>
            </div>

            <h2 class="card-title mt-3"><?= $this->language->admin_packages->header_settings ?></h2>
            <p class="text-muted"><?= $this->language->admin_packages->subheader_settings ?></p>

            <div class="form-group">
                <label for="projects_limit"><?= $this->language->admin_packages->input->projects_limit ?></label>
                <input type="number" id="projects_limit" name="projects_limit" min="-1" class="form-control" value="<?= $data->package->settings->projects_limit ?>" />
                <small class="text-muted"><?= $this->language->admin_packages->input->projects_limit_help ?></small>
            </div>

            <div class="form-group">
                <label for="biolinks_limit"><?= $this->language->admin_packages->input->biolinks_limit ?></label>
                <input type="number" id="biolinks_limit" name="biolinks_limit" min="-1" class="form-control" value="<?= $data->package->settings->biolinks_limit ?>" />
                <small class="text-muted"><?= $this->language->admin_packages->input->biolinks_limit_help ?></small>
            </div>

            <div class="form-group">
                <label for="links_limit"><?= $this->language->admin_packages->input->links_limit ?></label>
                <input type="number" id="links_limit" name="links_limit" min="-1" class="form-control" value="<?= $data->package->settings->links_limit ?>" />
                <small class="text-muted"><?= $this->language->admin_packages->input->links_limit_help ?></small>
            </div>

            <div class="custom-control custom-switch">
                <input id="no_ads" name="no_ads" type="checkbox" class="custom-control-input" <?= $data->package->settings->no_ads ? 'checked="true"' : null ?>>
                <label class="custom-control-label" for="no_ads"><?= $this->language->admin_packages->input->no_ads ?></label>
                <div><small class="text-muted"><?= $this->language->admin_packages->input->no_ads_help ?></small></div>
            </div>

            <div class="custom-control custom-switch">
                <input id="removable_branding" name="removable_branding" type="checkbox" class="custom-control-input" <?= $data->package->settings->removable_branding ? 'checked="true"' : null ?>>
                <label class="custom-control-label" for="removable_branding"><?= $this->language->admin_packages->input->removable_branding ?></label>
                <div><small class="text-muted"><?= $this->language->admin_packages->input->removable_branding_help ?></small></div>
            </div>

            <div class="custom-control custom-switch">
                <input id="custom_branding" name="custom_branding" type="checkbox" class="custom-control-input" <?= $data->package->settings->custom_branding ? 'checked="true"' : null ?>>
                <label class="custom-control-label" for="custom_branding"><?= $this->language->admin_packages->input->custom_branding ?></label>
                <div><small class="text-muted"><?= $this->language->admin_packages->input->custom_branding_help ?></small></div>
            </div>

            <div class="custom-control custom-switch">
                <input id="custom_colored_links" name="custom_colored_links" type="checkbox" class="custom-control-input" <?= $data->package->settings->custom_colored_links ? 'checked="true"' : null ?>>
                <label class="custom-control-label" for="custom_colored_links"><?= $this->language->admin_packages->input->custom_colored_links ?></label>
                <div><small class="text-muted"><?= $this->language->admin_packages->input->custom_colored_links_help ?></small></div>
            </div>

            <div class="custom-control custom-switch">
                <input id="statistics" name="statistics" type="checkbox" class="custom-control-input" <?= $data->package->settings->statistics ? 'checked="true"' : null ?>>
                <label class="custom-control-label" for="statistics"><?= $this->language->admin_packages->input->statistics ?></label>
                <div><small class="text-muted"><?= $this->language->admin_packages->input->statistics_help ?></small></div>
            </div>

            <div class="custom-control custom-switch">
                <input id="google_analytics" name="google_analytics" type="checkbox" class="custom-control-input" <?= $data->package->settings->google_analytics ? 'checked="true"' : null ?>>
                <label class="custom-control-label" for="google_analytics"><?= $this->language->admin_packages->input->google_analytics ?></label>
                <div><small class="text-muted"><?= $this->language->admin_packages->input->google_analytics_help ?></small></div>
            </div>

            <div class="custom-control custom-switch">
                <input id="facebook_pixel" name="facebook_pixel" type="checkbox" class="custom-control-input" <?= $data->package->settings->facebook_pixel ? 'checked="true"' : null ?>>
                <label class="custom-control-label" for="facebook_pixel"><?= $this->language->admin_packages->input->facebook_pixel ?></label>
                <div><small class="text-muted"><?= $this->language->admin_packages->input->facebook_pixel_help ?></small></div>
            </div>

            <div class="custom-control custom-switch">
                <input id="custom_backgrounds" name="custom_backgrounds" type="checkbox" class="custom-control-input" <?= $data->package->settings->custom_backgrounds ? 'checked="true"' : null ?>>
                <label class="custom-control-label" for="custom_backgrounds"><?= $this->language->admin_packages->input->custom_backgrounds ?></label>
                <div><small class="text-muted"><?= $this->language->admin_packages->input->custom_backgrounds_help ?></small></div>
            </div>

            <div class="custom-control custom-switch">
                <input id="verified" name="verified" type="checkbox" class="custom-control-input" <?= $data->package->settings->verified ? 'checked="true"' : null ?>>
                <label class="custom-control-label" for="verified"><?= $this->language->admin_packages->input->verified ?></label>
                <div><small class="text-muted"><?= $this->language->admin_packages->input->verified_help ?></small></div>
            </div>

            <div class="custom-control custom-switch">
                <input id="scheduling" name="scheduling" type="checkbox" class="custom-control-input" <?= $data->package->settings->scheduling ? 'checked="true"' : null ?>>
                <label class="custom-control-label" for="scheduling"><?= $this->language->admin_packages->input->scheduling ?></label>
                <div><small class="text-muted"><?= $this->language->admin_packages->input->scheduling_help ?></small></div>
            </div>

            <div class="text-center mt-3">
                <button type="submit" name="submit" class="btn btn-default"><?= $this->language->global->submit ?></button>
            </div>
        </form>

    </div>
</div>

<div class="card border-0 shadow-sm mt-5">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1><?= $this->language->admin_package_update->update_users_package_settings->header ?></h1>
                <p class="text-muted"><?= $this->language->admin_package_update->update_users_package_settings->subheader ?></p>
            </div>

            <form action="" method="post" role="form">
                <input type="hidden" name="token" value="<?= \Altum\Middlewares\Csrf::get() ?>" />
                <input type="hidden" name="type" value="update_users_package_settings" />

                <button type="submit" name="submit" class="btn btn-info"><?= $this->language->global->update ?></button>
            </form>
        </div>
    </div>
</div>

