<?php defined('ALTUMCODE') || die() ?>

<nav class="admin-sidebar" id="admin_sidebar">
    <div class="admin-sidebar-sticky">

        <div class="my-3">
            <a href="<?= url() ?>" class="text-decoration-none">
                <?php if($this->settings->logo != ''): ?>
                    <img src="<?= url(UPLOADS_URL_PATH . 'logo/' . $this->settings->logo) ?>" class="img-fluid admin-navbar-logo" alt="<?= $this->language->global->accessibility->logo_alt ?>" />
                <?php else: ?>
                    <span class="admin-navbar-brand"><?= $this->settings->title ?></span>
                <?php endif ?>
            </a>
        </div>

        <ul class="nav flex-column">

            <li class="admin-nav-item">
                <a class="nav-link admin-nav-link row <?= \Altum\Routing\Router::$controller == 'AdminIndex' ? 'active' : null ?>" href="<?= url('admin/') ?>">
                    <div class="col-1 d-flex align-items-center"><i class="admin-nav-icon fa fa-tv"></i></div>
                    <div class="col">
                        <span class="d-inline"><?= $this->language->admin_index->menu ?></span>
                    </div>
                </a>
            </li>

            <li class="admin-nav-item">
                <a class="nav-link admin-nav-link row <?= \Altum\Routing\Router::$controller == 'AdminUsers' ? 'active' : null ?>" href="<?= url('admin/users') ?>">
                    <div class="col-1 d-flex align-items-center"><i class="admin-nav-icon fa fa-users"></i></div>
                    <div class="col">
                        <span class="d-inline"><?= $this->language->admin_users->menu ?></span>
                    </div>
                </a>
            </li>

            <li class="admin-nav-item">
                <a class="nav-link admin-nav-link row <?= \Altum\Routing\Router::$controller == 'AdminLinks' ? 'active' : null ?>" href="<?= url('admin/links') ?>">
                    <div class="col-1 d-flex align-items-center"><i class="admin-nav-icon fa fa-link"></i></div>
                    <div class="col">
                        <span class="d-inline"><?= $this->language->admin_links->menu ?></span>
                    </div>
                </a>
            </li>

            <li class="admin-nav-item">
                <a class="nav-link admin-nav-link row <?= \Altum\Routing\Router::$controller == 'AdminPackages' ? 'active' : null ?>" href="<?= url('admin/packages') ?>">
                    <div class="col-1 d-flex align-items-center"><i class="admin-nav-icon fa fa-box-open"></i></div>
                    <div class="col">
                        <span class="d-inline"><?= $this->language->admin_packages->menu ?></span>
                    </div>
                </a>
            </li>

            <?php if($this->settings->payment->is_enabled): ?>
            <li class="admin-nav-item">
                <a class="nav-link admin-nav-link row <?= \Altum\Routing\Router::$controller == 'AdminPayments' ? 'active' : null ?>" href="<?= url('admin/payments') ?>">
                    <div class="col-1 d-flex align-items-center"><i class="admin-nav-icon fa fa-dollar-sign"></i></div>
                    <div class="col">
                        <span class="d-inline"><?= $this->language->admin_payments->menu ?></span>
                    </div>
                </a>
            </li>
            <?php endif ?>

            <li class="admin-nav-item">
                <a class="nav-link admin-nav-link row <?= \Altum\Routing\Router::$controller == 'AdminStatistics' ? 'active' : null ?>" href="<?= url('admin/statistics') ?>">
                    <div class="col-1 d-flex align-items-center"><i class="admin-nav-icon fa fa-chart-line"></i></div>
                    <div class="col">
                        <span class="d-inline"><?= $this->language->admin_statistics->menu ?></span>
                    </div>
                </a>
            </li>

        </ul>

        <h6 class="admin-sidebar-heading text-muted">
            <span>MORE</span>
        </h6>

        <ul class="nav flex-column">

            <li class="admin-nav-item">
                <a class="nav-link admin-nav-link row" target="_blank" href="<?= url() ?>">
                    <div class="col-1 d-flex align-items-center"><i class="admin-nav-icon fa fa-home"></i></div>
                    <div class="col">
                        <span class="d-inline">Go Home</span>
                    </div>
                </a>
            </li>

            <li class="admin-nav-item dropdown">
                <a class="nav-link admin-nav-link row dropdown-toggle dropdown-toggle-simple" data-toggle="dropdown" href="#" aria-haspopup="true" aria-expanded="false">
                    <div class="col-1 d-flex align-items-center"><img src="<?= get_gravatar($this->user->email) ?>" class="admin-avatar" /></div>
                    <div class="col">
                        <span class="d-inline"><?= $this->user->name ?></span>
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" href="<?= url('account') ?>"><i class="fa fa-sm fa-wrench mr-1"></i> <?= $this->language->account->menu ?></a>
                    <a class="dropdown-item" href="<?= url('logout') ?>"><i class="fa fa-sm fa-sign-out-alt mr-1"></i> <?= $this->language->global->menu->logout ?></a>
                </div>
            </li>


        </ul>

    </div>
</nav>
