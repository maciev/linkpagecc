<?php defined('ALTUMCODE') || die() ?>

<input type="hidden" id="base_controller_url" name="base_controller_url" value="<?= url('project/' . $data->project->project_id) ?>" />

<header class="header">
    <div class="container">

        <div class="d-flex flex-column flex-md-row justify-content-between">
            <div class="d-flex align-items-center">
                <h1 class="mr-3"><?= sprintf($this->language->project->header->header, $data->project->name) ?></h1>

                <div class="dropdown">
                    <a href="#" data-toggle="dropdown" class="text-secondary dropdown-toggle dropdown-toggle-simple">
                        <i class="fas fa-ellipsis-v"></i>

                        <div class="dropdown-menu dropdown-menu-right">
                            <a href="#" class="dropdown-item" data-delete="<?= $this->language->global->info_message->confirm_delete ?>" data-row-id="<?= $data->project->project_id ?>"><i class="fa fa-times"></i> <?= $this->language->global->delete ?></a>
                        </div>
                    </a>
                </div>
            </div>

            <div class="col-auto p-0">
                <a href="<?= url('dashboard') ?>" class="btn btn-default rounded-pill"><i class="fa fa-list-ul"></i> <?= $this->language->project->header->projects ?></a>
            </div>
        </div>

        <p class="text-muted"><?= $this->language->project->header->subheader ?></p>

    </div>
</header>

<?php require THEME_PATH . 'views/partials/ads_header.php' ?>

<section class="container">

    <?php display_notifications() ?>

    <?php if($data->logs_chart): ?>
    <div class="chart-container">
        <canvas id="clicks_chart"></canvas>
    </div>
    <?php endif ?>

    <div class="margin-top-3 d-flex justify-content-between">
        <h2><?= $this->language->project->links->header ?></h2>

        <div class="col-auto p-0">
            <div class="dropdown">
                <button type="button" data-toggle="dropdown" class="btn btn-success rounded-pill dropdown-toggle dropdown-toggle-simple">
                    <i class="fas fa-plus-circle"></i> <?= $this->language->project->links->create ?></button>

                <div class="dropdown-menu dropdown-menu-right">
                    <a href="#" class="dropdown-item" data-toggle="modal" data-target="#create_biolink">
                        <i class="fa fa-circle fa-sm" style="color: <?= $this->language->link->biolink->color ?>"></i>

                        <?= $this->language->link->biolink->name ?>
                    </a>

                    <a href="#" class="dropdown-item" data-toggle="modal" data-target="#create_link">
                        <i class="fa fa-circle fa-sm" style="color: <?= $this->language->link->link->color ?>"></i>

                        <?= $this->language->link->link->name ?>
                    </a>
                </div>
            </div>

        </div>
    </div>

    <?php if(count($data->links_logs)): ?>

        <?php foreach($data->links_logs as $row): ?>

        <div class="d-flex custom-row align-items-center my-3 <?= $row->is_enabled ? null : 'custom-row-inactive' ?>">

            <div class="col-1 p-0">

                <span class="fa-stack fa-1x" data-toggle="tooltip" title="<?= $this->language->link->{$row->type}->name ?>">
                  <i class="fas fa-circle fa-stack-2x" style="color: <?= $this->language->link->{$row->type}->color ?>"></i>
                  <i class="fas <?= $this->language->link->{$row->type}->icon ?> fa-stack-1x fa-inverse"></i>
                </span>

            </div>

            <div class="col-8 col-md-5">
                <div class="d-flex flex-column">
                    <strong><a href="<?= url('link/' . $row->link_id) ?>"><?= $row->url ?></a></strong>
                    <span class="d-flex align-items-center">
                        <?php if(!empty($row->location_url)): ?>
                            <img src="https://www.google.com/s2/favicons?domain=<?= $row->location_url ?>" class="img-fluid mr-2" />
                            <a href="<?= $row->location_url ?>"><?= $row->location_url ?></a>
                        <?php else: ?>
                            <img src="https://www.google.com/s2/favicons?domain=<?= $row->full_url ?>" class="img-fluid mr-2" />
                            <a href="<?= $row->full_url ?>"><?= $row->full_url ?></a>
                        <?php endif ?>
                    </span>
                </div>
            </div>

            <div class="col d-none d-md-block">
                <a href="<?= url('link/' . $row->link_id . '/statistics') ?>">
                    <span data-toggle="tooltip" title="<?= $this->language->project->links->clicks ?>"><i class="fa fa-chart-bar custom-row-statistic-icon"></i> <span class="custom-row-statistic-number"><?= nr($row->clicks) ?></span></span>
                </a>
            </div>

            <div class="col d-none d-md-block">
                <span class="text-muted" data-toggle="tooltip" title="<?= $this->language->project->links->date ?>"><?= \Altum\Date::get($row->date, 2) ?></span>
            </div>

            <div class="col-1 col-md-auto">
                <div class="custom-control custom-switch">
                    <input
                            type="checkbox"
                            class="custom-control-input"
                            id="link_is_enabled_<?= $row->link_id ?>"
                            data-row-id="<?= $row->link_id ?>"
                            onchange="ajax_call_helper(event, 'link-ajax', 'is_enabled_toggle')"
                        <?= $row->is_enabled ? 'checked="true"' : null ?>
                    >
                    <label class="custom-control-label clickable" data-toggle="tooltip" title="<?= $this->language->project->links->is_enabled_tooltip ?>" for="link_is_enabled_<?= $row->link_id ?>"></label>
                </div>
            </div>

            <div class="col-1 col-md-auto">
                <div class="dropdown">
                    <a href="#" data-toggle="dropdown" class="text-secondary dropdown-toggle dropdown-toggle-simple">
                        <i class="fas fa-ellipsis-v"></i>

                        <div class="dropdown-menu dropdown-menu-right">
                            <a href="<?= url('link/' . $row->link_id) ?>" class="dropdown-item"><i class="fa fa-pencil-alt"></i> <?= $this->language->global->edit ?></a>
                            <a href="<?= url('link/' . $row->link_id . '/statistics') ?>" class="dropdown-item"><i class="fa fa-chart-bar"></i> <?= $this->language->link->statistics->link ?></a>
                            <a href="#" class="dropdown-item" data-delete="<?= $this->language->global->info_message->confirm_delete ?>" data-row-id="<?= $row->link_id ?>"><i class="fa fa-times"></i> <?= $this->language->global->delete ?></a>
                        </div>
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach ?>

    <?php else: ?>

        <div class="alert alert-info" role="alert">
            <?= $this->language->project->links->no_links ?>
        </div>

    <?php endif ?>

