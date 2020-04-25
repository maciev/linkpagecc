<?php defined('ALTUMCODE') || die() ?>

<?php

use Altum\Middlewares\Authentication;

?>

<?php $packages_result = $this->database->query("SELECT * FROM `packages` WHERE `is_enabled` = '1'"); ?>

<div class="pricing pricing-palden">

    <?php

    /* Detect if we need to show the Free plan to the user */
    if($this->settings->package_free->is_enabled):

        ?>
        <div class="pricing-item zoomer-shadow">
            <div class="pricing-deco">
                <h3 class="pricing-title"><?= $this->settings->package_free->name ?></h3>

                <svg class="pricing-deco-img" enable-background="new 0 0 300 100" height="100px" id="Layer_1" preserveAspectRatio="none" version="1.1" viewBox="0 0 300 100" width="300px" x="0px" xml:space="preserve" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns="http://www.w3.org/2000/svg" y="0px">
                    <path class="deco-layer deco-layer--1" d="M30.913,43.944c0,0,42.911-34.464,87.51-14.191c77.31,35.14,113.304-1.952,146.638-4.729&#x000A;	c48.654-4.056,69.94,16.218,69.94,16.218v54.396H30.913V43.944z" fill="#FFFFFF" opacity="0.6"></path>
                    <path class="deco-layer deco-layer--2" d="M-35.667,44.628c0,0,42.91-34.463,87.51-14.191c77.31,35.141,113.304-1.952,146.639-4.729&#x000A;	c48.653-4.055,69.939,16.218,69.939,16.218v54.396H-35.667V44.628z" fill="#FFFFFF" opacity="0.6"></path>
                    <path class="deco-layer deco-layer--3" d="M43.415,98.342c0,0,48.283-68.927,109.133-68.927c65.886,0,97.983,67.914,97.983,67.914v3.716&#x000A;	H42.401L43.415,98.342z" fill="#FFFFFF" opacity="0.7"></path>
                    <path class="deco-layer deco-layer--4" d="M-34.667,62.998c0,0,56-45.667,120.316-27.839C167.484,57.842,197,41.332,232.286,30.428&#x000A;	c53.07-16.399,104.047,36.903,104.047,36.903l1.333,36.667l-372-2.954L-34.667,62.998z" fill="#FFFFFF"></path>
                </svg>

                <div class="pricing-price">
                    <?= $this->language->package->free->price ?>
                </div>

                <div class="pricing-sub">&nbsp;</div>
            </div>

            <ul class="pricing-feature-list">
                <?php foreach($data->simple_package_settings as $package_setting): ?>
                    <?php if($this->settings->package_free->settings->{$package_setting}): ?>
                        <li class="pricing-feature"><?= $this->language->global->package_settings->{$package_setting} ?></li>
                    <?php endif ?>
                <?php endforeach ?>

                <?php if($this->settings->package_free->settings->projects_limit == -1): ?>
                    <li class="pricing-feature"><?= $this->language->global->package_settings->unlimited_projects_limit ?></li>
                <?php else: ?>
                    <li class="pricing-feature"><?= sprintf($this->language->global->package_settings->projects_limit, $this->settings->package_free->settings->projects_limit) ?></li>
                <?php endif ?>

                <?php if($this->settings->package_free->settings->biolinks_limit == -1): ?>
                    <li class="pricing-feature"><?= $this->language->global->package_settings->unlimited_biolinks_limit ?></li>
                <?php else: ?>
                    <li class="pricing-feature"><?= sprintf($this->language->global->package_settings->biolinks_limit, $this->settings->package_free->settings->biolinks_limit) ?></li>
                <?php endif ?>

                <?php if($this->settings->package_free->settings->links_limit == -1): ?>
                    <li class="pricing-feature"><?= $this->language->global->package_settings->unlimited_links_limit ?></li>
                <?php else: ?>
                    <li class="pricing-feature"><?= sprintf($this->language->global->package_settings->links_limit, $this->settings->package_free->settings->links_limit) ?></li>
                <?php endif ?>
            </ul>

            <?php if(Authentication::check() && $this->user->package_id == 'free'): ?>
                <button class="pricing-action-disabled"><?= $this->language->package->button->already_free ?></button>
            <?php else: ?>
                <a href="<?= Authentication::check() ? url('pay/free') : url('register?redirect=pay/free') ?>" class="pricing-action"><?= $this->language->package->button->choose ?></a>
            <?php endif ?>
        </div>

    <?php endif ?>

    <?php if($this->settings->payment->is_enabled): ?>

    <?php

    /* Detect if we need to show the Free plan to the user */
    if($this->settings->package_trial->is_enabled):

        ?>
        <div class="pricing-item zoomer-shadow">
            <div class="pricing-deco">
                <h3 class="pricing-title"><?= $this->settings->package_trial->name ?></h3>

                <svg class="pricing-deco-img" enable-background="new 0 0 300 100" height="100px" id="Layer_1" preserveAspectRatio="none" version="1.1" viewBox="0 0 300 100" width="300px" x="0px" xml:space="preserve" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns="http://www.w3.org/2000/svg" y="0px">
                    <path class="deco-layer deco-layer--1" d="M30.913,43.944c0,0,42.911-34.464,87.51-14.191c77.31,35.14,113.304-1.952,146.638-4.729&#x000A;	c48.654-4.056,69.94,16.218,69.94,16.218v54.396H30.913V43.944z" fill="#FFFFFF" opacity="0.6"></path>
                    <path class="deco-layer deco-layer--2" d="M-35.667,44.628c0,0,42.91-34.463,87.51-14.191c77.31,35.141,113.304-1.952,146.639-4.729&#x000A;	c48.653-4.055,69.939,16.218,69.939,16.218v54.396H-35.667V44.628z" fill="#FFFFFF" opacity="0.6"></path>
                    <path class="deco-layer deco-layer--3" d="M43.415,98.342c0,0,48.283-68.927,109.133-68.927c65.886,0,97.983,67.914,97.983,67.914v3.716&#x000A;	H42.401L43.415,98.342z" fill="#FFFFFF" opacity="0.7"></path>
                    <path class="deco-layer deco-layer--4" d="M-34.667,62.998c0,0,56-45.667,120.316-27.839C167.484,57.842,197,41.332,232.286,30.428&#x000A;	c53.07-16.399,104.047,36.903,104.047,36.903l1.333,36.667l-372-2.954L-34.667,62.998z" fill="#FFFFFF"></path>
                </svg>

                <div class="pricing-price">
                    <?= $this->language->package->trial->price ?>
                </div>

                <div class="pricing-sub">&nbsp;</div>
            </div>

            <ul class="pricing-feature-list">
                <?php foreach($data->simple_package_settings as $package_setting): ?>
                    <?php if($this->settings->package_trial->settings->{$package_setting}): ?>
                        <li class="pricing-feature"><?= $this->language->global->package_settings->{$package_setting} ?></li>
                    <?php endif ?>
                <?php endforeach ?>

                <?php if($this->settings->package_trial->settings->projects_limit == -1): ?>
                    <li class="pricing-feature"><?= $this->language->global->package_settings->unlimited_projects_limit ?></li>
                <?php else: ?>
                    <li class="pricing-feature"><?= sprintf($this->language->global->package_settings->projects_limit, $this->settings->package_trial->settings->projects_limit) ?></li>
                <?php endif ?>

                <?php if($this->settings->package_trial->settings->biolinks_limit == -1): ?>
                    <li class="pricing-feature"><?= $this->language->global->package_settings->unlimited_biolinks_limit ?></li>
                <?php else: ?>
                    <li class="pricing-feature"><?= sprintf($this->language->global->package_settings->biolinks_limit, $this->settings->package_trial->settings->biolinks_limit) ?></li>
                <?php endif ?>

                <?php if($this->settings->package_trial->settings->links_limit == -1): ?>
                    <li class="pricing-feature"><?= $this->language->global->package_settings->unlimited_links_limit ?></li>
                <?php else: ?>
                    <li class="pricing-feature"><?= sprintf($this->language->global->package_settings->links_limit, $this->settings->package_trial->settings->links_limit) ?></li>
                <?php endif ?>
            </ul>

            <?php if(Authentication::check() && $this->user->package_trial_done): ?>
                <button class="pricing-action-disabled"><?= $this->language->package->button->disabled ?></button>
            <?php else: ?>
                <a href="<?= Authentication::check() ? url('pay/trial') : url('register?redirect=pay/trial') ?>" class="pricing-action"><?= $this->language->package->button->choose ?></a>
            <?php endif ?>
        </div>

    <?php endif ?>

    <?php while($package = $packages_result->fetch_object()): ?>
        <?php $package->settings = json_decode($package->settings) ?>
        <div class="pricing-item zoomer-shadow">
            <div class="pricing-deco">
                <h3 class="pricing-title"><?= $package->name ?></h3>

                <svg class="pricing-deco-img" enable-background="new 0 0 300 100" height="100px" id="Layer_1" preserveAspectRatio="none" version="1.1" viewBox="0 0 300 100" width="300px" x="0px" xml:space="preserve" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns="http://www.w3.org/2000/svg" y="0px">
                    <path class="deco-layer deco-layer--1" d="M30.913,43.944c0,0,42.911-34.464,87.51-14.191c77.31,35.14,113.304-1.952,146.638-4.729&#x000A;	c48.654-4.056,69.94,16.218,69.94,16.218v54.396H30.913V43.944z" fill="#FFFFFF" opacity="0.6"></path>
                    <path class="deco-layer deco-layer--2" d="M-35.667,44.628c0,0,42.91-34.463,87.51-14.191c77.31,35.141,113.304-1.952,146.639-4.729&#x000A;	c48.653-4.055,69.939,16.218,69.939,16.218v54.396H-35.667V44.628z" fill="#FFFFFF" opacity="0.6"></path>
                    <path class="deco-layer deco-layer--3" d="M43.415,98.342c0,0,48.283-68.927,109.133-68.927c65.886,0,97.983,67.914,97.983,67.914v3.716&#x000A;	H42.401L43.415,98.342z" fill="#FFFFFF" opacity="0.7"></path>
                    <path class="deco-layer deco-layer--4" d="M-34.667,62.998c0,0,56-45.667,120.316-27.839C167.484,57.842,197,41.332,232.286,30.428&#x000A;	c53.07-16.399,104.047,36.903,104.047,36.903l1.333,36.667l-372-2.954L-34.667,62.998z" fill="#FFFFFF"></path>
                </svg>

                <div class="pricing-price">
                    <span class="pricing-currency"><?= $this->settings->payment->currency ?></span>
                    <?= $package->monthly_price ?>
                    <span class='pricing-period'><?= $this->language->package->display->per_month ?></span>
                </div>

                <div class="pricing-sub"><?= sprintf($this->language->package->display->annual_price, $package->annual_price, $this->settings->payment->currency) ?></div>
            </div>

            <ul class="pricing-feature-list">
                <?php foreach($data->simple_package_settings as $package_setting): ?>
                    <?php if($package->settings->{$package_setting}): ?>
                        <li class="pricing-feature"><?= $this->language->global->package_settings->{$package_setting} ?></li>
                    <?php endif ?>
                <?php endforeach ?>

                <?php if($package->settings->projects_limit == -1): ?>
                    <li class="pricing-feature"><?= $this->language->global->package_settings->unlimited_projects_limit ?></li>
                <?php else: ?>
                    <li class="pricing-feature"><?= sprintf($this->language->global->package_settings->projects_limit, $package->settings->projects_limit) ?></li>
                <?php endif ?>

                <?php if($package->settings->biolinks_limit == -1): ?>
                    <li class="pricing-feature"><?= $this->language->global->package_settings->unlimited_biolinks_limit ?></li>
                <?php else: ?>
                    <li class="pricing-feature"><?= sprintf($this->language->global->package_settings->biolinks_limit, $package->settings->biolinks_limit) ?></li>
                <?php endif ?>

                <?php if($package->settings->links_limit == -1): ?>
                    <li class="pricing-feature"><?= $this->language->global->package_settings->unlimited_links_limit ?></li>
                <?php else: ?>
                    <li class="pricing-feature"><?= sprintf($this->language->global->package_settings->links_limit, $package->settings->links_limit) ?></li>
                <?php endif ?>

            </ul>

            <a href="<?= Authentication::check() ? url('pay/' . $package->package_id) : url('register?redirect=pay/' . $package->package_id) ?>" class="pricing-action"><?= $this->language->package->button->choose ?></a>
        </div>

    <?php endwhile ?>

    <?php endif ?>

</div>
