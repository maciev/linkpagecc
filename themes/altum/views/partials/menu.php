<?php defined('ALTUMCODE') || die() ?>

<nav class="navbar navbar-main <?= \Altum\Routing\Router::$controller_settings['menu_no_margin'] ? null : 'margin-bottom-6'?> navbar-expand-lg navbar-light <?= \Altum\Routing\Router::$controller_key == 'index' ? 'navbar-absolute navbar-dark' : null ?>">
    <div class="container">
        <a class="navbar-brand" href="<?= url() ?>">
            <?php if($this->settings->logo != ''): ?>
                <img src="<?= url(UPLOADS_URL_PATH . 'logo/' . $this->settings->logo) ?>" class="img-fluid navbar-logo" alt="<?= $this->language->global->accessibility->logo_alt ?>" />
            <?php else: ?>
                <?= $this->settings->title ?>
            <?php endif ?>
        </a>

        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#main_navbar" aria-controls="main_navbar" aria-expanded="false" aria-label="<?= $this->language->global->accessibility->toggle_navigation ?>">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-end" id="main_navbar">
            <ul class="navbar-nav">

                <?php foreach($data->pages as $data): ?>
                <li class="nav-item"><a class="nav-link" href="<?= $data->url ?>" target="<?= $data->target ?>"><?= $data->title ?></a></li>
                <?php endforeach ?>


                <?php if(\Altum\Middlewares\Authentication::check()): ?>

                    <li class="nav-item"><a class="nav-link" href="<?= url('dashboard') ?>"> <?= $this->language->dashboard->menu ?></a></li>

                    <li class="dropdown">
                        <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" aria-haspopup="true" aria-expanded="false">
                            <img src="<?= get_gravatar($this->user->email) ?>" class="navbar-avatar" />
                            <?= $this->user->name ?> <span class="caret"></span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <?php if(\Altum\Middlewares\Authentication::is_admin()): ?>
                                <a class="dropdown-item" href="<?= url('admin') ?>"><i class="fa fa-sm fa-user-shield mr-1"></i> <?= $this->language->global->menu->admin ?></a>
                            <?php endif ?>
                            <a class="dropdown-item" href="<?= url('account') ?>"><i class="fa fa-sm fa-wrench mr-1"></i> <?= $this->language->account->menu ?></a>
                            <a class="dropdown-item" href="<?= url('logout') ?>"><i class="fa fa-sm fa-sign-out-alt mr-1"></i> <?= $this->language->global->menu->logout ?></a>
                        </div>
                    </li>

                <?php else: ?>

                    <li class="nav-item active"><a class="nav-link" href="<?= url('login') ?>"><i class="fa fa-sm fa-sign-in-alt mr-1"></i> <?= $this->language->login->menu ?></a></li>

                    <?php if($this->settings->register_is_enabled): ?>
                    <li class="nav-item active"><a class="nav-link" href="<?= url('register') ?>"><i class="fa fa-sm fa-plus mr-1"></i> <?= $this->language->register->menu ?></a></li>
                    <?php endif ?>

                <?php endif ?>

            </ul>
        </div>
    </div>
</nav>
