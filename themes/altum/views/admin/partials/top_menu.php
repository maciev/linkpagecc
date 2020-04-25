<?php defined('ALTUMCODE') || die() ?>

<nav class="navbar navbar-expand-lg navbar-light admin-top-navbar">
    <button class="btn admin-top-toggler d-inline-block d-lg-none" type="button" id="admin_menu_toggler"><i class="fa fa-bars"></i></button>

    <h1 class="admin-top-navbar-brand">
        <?= '' ?>
    </h1>

    <ul class="navbar-nav ml-auto">
        <li class="dropdown">
            <a class="nav-link admin-top-nav-link row dropdown-toggle dropdown-toggle-simple" data-toggle="dropdown" href="#" aria-haspopup="true" aria-expanded="false">
                <div class="col-1 d-flex align-items-center">
                    <img src="<?= get_gravatar($this->user->email) ?>" class="admin-avatar" />
                </div>
                <div class="col">
                    <span class="d-inline"><?= $this->user->name ?></span>
                </div>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <a class="dropdown-item" href="<?= url('account') ?>"><i class="fa fa-wrench"></i> <?= $this->language->account->menu ?></a>
                <a class="dropdown-item" href="<?= url('logout') ?>"><i class="fa fa-sign-out-alt"></i> <?= $this->language->global->menu->logout ?></a>
            </div>
        </li>
    </ul>
</nav>
