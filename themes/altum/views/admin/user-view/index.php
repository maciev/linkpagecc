<?php defined('ALTUMCODE') || die() ?>

<div class="d-flex justify-content-between">
    <div class="d-flex align-items-center">
        <h1><span class="underline mr-3"><?= $this->language->admin_user_view->header ?></span></h1>

        <?= get_admin_options_button('user', $data->user->user_id) ?>
    </div>

    <div><?= get_back_button('admin/users') ?></div>
</div>

<div class="card border-0 shadow-sm mt-5">
    <div class="card-body">

        <div class="row mt-md-3">
            <div class="col-12 col-md-4">
                <div class="form-group">
                    <label class="font-weight-bold"><?= $this->language->admin_user_view->main->email ?></label>
                    <input type="text" class="form-control-plaintext" value="<?= $data->user->email ?>" readonly />
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="form-group">
                    <label class="font-weight-bold"><?= $this->language->admin_user_view->main->name ?></label>
                    <input type="text" class="form-control-plaintext" value="<?= $data->user->name ?>" readonly />
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="form-group">
                    <label class="font-weight-bold"><?= $this->language->admin_user_view->main->status ?></label>
                    <input type="text" class="form-control-plaintext" value="<?= $data->user->active ? $this->language->admin_user_view->main->status_active : $this->language->admin_user_view->main->status_disabled ?>" readonly />
                </div>
            </div>
        </div>

        <div class="row mt-md-3">

            <div class="col-12 col-md-4">
                <div class="form-group">
                    <label class="font-weight-bold"><?= $this->language->admin_user_view->main->ip ?></label>
                    <input type="text" class="form-control-plaintext" value="<?= $data->user->ip ?>" readonly />
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="form-group">
                    <label class="font-weight-bold"><?= $this->language->admin_user_view->main->last_activity ?></label>
                    <input type="text" class="form-control-plaintext" value="<?= $data->user->last_activity ? $data->user->last_activity : '-' ?>" readonly />
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="form-group">
                    <label class="font-weight-bold"><?= $this->language->admin_user_view->main->last_user_agent ?></label>
                    <input type="text" class="form-control-plaintext" value="<?= $data->user->last_user_agent ?>" readonly />
                </div>
            </div>
        </div>

        <div class="row mt-md-3">
            <div class="col-12 col-md-4">
                <div class="form-group">
                    <label class="font-weight-bold"><?= $this->language->admin_user_view->main->package ?></label>
                    <div>
                        <a href="<?= url('admin/package-update/' . $data->user->package->package_id) ?>"><?= $data->user->package->name ?></a>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="form-group">
                    <label class="font-weight-bold"><?= $this->language->admin_user_view->main->package_expiration_date ?></label>
                    <input type="text" class="form-control-plaintext" value="<?= $data->user->package_expiration_date ?>" readonly />
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="form-group">
                    <label class="font-weight-bold"><?= $this->language->admin_user_view->main->package_trial_done ?></label>
                    <input type="text" class="form-control-plaintext" value="<?= $data->user->package_trial_done ? $this->language->global->yes : $this->language->global->no ?>" readonly />
                </div>
            </div>
        </div>

        <div class="row mt-md-3">
            <div class="col-12 col-md-4">
                <div class="form-group">
                    <label class="font-weight-bold"><?= $this->language->admin_user_view->main->total_logins ?></label>
                    <input type="text" class="form-control-plaintext" value="<?= $data->user->total_logins ?>" readonly />
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="form-group">
                    <label class="font-weight-bold"><?= $this->language->admin_user_view->main->language ?></label>
                    <input type="text" class="form-control-plaintext" value="<?= $data->user->language ?>" readonly />
                </div>
            </div>
        </div>
    </div>
</div>


<?php if($this->settings->payment->is_enabled): ?>
    <h2 class="mt-5"><?= $this->language->admin_user_view->payments->header ?></h2>

    <?php if($data->user_payments_result->num_rows): ?>

        <div class="mt-5 table-responsive table-custom-container">
            <table class="table table-custom">
                <thead class="thead-black">
                <tr>
                    <th><?= $this->language->admin_user_view->payments->nr ?></th>
                    <th><?= $this->language->admin_user_view->payments->type ?></th>
                    <th><?= $this->language->admin_user_view->payments->processor ?></th>
                    <th><?= $this->language->admin_user_view->payments->email ?></th>
                    <th><?= $this->language->admin_user_view->payments->name ?></th>
                    <th><?= $this->language->admin_user_view->payments->amount ?></th>
                    <th><?= $this->language->admin_user_view->payments->date ?></th>

                </tr>
                </thead>
                <tbody>

                <?php $nr = 1; while($row = $data->user_payments_result->fetch_object()): ?>

                    <?php
                    switch($row->processor) {
                        case 'STRIPE':
                            $row->processor = '<span data-toggle="tooltip" title="' . $this->language->admin_payments->table->stripe .'"><i class="fab fa-stripe fa-2x icon-stripe"></i></span>';
                            break;

                        case 'PAYPAL':
                            $row->processor = '<span data-toggle="tooltip" title="' . $this->language->admin_payments->table->paypal .'"><i class="fab fa-paypal fa-2x icon-paypal"></i></span>';
                            break;
                    }
                    ?>

                    <tr>
                        <td><?= $nr++ ?></td>
                        <td><?= $row->type == 'ONE-TIME' ? '<span data-toggle="tooltip" title="' . $row->type . '"><i class="fa fa-hand-holding-usd"></i></span>' : '<span data-toggle="tooltip" title="' . $row->type . '"><i class="fa fa-sync-alt"></i></span>' ?></td>
                        <td><?= $row->processor ?></td>
                        <td><?= $row->email ?></td>
                        <td><?= $row->name ?></td>
                        <td><span class="text-success"><?= $row->amount ?></span> <?= $row->currency ?></td>
                        <td><span data-toggle="tooltip" title="<?= \Altum\Date::get($row->date, true) ?>"><?= \Altum\Date::get($row->date) ?></span></td>
                    </tr>
                <?php endwhile ?>

                </tbody>
            </table>
        </div>

    <?php else: ?>
        <?= $this->language->admin_user_view->info_message->no_payments ?>
    <?php endif ?>
<?php endif ?>

<?php if($data->user_logs_result->num_rows): ?>
    <h2 class="mt-5"><?= $this->language->admin_user_view->logs->header ?></h2>
    <p class="text-muted"><?= $this->language->admin_user_view->logs->subheader ?></p>

    <div class="table-responsive table-custom-container">
        <table class="table table-custom">
            <thead class="thead-black">
            <tr>
                <th><?= $this->language->admin_user_view->logs->type ?></th>
                <th><?= $this->language->admin_user_view->logs->ip ?></th>
                <th><?= $this->language->admin_user_view->logs->date ?></th>
            </tr>
            </thead>
            <tbody>

            <?php $nr = 1; while($row = $data->user_logs_result->fetch_object()): ?>
                <tr>
                    <td><?= $row->type ?></td>
                    <td><?= $row->ip ?></td>
                    <td class="text-muted"><?= \Altum\Date::get($row->date, true) ?></td>
                </tr>
            <?php endwhile ?>

            </tbody>
        </table>
    </div>
<?php endif ?>
