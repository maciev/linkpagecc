<?php defined('ALTUMCODE') || die() ?>

<div class="d-flex justify-content-between">
    <h1><span class="underline"><?= $this->language->admin_settings->header ?></span></h1>
</div>

<?php display_notifications() ?>

<div class="row mt-5">
    <div class="col  order-1 order-lg-0">
        <div class="card border-0 shadow-sm">
            <div class="card-body">

                <form action="" method="post" role="form" enctype="multipart/form-data">
                    <input type="hidden" name="token" value="<?= \Altum\Middlewares\Csrf::get() ?>" />

                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="main">
                            <div class="form-group">
                                <label><i class="fa fa-heading text-muted"></i> <?= $this->language->admin_settings->main->title ?></label>
                                <input type="text" name="title" class="form-control" value="<?= $this->settings->title ?>" />
                            </div>

                            <div class="form-group">
                                <label><i class="fa fa-language text-muted"></i> <?= $this->language->admin_settings->main->default_language ?></label>
                                <select name="default_language" class="form-control">
                                    <?php foreach(\Altum\Language::$languages as $value) echo '<option value="' . $value . '" ' . (($this->settings->default_language == $value) ? 'selected' : null) . '>' . $value . '</option>' ?>
                                </select>
                                <small class="text-muted"><?= $this->language->admin_settings->main->default_language_help ?></small>
                            </div>

                            <div class="form-group">
                                <label><i class="fa fa-eye text-muted"></i> <?= $this->language->admin_settings->main->logo ?></label>
                                <?php if($this->settings->logo != ''): ?>
                                    <div class="m-1">
                                        <img src="<?= url(UPLOADS_URL_PATH . 'logo/' . $this->settings->logo) ?>" class="img-fluid navbar-logo" />
                                    </div>
                                <?php endif ?>
                                <input id="logo-file-input" type="file" name="logo" class="form-control" />
                                <small class="text-muted"><?= $this->language->admin_settings->main->logo_help ?></small>
                                <small class="text-muted"><a href="admin/settings/removelogo<?= \Altum\Middlewares\Csrf::get_url_query() ?>"><?= $this->language->admin_settings->main->logo_remove ?></a></small>
                            </div>

                            <div class="form-group">
                                <label><i class="fa fa-icons text-muted"></i> <?= $this->language->admin_settings->main->favicon ?></label>
                                <?php if($this->settings->favicon != ''): ?>
                                    <div class="m-1">
                                        <img src="<?= url(UPLOADS_URL_PATH . 'favicon/' . $this->settings->favicon) ?>" class="img-fluid" />
                                    </div>
                                <?php endif ?>
                                <input id="favicon-file-input" type="file" name="favicon" class="form-control" />
                                <small class="text-muted"><?= $this->language->admin_settings->main->favicon_help ?></small>
                                <small class="text-muted"><a href="admin/settings/removefavicon<?= \Altum\Middlewares\Csrf::get_url_query() ?>"><?= $this->language->admin_settings->main->favicon_remove ?></a></small>
                            </div>

                            <div class="form-group">
                                <label><i class="fa fa-atlas text-muted"></i> <?= $this->language->admin_settings->main->time_zone ?></label>
                                <select name="time_zone" class="form-control">
                                    <?php foreach(DateTimeZone::listIdentifiers() as $time_zone) echo '<option value="' . $time_zone . '" ' . (($this->settings->time_zone == $time_zone) ? 'selected' : null) . '>' . $time_zone . '</option>' ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label><i class="fa fa-envelope text-muted"></i> <?= $this->language->admin_settings->main->email_confirmation ?></label>

                                <select class="custom-select" name="email_confirmation">
                                    <option value="1" <?= $this->settings->email_confirmation ? 'selected' : null ?>><?= $this->language->global->yes ?></option>
                                    <option value="0" <?= !$this->settings->email_confirmation ? 'selected' : null ?>><?= $this->language->global->no ?></option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label><i class="fa fa-users text-muted"></i> <?= $this->language->admin_settings->main->register_is_enabled ?></label>

                                <select class="custom-select" name="register_is_enabled">
                                    <option value="1" <?= $this->settings->register_is_enabled ? 'selected' : null ?>><?= $this->language->global->yes ?></option>
                                    <option value="0" <?= !$this->settings->register_is_enabled ? 'selected' : null ?>><?= $this->language->global->no ?></option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label><i class="fa fa-sitemap text-muted"></i> <?= $this->language->admin_settings->main->index_url ?></label>
                                <input type="text" name="index_url" class="form-control" value="<?= $this->settings->index_url ?>" />
                                <small class="text-muted"><?= $this->language->admin_settings->main->index_url_help ?></small>
                            </div>

                            <div class="form-group">
                                <label><i class="fa fa-file-word text-muted"></i> <?= $this->language->admin_settings->main->terms_and_conditions_url ?></label>
                                <input type="text" name="terms_and_conditions_url" class="form-control" value="<?= $this->settings->terms_and_conditions_url ?>" />
                                <small class="text-muted"><?= $this->language->admin_settings->main->terms_and_conditions_url_help ?></small>
                            </div>

                            <div class="form-group">
                                <label><i class="fa fa-file-word text-muted"></i> <?= $this->language->admin_settings->main->privacy_policy_url ?></label>
                                <input type="text" name="privacy_policy_url" class="form-control" value="<?= $this->settings->privacy_policy_url ?>" />
                                <small class="text-muted"><?= $this->language->admin_settings->main->privacy_policy_url_help ?></small>
                            </div>
                        </div>


                        <div class="tab-pane fade" id="links">
                            <div class="form-group">
                                <label><?= $this->language->admin_settings->links->blacklisted_domains ?></label>
                                <textarea class="form-control" name="links_blacklisted_domains"><?= implode(',', $this->settings->links->blacklisted_domains) ?></textarea>
                                <small class="text-muted"><?= $this->language->admin_settings->links->blacklisted_domains_help ?></small>
                            </div>

                            <div class="form-group">
                                <label><?= $this->language->admin_settings->links->blacklisted_keywords ?></label>
                                <textarea class="form-control" name="links_blacklisted_keywords"><?= implode(',', $this->settings->links->blacklisted_keywords) ?></textarea>
                                <small class="text-muted"><?= $this->language->admin_settings->links->blacklisted_keywords_help ?></small>
                            </div>

                            <hr class="my-3">

                            <h4><i class="fa fa-fish text-muted"></i> <?= $this->language->admin_settings->links->phishtank ?></h4>
                            <p class="text-muted"><?= $this->language->admin_settings->links->phishtank_help ?></p>

                            <div class="form-group">
                                <label><?= $this->language->admin_settings->links->phishtank_is_enabled ?></label>

                                <select name="links_phishtank_is_enabled" class="custom-select form-control">
                                    <option value="1" <?= $this->settings->links->phishtank_is_enabled ? 'selected' : null ?>><?= $this->language->global->yes ?></option>
                                    <option value="0" <?= !$this->settings->links->phishtank_is_enabled ? 'selected' : null ?>><?= $this->language->global->no ?></option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label><?= $this->language->admin_settings->links->phishtank_api_key ?></label>
                                <input type="text" name="links_phishtank_api_key" class="form-control" value="<?= $this->settings->links->phishtank_api_key ?>" />
                                <small class="text-muted"><?= $this->language->admin_settings->links->phishtank_api_key_help ?></small>
                            </div>

                            <hr class="my-3">

                            <h4><i class="fab fa-google text-muted"></i> <?= $this->language->admin_settings->links->google_safe_browsing ?></h4>
                            <p class="text-muted"><?= $this->language->admin_settings->links->google_safe_browsing_help ?></p>

                            <div class="form-group">
                                <label><?= $this->language->admin_settings->links->google_safe_browsing_is_enabled ?></label>

                                <select name="links_google_safe_browsing_is_enabled" class="custom-select form-control">
                                    <option value="1" <?= $this->settings->links->google_safe_browsing_is_enabled ? 'selected' : null ?>><?= $this->language->global->yes ?></option>
                                    <option value="0" <?= !$this->settings->links->google_safe_browsing_is_enabled ? 'selected' : null ?>><?= $this->language->global->no ?></option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label><?= $this->language->admin_settings->links->google_safe_browsing_api_key ?></label>
                                <input type="text" name="links_google_safe_browsing_api_key" class="form-control" value="<?= $this->settings->links->google_safe_browsing_api_key ?>" />
                                <small class="text-muted"><?= $this->language->admin_settings->links->google_safe_browsing_api_key_help ?></small>
                            </div>

                        </div>


                        <div class="tab-pane fade" id="payment">
                            <div class="alert alert-primary" role="alert">
                                You must own the Extended License in order use the payment system otherwise your license and the support can and will be suspended!
                            </div>

                            <div class="form-group">
                                <label><i class="fa fa-dollar-sign text-muted"></i> <?= $this->language->admin_settings->payment->is_enabled ?></label>

                                <select name="payment_is_enabled" class="custom-select form-control">
                                    <option value="1" <?= $this->settings->payment->is_enabled ? 'selected' : null ?>><?= $this->language->global->yes ?></option>
                                    <option value="0" <?= !$this->settings->payment->is_enabled ? 'selected' : null ?>><?= $this->language->global->no ?></option>
                                </select>
                                <small class="text-muted"><?= $this->language->admin_settings->payment->is_enabled_help ?></small>
                            </div>

                            <div class="form-group">
                                <label><i class="fa fa-copyright text-muted"></i> <?= $this->language->admin_settings->payment->brand_name ?></label>
                                <input type="text" name="payment_brand_name" class="form-control" value="<?= $this->settings->payment->brand_name ?>" />
                                <small class="text-muted"><?= $this->language->admin_settings->payment->brand_name_help ?></small>
                            </div>

                            <div class="form-group">
                                <label><i class="fa fa-coins text-muted"></i> <?= $this->language->admin_settings->payment->currency ?></label>
                                <input type="text" name="payment_currency" class="form-control" value="<?= $this->settings->payment->currency ?>" />
                                <small class="text-muted"><?= $this->language->admin_settings->payment->currency_help ?></small>
                            </div>

                            <hr class="my-3">

                            <h4><i class="fab fa-paypal icon-paypal text-muted"></i> <?= $this->language->admin_settings->payment->paypal ?></h4>
                            <p class="text-muted"><?= $this->language->admin_settings->payment->paypal_help ?></p>

                            <div class="form-group">
                                <label><?= $this->language->admin_settings->payment->paypal_is_enabled ?></label>

                                <select name="paypal_is_enabled" class="custom-select form-control">
                                    <option value="1" <?= $this->settings->paypal->is_enabled ? 'selected' : null ?>><?= $this->language->global->yes ?></option>
                                    <option value="0" <?= !$this->settings->paypal->is_enabled ? 'selected' : null ?>><?= $this->language->global->no ?></option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label><?= $this->language->admin_settings->payment->paypal_mode ?></label>

                                <select name="paypal_mode" class="custom-select form-control">
                                    <option value="live" <?= $this->settings->paypal->mode == 'live' ? 'selected' : null ?>>live</option>
                                    <option value="sandbox" <?= $this->settings->paypal->mode == 'sandbox' ? 'selected' : null ?>>sandbox</option>
                                </select>

                                <small class="text-muted"><?= $this->language->admin_settings->payment->paypal_mode_help ?></small>
                            </div>

                            <div class="form-group">
                                <label><?= $this->language->admin_settings->payment->paypal_client_id ?></label>
                                <input type="text" name="paypal_client_id" class="form-control" value="<?= $this->settings->paypal->client_id ?>" />
                            </div>

                            <div class="form-group">
                                <label><?= $this->language->admin_settings->payment->paypal_secret ?></label>
                                <input type="text" name="paypal_secret" class="form-control" value="<?= $this->settings->paypal->secret ?>" />
                            </div>

                            <hr class="my-3">

                            <h4><i class="fab fa-stripe icon-stripe text-muted"></i> <?= $this->language->admin_settings->payment->stripe ?></h4>
                            <p class="text-muted"><?= $this->language->admin_settings->payment->stripe_help ?></p>

                            <div class="form-group">
                                <label><?= $this->language->admin_settings->payment->stripe_is_enabled ?></label>

                                <select name="stripe_is_enabled" class="custom-select form-control">
                                    <option value="1" <?= $this->settings->stripe->is_enabled ? 'selected' : null ?>><?= $this->language->global->yes ?></option>
                                    <option value="0" <?= !$this->settings->stripe->is_enabled ? 'selected' : null ?>><?= $this->language->global->no ?></option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label><?= $this->language->admin_settings->payment->stripe_publishable_key ?></label>
                                <input type="text" name="stripe_publishable_key" class="form-control" value="<?= $this->settings->stripe->publishable_key ?>" />
                            </div>

                            <div class="form-group">
                                <label><?= $this->language->admin_settings->payment->stripe_secret_key ?></label>
                                <input type="text" name="stripe_secret_key" class="form-control" value="<?= $this->settings->stripe->secret_key ?>" />
                            </div>

                            <div class="form-group">
                                <label><?= $this->language->admin_settings->payment->stripe_webhook_secret ?></label>
                                <input type="text" name="stripe_webhook_secret" class="form-control" value="<?= $this->settings->stripe->webhook_secret ?>" />
                            </div>
                        </div>


                        <div class="tab-pane fade" id="business">
                            <h4><?= $this->language->admin_settings->business->header ?></h4>
                            <p class="text-muted"><?= $this->language->admin_settings->business->subheader ?></p>

                            <div class="form-group">
                                <label><?= $this->language->admin_settings->business->invoice_is_enabled ?></label>

                                <select name="business_invoice_is_enabled" class="custom-select form-control">
                                    <option value="1" <?= $this->settings->business->invoice_is_enabled ? 'selected' : null ?>><?= $this->language->global->yes ?></option>
                                    <option value="0" <?= !$this->settings->business->invoice_is_enabled ? 'selected' : null ?>><?= $this->language->global->no ?></option>
                                </select>

                                <small class="text-muted"><?= $this->language->admin_settings->business->invoice_is_enabled_help ?></small>
                            </div>

                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label><?= $this->language->admin_settings->business->name ?></label>
                                        <input type="text" name="business_name" class="form-control" value="<?= $this->settings->business->name ?>" />
                                    </div>

                                    <div class="form-group">
                                        <label><?= $this->language->admin_settings->business->email ?></label>
                                        <input type="text" name="business_email" class="form-control" value="<?= $this->settings->business->email ?>" />
                                    </div>

                                    <div class="form-group">
                                        <label><?= $this->language->admin_settings->business->phone ?></label>
                                        <input type="text" name="business_phone" class="form-control" value="<?= $this->settings->business->phone ?>" />
                                    </div>

                                    <div class="form-group">
                                        <label><?= $this->language->admin_settings->business->tax_type ?></label>
                                        <input type="text" name="business_tax_type" class="form-control" value="<?= $this->settings->business->tax_type ?>" placeholder="<?= $this->language->admin_settings->business->tax_type_placeholder ?>" />
                                    </div>

                                    <div class="form-group">
                                        <label><?= $this->language->admin_settings->business->tax_id ?></label>
                                        <input type="text" name="business_tax_id" class="form-control" value="<?= $this->settings->business->tax_id ?>" />
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="form-group">
                                        <label><?= $this->language->admin_settings->business->address ?></label>
                                        <input type="text" name="business_address" class="form-control" value="<?= $this->settings->business->address ?>" />
                                    </div>

                                    <div class="form-group">
                                        <label><?= $this->language->admin_settings->business->city ?></label>
                                        <input type="text" name="business_city" class="form-control" value="<?= $this->settings->business->city ?>" />
                                    </div>

                                    <div class="form-group">
                                        <label><?= $this->language->admin_settings->business->county ?></label>
                                        <input type="text" name="business_county" class="form-control" value="<?= $this->settings->business->county ?>" />
                                    </div>

                                    <div class="form-group">
                                        <label><?= $this->language->admin_settings->business->zip ?></label>
                                        <input type="number" name="business_zip" class="form-control" value="<?= $this->settings->business->zip ?>" />
                                    </div>

                                    <div class="form-group">
                                        <label><?= $this->language->admin_settings->business->country ?></label>
                                        <input type="text" name="business_country" class="form-control" value="<?= $this->settings->business->country ?>" />
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="tab-pane fade" id="ads">
                            <p class="text-muted"><?= $this->language->admin_settings->ads->ads_help ?></p>

                            <div class="form-group">
                                <label><?= $this->language->admin_settings->ads->header ?></label>
                                <textarea class="form-control" name="ads_header"><?= $this->settings->ads->header ?></textarea>
                            </div>

                            <div class="form-group">
                                <label><?= $this->language->admin_settings->ads->footer ?></label>
                                <textarea class="form-control" name="ads_footer"><?= $this->settings->ads->footer ?></textarea>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="captcha">
                            <h4><i class="fab fa-google text-muted"></i> <?= $this->language->admin_settings->captcha->recaptcha ?></h4>

                            <div class="form-group">
                                <label><?= $this->language->admin_settings->captcha->recaptcha_is_enabled ?></label>

                                <select name="captcha_recaptcha_is_enabled" class="custom-select form-control">
                                    <option value="1" <?= $this->settings->captcha->recaptcha_is_enabled ? 'selected' : null ?>><?= $this->language->global->yes ?></option>
                                    <option value="0" <?= !$this->settings->captcha->recaptcha_is_enabled ? 'selected' : null ?>><?= $this->language->global->no ?></option>
                                </select>

                                <small class="text-muted"><?= $this->language->admin_settings->captcha->recaptcha_is_enabled_help ?></small>
                            </div>

                            <div class="form-group">
                                <label><?= $this->language->admin_settings->captcha->recaptcha_public_key ?></label>
                                <input type="text" name="captcha_recaptcha_public_key" class="form-control" value="<?= $this->settings->captcha->recaptcha_public_key ?>" />
                            </div>

                            <div class="form-group">
                                <label><?= $this->language->admin_settings->captcha->recaptcha_private_key ?></label>
                                <input type="text" name="captcha_recaptcha_private_key" class="form-control" value="<?= $this->settings->captcha->recaptcha_private_key ?>" />
                            </div>
                        </div>


                        <div class="tab-pane fade" id="facebook">
                            <div class="form-group">
                                <label><?= $this->language->admin_settings->facebook->is_enabled ?></label>

                                <select name="facebook_is_enabled" class="custom-select form-control">
                                    <option value="1" <?= $this->settings->facebook->is_enabled ? 'selected' : null ?>><?= $this->language->global->yes ?></option>
                                    <option value="0" <?= !$this->settings->facebook->is_enabled ? 'selected' : null ?>><?= $this->language->global->no ?></option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label><?= $this->language->admin_settings->facebook->app_id ?></label>
                                <input type="text" name="facebook_app_id" class="form-control" value="<?= $this->settings->facebook->app_id ?>" />
                            </div>

                            <div class="form-group">
                                <label><?= $this->language->admin_settings->facebook->app_secret ?></label>
                                <input type="text" name="facebook_app_secret" class="form-control" value="<?= $this->settings->facebook->app_secret ?>" />
                            </div>
                        </div>


                        <div class="tab-pane fade" id="instagram">
                            <div class="form-group">
                                <label><?= $this->language->admin_settings->instagram->is_enabled ?></label>

                                <select name="instagram_is_enabled" class="custom-select form-control">
                                    <option value="1" <?= $this->settings->instagram->is_enabled ? 'selected' : null ?>><?= $this->language->global->yes ?></option>
                                    <option value="0" <?= !$this->settings->instagram->is_enabled ? 'selected' : null ?>><?= $this->language->global->no ?></option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label><?= $this->language->admin_settings->instagram->client_id ?></label>
                                <input type="text" name="instagram_client_id" class="form-control" value="<?= $this->settings->instagram->client_id ?>" />
                            </div>

                            <div class="form-group">
                                <label><?= $this->language->admin_settings->instagram->client_secret ?></label>
                                <input type="text" name="instagram_client_secret" class="form-control" value="<?= $this->settings->instagram->client_secret ?>" />
                            </div>
                        </div>


                        <div class="tab-pane fade" id="socials">
                            <p class="text-muted"><?= $this->language->admin_settings->socials->socials_help ?></p>

                            <div class="form-group">
                                <label><i class="fab fa-facebook text-muted"></i> <?= $this->language->admin_settings->socials->facebook ?></label>
                                <input type="text" name="socials_facebook" class="form-control" value="<?= $this->settings->socials->facebook ?>" />
                            </div>

                            <div class="form-group">
                                <label><i class="fab fa-twitter text-muted"></i> <?= $this->language->admin_settings->socials->twitter ?></label>
                                <input type="text" name="socials_twitter" class="form-control" value="<?= $this->settings->socials->twitter ?>" />
                            </div>

                            <div class="form-group">
                                <label><i class="fab fa-youtube text-muted"></i> <?= $this->language->admin_settings->socials->youtube ?></label>
                                <input type="text" name="socials_youtube" class="form-control" value="<?= $this->settings->socials->youtube ?>" />
                            </div>

                            <div class="form-group">
                                <label><i class="fab fa-instagram text-muted"></i> <?= $this->language->admin_settings->socials->instagram ?></label>
                                <input type="text" name="socials_instagram" class="form-control" value="<?= $this->settings->socials->instagram ?>" />
                            </div>

                        </div>


                        <div class="tab-pane fade" id="smtp">

                            <div class="form-group">
                                <label><?= $this->language->admin_settings->smtp->host ?></label>
                                <input type="text" name="smtp_host" class="form-control" value="<?= $this->settings->smtp->host ?>" />
                                <small class="form-text text-muted"><?= $this->language->admin_settings->smtp->host_help ?></small>
                            </div>

                            <div class="form-group">
                                <label><?= $this->language->admin_settings->smtp->from ?></label>
                                <input type="text" name="smtp_from" class="form-control" value="<?= $this->settings->smtp->from ?>" />
                                <small class="form-text text-muted"><?= $this->language->admin_settings->smtp->from_help ?></small>
                            </div>

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label><?= $this->language->admin_settings->smtp->encryption ?></label>
                                        <select name="smtp_encryption" class="custom-select form-control">
                                            <option value="0" <?= $this->settings->smtp->encryption == '0' ? 'selected' : null ?>>None</option>
                                            <option value="ssl" <?= $this->settings->smtp->encryption == 'ssl' ? 'selected' : null ?>>SSL</option>
                                            <option value="tls" <?= $this->settings->smtp->encryption == 'tls' ? 'selected' : null ?>>TLS</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-9">
                                    <div class="form-group">
                                        <label><?= $this->language->admin_settings->smtp->port ?></label>
                                        <input type="text" name="smtp_port" class="form-control" value="<?= $this->settings->smtp->port ?>" />
                                    </div>
                                </div>
                            </div>

                            <div class="form-check">
                                <label class="form-check-label">
                                    <input class="form-check-input" name="smtp_auth" type="checkbox" value="" <?= $this->settings->smtp->auth ? 'checked' : null ?>>
                                    <?= $this->language->admin_settings->smtp->auth ?>
                                </label>
                            </div>

                            <div class="form-group">
                                <label><?= $this->language->admin_settings->smtp->username ?></label>
                                <input type="text" name="smtp_username" class="form-control" value="<?= $this->settings->smtp->username ?>" <?= ($this->settings->smtp->auth) ? null : 'disabled' ?>/>
                            </div>

                            <div class="form-group">
                                <label><?= $this->language->admin_settings->smtp->password ?></label>
                                <input type="password" name="smtp_password" class="form-control" value="<?= $this->settings->smtp->password ?>" <?= ($this->settings->smtp->auth) ? null : 'disabled' ?>/>
                            </div>

                            <div class="">
                                <a href="admin/settings/testemail<?= \Altum\Middlewares\Csrf::get_url_query() ?>" class="btn btn-info"><?= $this->language->admin_settings->button->test_email ?></a>
                                <small class="form-text text-muted"><?= $this->language->admin_settings->button->test_email_help ?></small>
                            </div>

                        </div>


                        <div class="tab-pane fade" id="custom">
                            <div class="form-group">
                                <label><i class="fab fa-js text-muted"></i> <?= $this->language->admin_settings->custom->head_js ?></label>
                                <textarea class="form-control" name="custom_head_js"><?= $this->settings->custom->head_js ?></textarea>
                                <small class="text-muted"><?= $this->language->admin_settings->custom->head_js_help ?></small>
                            </div>

                            <div class="form-group">
                                <label><i class="fab fa-css3 text-muted"></i> <?= $this->language->admin_settings->custom->head_css ?></label>
                                <textarea class="form-control" name="custom_head_css"><?= $this->settings->custom->head_css ?></textarea>
                                <small class="text-muted"><?= $this->language->admin_settings->custom->head_css_help ?></small>
                            </div>
                        </div>


                        <div class="tab-pane fade" id="email_notifications">

                            <div class="form-group">
                                <label><?= $this->language->admin_settings->email_notifications->emails ?></label>
                                <textarea class="form-control" name="email_notifications_emails" rows="5"><?= $this->settings->email_notifications->emails ?></textarea>
                                <small class="form-text text-muted"><?= $this->language->admin_settings->email_notifications->emails_help ?></small>
                            </div>

                            <div class="form-check">
                                <label class="form-check-label">
                                    <input type="checkbox" class="form-check-input" name="email_notifications_new_user" <?= $this->settings->email_notifications->new_user ? 'checked' : null?>>
                                    <?= $this->language->admin_settings->email_notifications->new_user ?>
                                </label>

                                <small class="form-text text-muted"><?= $this->language->admin_settings->email_notifications->new_user_help ?></small>
                            </div>

                            <div class="form-check">
                                <label class="form-check-label">
                                    <input type="checkbox" class="form-check-input" name="email_notifications_new_payment" <?= $this->settings->email_notifications->new_payment ? 'checked' : null?>>
                                    <?= $this->language->admin_settings->email_notifications->new_payment ?>
                                </label>

                                <small class="form-text text-muted"><?= $this->language->admin_settings->email_notifications->new_payment_help ?></small>
                            </div>

                        </div>


                        <div class="text-center mt-3">
                            <button type="submit" name="submit" class="btn btn-default"><?= $this->language->global->submit ?></button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <div class="mb-5 mb-lg-0 col-12 col-lg-3 order-0 order-lg-1">
        <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
            <a class="nav-link active" href="#main" data-toggle="pill" role="tab"><i class="fa fa-home"></i> <?= $this->language->admin_settings->tab->main ?></a>
            <a class="nav-link" href="#links" data-toggle="pill" role="tab"><i class="fa fa-link"></i> <?= $this->language->admin_settings->tab->links ?></a>
            <a class="nav-link" href="#payment" data-toggle="pill" role="tab"><i class="fa fa-dollar-sign"></i> <?= $this->language->admin_settings->tab->payment ?></a>
            <a class="nav-link" href="#business" data-toggle="pill" role="tab"><i class="fa fa-briefcase"></i> <?= $this->language->admin_settings->tab->business ?></a>
            <a class="nav-link" href="#captcha" data-toggle="pill" role="tab"><i class="fa fa-low-vision"></i> <?= $this->language->admin_settings->tab->captcha ?></a>
            <a class="nav-link" href="#facebook" data-toggle="pill" role="tab"><i class="fab fa-facebook"></i> <?= $this->language->admin_settings->tab->facebook ?></a>
            <a class="nav-link" href="#instagram" data-toggle="pill" role="tab"><i class="fab fa-instagram"></i> <?= $this->language->admin_settings->tab->instagram ?></a>
            <a class="nav-link" href="#ads" data-toggle="pill" role="tab"><i class="fa fa-ad"></i> <?= $this->language->admin_settings->tab->ads ?></a>
            <a class="nav-link" href="#socials" data-toggle="pill" role="tab"><i class="fab fa-instagram"></i> <?= $this->language->admin_settings->tab->socials ?></a>
            <a class="nav-link" href="#smtp" data-toggle="pill" role="tab"><i class="fa fa-mail-bulk"></i> <?= $this->language->admin_settings->tab->smtp ?></a>
            <a class="nav-link" href="#custom" data-toggle="pill" role="tab"><i class="fa fa-paint-brush"></i> <?= $this->language->admin_settings->tab->custom ?></a>
            <a class="nav-link" href="#email_notifications" data-toggle="pill" role="tab"><i class="fa fa-bell"></i> <?= $this->language->admin_settings->tab->email_notifications ?></a>
        </div>
    </div>
</div>


<?php ob_start() ?>
<script>
    $(document).ready(() => {

        $('input[name="smtp_auth"]').on('change', (event) => {

            if($(event.currentTarget).is(':checked')) {
                $('input[name="smtp_username"],input[name="smtp_password"]').removeAttr('disabled');
            } else {
                $('input[name="smtp_username"],input[name="smtp_password"]').attr('disabled', 'true');
            }

        })
    })
</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
