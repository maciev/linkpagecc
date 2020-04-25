<?php defined('ALTUMCODE') || die() ?>

<?php ob_start() ?>
<script src="<?= url(ASSETS_URL_PATH . 'js/libraries/Chart.bundle.min.js') ?>"></script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>

<div class="mb-5 row justify-content-between">
    <div class="col-6 col-md-3 mb-5">
        <div class="card border-0 bg-gradient-primary text-white h-100 zoomer">
            <div class="card-body">
                <div class="card-title h4 mb-3"><?= $data->links->clicks_month ?> <i class="fa fa-chart-line fa-xs"></i></div>

                <p><?= $this->language->admin_index->display->clicks_month ?></p>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3 mb-5">
        <div class="card border-0 bg-gradient-danger text-white h-100 zoomer">
            <div class="card-body">
                <div class="card-title h4 mb-3"><?= $data->users->active_users_month ?> <i class="fa fa-users fa-xs"></i></div>

                <p><?= $this->language->admin_index->display->active_users_month ?></p>
            </div>
        </div>
    </div>

    <?php if($this->settings->payment->is_enabled): ?>
    <div class="col-6 col-md-3 mb-5">
        <div class="card border-0 bg-gradient-warning text-white h-100 zoomer">
            <div class="card-body pb-0">
                <p>
                    <span class="card-title h4"><?= $data->payments_month ?></span>

                    <?= $this->language->admin_index->display->payments_month ?>
                </p>
            </div>

            <div class="admin-widget-chart-container">
                <canvas id="payments"></canvas>
            </div>
        </div>
    </div>

    <?php ob_start() ?>
    <script>
        new Chart(document.getElementById('payments').getContext('2d'), {
            type: 'line',
            data: {
                labels: <?= $data->payments_month_chart['labels'] ?>,
                datasets: [{
                    data: <?= $data->payments_month_chart['payments'] ?? '[]' ?>,
                    backgroundColor: 'rgba(255, 255, 255, .5)',
                    borderColor: 'rgb(255, 255, 255)',
                    fill: true
                }]
            },
            options: {
                legend: {
                    display: false
                },
                tooltips: {
                    enabled: false
                },
                title: {
                    display: false
                },
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    yAxes: [{
                        display: false,
                        gridLines: false,
                        ticks: {
                            userCallback: (value, index, values) => {
                                if (Math.floor(value) === value) {
                                    return nr(value);
                                }
                            }
                        }
                    }],
                    xAxes: [{
                        display: false,
                        gridLines: false,
                    }]
                }
            }
        });
    </script>
    <?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>

    <div class="col-6 col-md-3 mb-5">
        <div class="card border-0 bg-gradient-info text-white h-100 zoomer">
            <div class="card-body pb-0">
                <p>
                    <span class="card-title h4"><?= $data->earnings_month ?> <?= $this->settings->payment->currency ?></span>

                    <?= $this->language->admin_index->display->earnings_month ?>
                </p>
            </div>

            <div class="admin-widget-chart-container">
                <canvas id="earnings"></canvas>
            </div>
        </div>
    </div>

    <?php ob_start() ?>
    <script>
        new Chart(document.getElementById('earnings').getContext('2d'), {
            type: 'line',
            data: {
                labels: <?= $data->payments_month_chart['labels'] ?>,
                datasets: [{
                    data: <?= $data->payments_month_chart['earnings'] ?? '[]' ?>,
                    backgroundColor: 'rgba(255, 255, 255, .5)',
                    borderColor: 'rgb(255, 255, 255)',
                    fill: true
                }]
            },
            options: {
                legend: {
                    display: false
                },
                tooltips: {
                    enabled: false
                },
                title: {
                    display: false
                },
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    yAxes: [{
                        display: false,
                        gridLines: false,
                        ticks: {
                            userCallback: (value, index, values) => {
                                if (Math.floor(value) === value) {
                                    return nr(value);
                                }
                            }
                        }
                    }],
                    xAxes: [{
                        display: false,
                        gridLines: false,
                    }]
                }
            }
        });
    </script>
    <?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
    <?php endif ?>

    <div class="col-6 col-md-3 mb-5 mb-md-0 zoomer">
        <div class="card border-0 h-100">
            <div class="card-body">
                <div class="card-title h4 mb-3"><?= $data->links->clicks ?> <i class="fa fa-chart-line fa-xs"></i></div>

                <p><?= $this->language->admin_index->display->clicks ?></p>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3 mb-5 mb-md-0 zoomer">
        <div class="card border-0 h-100">
            <div class="card-body">
                <div class="card-title h4 mb-3"><?= $data->users->active_users ?>  <i class="fa fa-users fa-xs"></i></div>

                <p><?= $this->language->admin_index->display->active_users ?></p>
            </div>
        </div>
    </div>

    <?php if($this->settings->payment->is_enabled): ?>
    <div class="col-6 col-md-3 mb-5 mb-md-0 zoomer">
        <div class="card border-0 h-100">
            <div class="card-body">
                <div class="card-title h4 mb-3"><?= $data->payments->payments ?>  <i class="fa fa-dollar-sign fa-xs"></i></div>

                <p><?= $this->language->admin_index->display->payments ?></p>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3 mb-5 mb-md-0 zoomer">
        <div class="card border-0 h-100">
            <div class="card-body">
                <div class="card-title h4 mb-3"><span class="text-success"><?= $data->payments->earnings ?></span> <?= $this->settings->payment->currency ?></div>

                <p><?= $this->language->admin_index->display->earnings ?></p>
            </div>
        </div>
    </div>
    <?php endif ?>
