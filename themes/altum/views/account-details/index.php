<?php defined('ALTUMCODE') || die() ?>

<header class="header">
    <div class="container">

        <div class="d-flex justify-content-between">
            <div>
                <h1><?= $this->language->account_details->header ?></h1>
            </div>

            <div>
                <small><?= get_back_button('account') ?></small>
            </div>
        </div>
    </div>
</header>

<?php require THEME_PATH . 'views/partials/ads_header.php' ?>

<section class="container">

    <?php display_notifications() ?>

    <?php if($this->settings->payment->is_enabled): ?>
        <div class="margin-top-3 d-flex justify-content-between">
            <h2><?= $this->language->account_details->payments->header ?></h2>
        </div>

        <?php if($data->payments_result->num_rows): ?>
            <div class="table-responsive table-custom-container">
                <table class="table table-custom">
                    <thead class="thead-black">
                    <tr>
                        <th><?= $this->language->account_details->payments->nr ?></th>
                        <th><?= $this->language->account_details->payments->type ?></th>
                        <th></th>
                        <th><?= $this->language->account_details->payments->package_id ?></th>
                        <th><?= $this->language->account_details->payments->email ?></th>
                        <th><?= $this->language->account_details->payments->name ?></th>
                        <th><?= $this->language->account_details->payments->amount ?></th>
                        <th><?= $this->language->account_details->payments->date ?></th>
                        <?php if($this->settings->business->invoice_is_enabled): ?>
                            <th></th>
                        <?php endif ?>
                    </tr>
                    </thead>
                    <tbody>

                    <?php $nr = 1; while($row = $data->payments_result->fetch_object()): ?>

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
                            <td class="text-muted"><?= $nr++ ?></td>
                            <td><?= $row->type == 'ONE-TIME' ? '<span data-toggle="tooltip" title="' . $row->type . '"><i class="fa fa-hand-holding-usd"></i></span>' : '<span data-toggle="tooltip" title="' . $row->type . '"><i class="fa fa-sync-alt"></i></span>' ?></td>
                            <td><?= $row->processor ?></td>
                            <td><?= (new \Altum\Models\Package())->get_package_by_id($row->package_id)->name ?></td>
                            <td><?= $row->email ?></td>
                            <td><?= $row->name ?></td>
                            <td><span class="text-success"><?= $row->amount ?></span> <?= $row->currency ?></td>
                            <td class="text-muted"><span data-toggle="tooltip" title="<?= \Altum\Date::get($row->date, true) ?>"><?= \Altum\Date::get($row->date) ?></span></td>
                            <?php if($this->settings->business->invoice_is_enabled): ?>
                            <td>
                                <a href="<?= url('invoice/' . $row->id) ?>">
                                    <span data-toggle="tooltip" title="<?= $this->language->account_details->payments->invoice ?>"><i class="fa fa-file-invoice"></i></span>
                                </a>
                            </td>
                            <?php endif ?>
                        </tr>
                    <?php endwhile ?>

                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-muted"><?= $this->language->account_details->info_message->no_payments ?></p>
        <?php endif ?>
    <?php endif ?>


    <?php if($data->logs_result->num_rows): ?>
        <div class="margin-top-3 d-flex justify-content-between">
            <h2><?= $this->language->account_details->logs->header ?></h2>
        </div>
        <p class="text-muted"><?= $this->language->account_details->logs->subheader ?></p>

        <div class="table-responsive table-custom-container">
            <table class="table table-custom">
                <thead class="thead-black">
                <tr>
                    <th><?= $this->language->account_details->logs->type ?></th>
                    <th><?= $this->language->account_details->logs->ip ?></th>
                    <th><?= $this->language->account_details->logs->date ?></th>
                </tr>
                </thead>
                <tbody>

                <?php $nr = 1; while($row = $data->logs_result->fetch_object()): ?>
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
</section>
