<?php defined('ALTUMCODE') || die() ?>

<?php require THEME_PATH . 'views/partials/ads_header.php' ?>

<div class="container">
    <div class="d-flex justify-content-center">
        <div class="col-md-10 col-lg-8">

            <?php display_notifications() ?>

            <h2><?= sprintf($this->language->pay->header, $data->package->name) ?></h2>
            <div class="text-muted mb-5"><?= $this->language->pay->subheader ?></div>


            <?php if($data->package_id == 'free'): ?>

                <?php if($this->user->package_id == 'free'): ?>

                <div class="alert alert-info" role="alert"><?= $this->language->pay->free->free_already ?></div>

                <div class="text-center mt-5">
                    <a href="<?= url('package') ?>" class="btn btn-primary"><?= $this->language->pay->free->choose_another_package ?></a>
                </div>

            <?php else: ?>

                <div class="alert alert-info" role="alert"><?= $this->language->pay->free->other_package_not_expired ?></div>

                <div class="text-center mt-5">
                    <a href="<?= url('package') ?>" class="btn btn-primary"><?= $this->language->pay->free->choose_another_package ?></a>
                </div>
            <?php endif ?>

            <?php elseif($data->package_id == 'trial'): ?>

            <?php if($this->user->package_trial_done): ?>

                <div class="alert alert-warning" role="alert"><?= $this->language->pay->trial->trial_done ?></div>

                <div class="text-center mt-5">
                    <a href="<?= url('package') ?>" class="btn btn-primary"><?= $this->language->pay->trial->choose_another_package ?></a>
                </div>

            <?php else: ?>

                <?php if($this->user->package_id != 'free' && !$this->user->package_is_expired): ?>

                <div class="alert alert-info" role="alert"><?= $this->language->pay->trial->other_package_not_expired ?></div>

            <?php endif ?>

                <form action="<?= 'pay/' . $data->package_id ?>" method="post" role="form">
                    <input type="hidden" name="token" value="<?= \Altum\Middlewares\Csrf::get() ?>" />

                    <div class="text-center mt-5">
                        <button type="submit" name="submit" class="btn btn-primary"><i class="fa fa-stopwatch"></i> <?= $this->language->pay->trial->trial_start ?></button>
                    </div>

                </form>

            <?php endif ?>

            <?php else: ?>

            <?php
            /* Check for extra savings on the prices */
            $annual_price_savings = ceil(($data->package->monthly_price * 12) - $data->package->annual_price);

            ?>

                <div class="margin-top-6 mb-5"><i class="fa fa-box-open mr-3"></i> <span class="h5 text-muted"><?= $this->language->pay->custom_package->payment_plan ?></span></div>

                <form action="<?= 'pay/' . $data->package_id ?>" method="post" role="form">
                    <input type="hidden" name="token" value="<?= \Altum\Middlewares\Csrf::get() ?>" />

                    <div class="row d-flex align-items-stretch">
                        <label class="col-6 custom-radio-box">

                            <input type="radio" id="monthly_price" name="payment_plan" value="monthly" class="custom-control-input" required="required">

                            <div class="card card-shadow zoomer h-100">
                                <div class="card-body">

                                    <div class="card-title text-center"><?= $this->language->pay->custom_package->monthly ?></div>

                                    <div class="mt-3 text-center">
                                        <span class="custom-radio-box-main-text"><?= $data->package->monthly_price ?></span> <span><?= $this->settings->payment->currency ?></span>
                                    </div>

                                </div>
                            </div>

                        </label>

                        <label class="col-6 custom-radio-box">

                            <input type="radio" id="annual_price" name="payment_plan" value="annual" class="custom-control-input" required="required">

                            <div class="card card-shadow zoomer h-100">
                                <div class="card-body">

                                    <div class="card-title text-center"><?= $this->language->pay->custom_package->annual ?></div>

                                    <div class="mt-3 text-center">
                                        <span class="custom-radio-box-main-text"><?= $data->package->annual_price ?></span> <span><?= $this->settings->payment->currency ?></span>
                                        <div class="text-muted">
                                            <small><?= sprintf($this->language->pay->custom_package->annual_savings, $annual_price_savings, $this->settings->payment->currency) ?></small>
                                        </div>
                                    </div>

                                </div>
                            </div>

                        </label>
                    </div>


                    <div class="margin-top-6 mb-5"><i class="fa fa-money-check-alt mr-3"></i> <span class="h5 text-muted"><?= $this->language->pay->custom_package->payment_processor ?></span></div>

                    <?php if(!$this->settings->paypal->is_enabled && !$this->settings->stripe->is_enabled): ?>
                        <div class="alert alert-info" role="alert">
                            <?= $this->language->pay->custom_package->no_processor ?>
                        </div>
                    <?php endif ?>

                    <div class="row d-flex align-items-stretch">

                        <?php if($this->settings->paypal->is_enabled): ?>
                            <label class="col-6 custom-radio-box">

                                <input type="radio" id="paypal_processor" name="payment_processor" value="paypal" class="custom-control-input" required="required">

                                <div class="card card-shadow zoomer h-100">
                                    <div class="card-body">

                                        <div class="card-title text-center"><?= $this->language->pay->custom_package->paypal_processor ?></div>

                                        <div class="mt-3 text-center">
                                            <span class="custom-radio-box-main-icon"><i class="fab fa-paypal"></i></span>
                                        </div>

                                    </div>
                                </div>

                            </label>
                        <?php endif ?>

                        <?php if($this->settings->stripe->is_enabled): ?>
                            <label class="col-6 custom-radio-box">

                                <input type="radio" id="stripe_processor" name="payment_processor" value="stripe" class="custom-control-input" required="required">

                                <div class="card card-shadow zoomer h-100">
                                    <div class="card-body">

                                        <div class="card-title text-center"><?= $this->language->pay->custom_package->stripe_processor ?></div>

                                        <div class="mt-3 text-center">
                                            <span class="custom-radio-box-main-icon"><i class="fab fa-stripe"></i></span>
                                        </div>

                                    </div>
                                </div>

                            </label>
                        <?php endif ?>
                    </div>

                    <div class="margin-top-6 mb-5"><i class="fa fa-dollar-sign mr-3"></i> <span class="h5 text-muted"><?= $this->language->pay->custom_package->payment_type ?></span></div>

                    <div class="row d-flex align-items-stretch">
                        <label class="col-6 custom-radio-box">

                            <input type="radio" id="one-time_type" name="payment_type" value="one-time" class="custom-control-input" required="required">

                            <div class="card card-shadow zoomer h-100">
                                <div class="card-body">

                                    <div class="card-title text-center"><?= $this->language->pay->custom_package->one_time_type ?></div>

                                    <div class="mt-3 text-center">
                                        <span class="custom-radio-box-main-icon"><i class="fa fa-hand-holding-usd"></i></span>
                                    </div>

                                </div>
                            </div>

                        </label>

                        <label class="col-6 custom-radio-box" id="recurring_type_label">

                            <input type="radio" id="recurring_type" name="payment_type" value="recurring" class="custom-control-input" required="required">

                            <div class="card card-shadow zoomer h-100">
                                <div class="card-body">

                                    <div class="card-title text-center"><?= $this->language->pay->custom_package->recurring_type ?></div>

                                    <div class="mt-3 text-center">
                                        <span class="custom-radio-box-main-icon"><i class="fa fa-sync-alt"></i></span>
                                    </div>

                                </div>
                            </div>

                        </label>
                    </div>

                    <div class="margin-top-3 form-check">
                        <label class="form-check-label">
                            <input class="form-check-input" name="accept" type="checkbox" required="required">
                            <?= sprintf(
                                $this->language->pay->accept,
                                '<a href="' . $this->settings->terms_and_conditions_url . '">' . $this->language->register->form->terms_and_conditions . '</a>',
                                '<a href="' . $this->settings->privacy_policy_url . '">' . $this->language->register->form->privacy_policy . '</a>'
                            ) ?>
                        </label>
                    </div>

                    <div class="text-center margin-top-6">
                        <button type="submit" name="submit" class="btn btn-primary"><?= $this->language->pay->custom_package->pay ?></button>
                    </div>
                </form>


            <?php

            /* Include only if the stripe redirect session was generated */
            if($data->stripe_session):

            ?>
                <script src="https://js.stripe.com/v3/"></script>

                <script>
                    let stripe = Stripe(<?= json_encode($this->settings->stripe->publishable_key) ?>);

                    stripe.redirectToCheckout({
                        sessionId: <?= json_encode($data->stripe_session->id) ?>,
                    }).then((result) => {

                        /* Nothing for the moment */

                    });
                </script>

            <?php endif ?>

            <?php endif ?>

        </div>
    </div>
</div>