</section>

<?php ob_start() ?>
<script src="<?= url(ASSETS_URL_PATH . 'js/libraries/Chart.bundle.min.js') ?>"></script>
<script>
    /* Charts */
    Chart.defaults.global.elements.line.borderWidth = 4;
    Chart.defaults.global.elements.point.radius = 3;
    Chart.defaults.global.elements.point.borderWidth = 7;

    let clicks_chart = document.getElementById('clicks_chart').getContext('2d');

    let gradient = clicks_chart.createLinearGradient(0, 0, 0, 250);
    gradient.addColorStop(0, 'rgba(43, 227, 155, 0.6)');
    gradient.addColorStop(1, 'rgba(43, 227, 155, 0.05)');

    let gradient_white = clicks_chart.createLinearGradient(0, 0, 0, 250);
    gradient_white.addColorStop(0, 'rgba(255, 255, 255, 0.6)');
    gradient_white.addColorStop(1, 'rgba(255, 255, 255, 0.05)');

    new Chart(clicks_chart, {
        type: 'line',
        data: {
            labels: <?= $data->logs_chart['labels'] ?>,
            datasets: [{
                label: <?= json_encode($this->language->link->statistics->impression) ?>,
                data: <?= $data->logs_chart['impression'] ?? '[]' ?>,
                backgroundColor: gradient,
                borderColor: '#2BE39B',
                fill: true
            },
                {
                    label: <?= json_encode($this->language->link->statistics->unique) ?>,
                    data: <?= $data->logs_chart['unique'] ?? '[]' ?>,
                    backgroundColor: gradient_white,
                    borderColor: '#ebebeb',
                    fill: true
                }]
        },
        options: {
            tooltips: {
                mode: 'index',
                intersect: false,
                callbacks: {
                    label: (tooltipItem, data) => {
                        let value = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index];

                        return `${nr(value)} ${data.datasets[tooltipItem.datasetIndex].label}`;
                    }
                }
            },
            title: {
                display: false
            },
            legend: {
                display: true
            },
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                yAxes: [{
                    gridLines: {
                        display: false
                    },
                    ticks: {
                        userCallback: (value, index, values) => {
                            if (Math.floor(value) === value) {
                                return nr(value);
                            }
                        }
                    },
                    min: 0
                }],
                xAxes: [{
                    gridLines: {
                        display: false
                    }
                }]
            }
        }
    });

    /* Delete handler */
    $('[data-delete]').on('click', event => {
        let message = $(event.currentTarget).attr('data-delete');

        if(!confirm(message)) return false;

        /* Continue with the deletion */
        ajax_call_helper(event, 'link-ajax', 'delete', () => {

            /* On success delete the actual row from the DOM */
            $(event.currentTarget).closest('.custom-row').remove();

        });

    });
</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
