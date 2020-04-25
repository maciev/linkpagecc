<?php defined('ALTUMCODE') || die() ?>

<div class="d-flex justify-content-between">
    <h1 class="d-flex">
        <span class="underline mr-3"><?= $this->language->admin_package_create->header ?></span>
    </h1>

    <div><?= get_back_button('admin/packages') ?></div>
</div>

<div class="card border-0 shadow-sm mt-5">
    <div class="card-body">

        <form action="" method="post" role="form">
            <input type="hidden" name="token" value="<?= \Altum\Middlewares\Csrf::get() ?>" />

            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <label><?= $this->language->admin_packages->input->name ?></label>
                        <input type="text" name="name" class="form-control" />
                    </div>
                </div>

                <div class="col-sm-12 col-md-6">
                    <div class="form-group">
                        <div class="form-group">
                            <label><?= sprintf($this->language->admin_packages->input->monthly_price, $this->settings->payment->currency) ?></label>
                            <input type="text" name="monthly_price" class="form-control" />
                        </div>
                    </div>
                </div>

                <div class="col-sm-12 col-md-6">
                    <div class="form-group">
                        <label id="url_label"><?= sprintf($this->language->admin_packages->input->annual_price, $this->settings->payment->currency) ?></label>
                        <input type="text" name="annual_price" class="form-control" />
                    </div>
                </div>

                <div class="col-12">
                    <div class="form-group">
                        <label><?= $this->language->admin_packages->input->is_enabled ?></label>
                        <select class="form-control" name="is_enabled">
                            <option value="1"><?= $this->language->global->yes ?></option>
                            <option value="0"><?= $this->language->global->no ?></option>
                        </select>
                    </div>
                </div>
            </div>

            <h2 class="card-title mt-3"><?= $this->language->admin_packages->header_settings ?></h2>
            <p class="text-muted"><?= $this->language->admin_packages->subheader_settings ?></p>

            <div class="form-group">
                <label for="projects_limit"><?= $this->language->admin_packages->input->projects_limit ?></label>
                <input type="number" id="projects_limit" name="projects_limit" min="-1" class="form-control" value="" />
                <small class="text-muted"><?= $this->language->admin_packages->input->projects_limit_help ?></small>
            </div>

            <div class="form-group">
                <label for="biolinks_limit"><?= $this->language->admin_packages->input->biolinks_limit ?></label>
                <input type="number" id="biolinks_limit" name="biolinks_limit" min="-1" class="form-control" value="" />
                <small class="text-muted"><?= $this->language->admin_packages->input->biolinks_limit_help ?></small>
            </div>

            <div class="form-group">
                <label for="links_limit"><?= $this->language->admin_packages->input->links_limit ?></label>
                <input type="number" id="links_limit" name="links_limit" min="-1" class="form-control" value="" />
                <small class="text-muted"><?= $this->language->admin_packages->input->links_limit_help ?></small>
            </div>

            <div class="custom-control custom-switch">
                <input id="no_ads" name="no_ads" type="checkbox" class="custom-control-input">
                <label class="custom-control-label" for="no_ads"><?= $this->language->admin_packages->input->no_ads ?></label>
                <div><small class="text-muted"><?= $this->language->admin_packages->input->no_ads_help ?></small></div>
            </div>

            <div class="custom-control custom-switch">
                <input id="removable_branding" name="removable_branding" type="checkbox" class="custom-control-input">
                <label class="custom-control-label" for="removable_branding"><?= $this->language->admin_packages->input->removable_branding ?></label>
                <div><small class="text-muted"><?= $this->language->admin_packages->input->removable_branding_help ?></small></div>
            </div>

            <div class="custom-control custom-switch">
                <input id="custom_branding" name="custom_branding" type="checkbox" class="custom-control-input">
                <label class="custom-control-label" for="custom_branding"><?= $this->language->admin_packages->input->custom_branding ?></label>
                <div><small class="text-muted"><?= $this->language->admin_packages->input->custom_branding_help ?></small></div>
            </div>

            <div class="custom-control custom-switch">
                <input id="custom_colored_links" name="custom_colored_links" type="checkbox" class="custom-control-input">
                <label class="custom-control-label" for="custom_colored_links"><?= $this->language->admin_packages->input->custom_colored_links ?></label>
                <div><small class="text-muted"><?= $this->language->admin_packages->input->custom_colored_links_help ?></small></div>
            </div>

            <div class="custom-control custom-switch">
                <input id="statistics" name="statistics" type="checkbox" class="custom-control-input">
                <label class="custom-control-label" for="statistics"><?= $this->language->admin_packages->input->statistics ?></label>
                <div><small class="text-muted"><?= $this->language->admin_packages->input->statistics_help ?></small></div>
            </div>

            <div class="custom-control custom-switch">
                <input id="google_analytics" name="google_analytics" type="checkbox" class="custom-control-input">
                <label class="custom-control-label" for="google_analytics"><?= $this->language->admin_packages->input->google_analytics ?></label>
                <div><small class="text-muted"><?= $this->language->admin_packages->input->google_analytics_help ?></small></div>
            </div>

            <div class="custom-control custom-switch">
                <input id="facebook_pixel" name="facebook_pixel" type="checkbox" class="custom-control-input">
                <label class="custom-control-label" for="facebook_pixel"><?= $this->language->admin_packages->input->facebook_pixel ?></label>
                <div><small class="text-muted"><?= $this->language->admin_packages->input->facebook_pixel_help ?></small></div>
            </div>

            <div class="custom-control custom-switch">
                <input id="custom_backgrounds" name="custom_backgrounds" type="checkbox" class="custom-control-input">
                <label class="custom-control-label" for="custom_backgrounds"><?= $this->language->admin_packages->input->custom_backgrounds ?></label>
                <div><small class="text-muted"><?= $this->language->admin_packages->input->custom_backgrounds_help ?></small></div>
            </div>

            <div class="custom-control custom-switch">
                <input id="verified" name="verified" type="checkbox" class="custom-control-input">
                <label class="custom-control-label" for="verified"><?= $this->language->admin_packages->input->verified ?></label>
                <div><small class="text-muted"><?= $this->language->admin_packages->input->verified_help ?></small></div>
            </div>

            <div class="custom-control custom-switch">
                <input id="scheduling" name="scheduling" type="checkbox" class="custom-control-input">
                <label class="custom-control-label" for="scheduling"><?= $this->language->admin_packages->input->scheduling ?></label>
                <div><small class="text-muted"><?= $this->language->admin_packages->input->scheduling_help ?></small></div>
            </div>

            <div class="text-center mt-3">
                <button type="submit" name="submit" class="btn btn-default"><?= $this->language->global->submit ?></button>
            </div>
        </form>

    </div>
</div>