</div>

<div class="mb-5">
    <h2><?= $this->language->admin_index->users->header ?></h2>

    <?php $result = \Altum\Database\Database::$database->query("SELECT `user_id`, `name`, `email`, `active` FROM `users` ORDER BY `user_id` DESC LIMIT 5"); ?>
    <div class="table-responsive table-custom-container">
        <table class="table table-custom">
            <thead class="thead-black">
            <tr>
                <th><?= $this->language->admin_index->users->name ?></th>
                <th><?= $this->language->admin_index->users->email ?></th>
                <th><?= $this->language->admin_index->users->status ?></th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php while($row = $result->fetch_object()): ?>
                <tr>
                    <td><?= '<a href="' . url('admin/user-update/' . $row->user_id) . '">' . $row->name . '</a>' ?></td>
                    <td><?= $row->email ?></td>
                    <td><?= $row->active ? '<span class="badge badge-pill badge-success">' . $this->language->global->active . '</span>' : '<span class="badge badge-pill badge-warning">' . $this->language->global->disabled . '</span>' ?></td>
                    <td><?= get_admin_options_button('user', $row->user_id) ?></td>
                </tr>
            <?php endwhile ?>
            </tbody>
        </table>
    </div>
</div>

<?php $result = \Altum\Database\Database::$database->query("SELECT `payments`.*, `users`.`email` AS `user_email` FROM `payments` LEFT JOIN `users` ON `payments`.`user_id` = `users`.`user_id` ORDER BY `id` DESC LIMIT 5"); ?>

<?php if($result->num_rows): ?>
    <div class="mb-5">
        <h2><?= $this->language->admin_index->payments->header ?></h2>

        <div class="table-responsive table-custom-container">
            <table class="table table-custom">
                <thead class="thead-black">
                <tr>
                    <th><?= $this->language->admin_index->payments->user ?></th>
                    <th></th>
                    <th></th>
                    <th><?= $this->language->admin_index->payments->email ?></th>
                    <th><?= $this->language->admin_index->payments->name ?></th>
                    <th></th>
                    <th><?= $this->language->admin_index->payments->date ?></th>
                </tr>
                </thead>
                <tbody>
                <?php while($row = $result->fetch_object()): ?>

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
                        <td><?= '<a href="' . url( 'admin/user-update/' . $row->user_id) . '">' . $row->user_email . '</a>' ?></td>
                        <td><?= $row->type == 'ONE-TIME' ? '<span data-toggle="tooltip" title="' . $row->type . '"><i class="fa fa-hand-holding-usd"></i></span>' : '<span data-toggle="tooltip" title="' . $row->type . '"><i class="fa fa-sync-alt"></i></span>' ?></td>
                        <td><?= $row->processor ?></td>
                        <td><?= $row->email ?></td>
                        <td><?= $row->name ?></td>
                        <td><span class="text-success"><?= $row->amount ?></span> <?= $row->currency ?></td>
                        <td><?= $row->date ?></td>
                    </tr>
                <?php endwhile ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif ?>
