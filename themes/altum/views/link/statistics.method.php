<div class="mb-3 d-flex justify-content-between align-items-center">
    <h2 class="mr-3"><?= $this->language->link->statistics->header ?></h2>

    <div>
        <form class="form-inline" id="datepicker_form">
            <div class="input-group mr-sm-2">
                <div class="input-group-prepend">
                    <div class="input-group-text"><i class="fa fa-calendar-alt"></i></div>
                </div>

                <input
                        type="text"
                        class="form-control form-control-sm"
                        id="datepicker_input"
                        data-range="true"
                        data-min="<?= (new \DateTime($data->link->date))->format('Y-m-d') ?>"
                        name="date_range"
                        value="<?= $data->date->input_date_range ?>"
                        placeholder="<?= $this->language->global->date_range_selector ?>"
                        autocomplete="off"
                >
            </div>

            <button type="submit" class="btn btn-sm btn-dark"><?= $this->language->global->date_range_selector ?></button>
        </form>
    </div>
</div>

<?php if(!count($data->logs)): ?>

    <div class="alert alert-info" role="alert"><?= $this->language->link->statistics->no_logs ?></div>

<?php elseif(!$this->user->package_settings->statistics): ?>

        <div class="alert alert-info" role="alert"><?= $this->language->link->statistics->missing_statistics_package ?></div>

<?php else: ?>

    <div class="chart-container">
        <canvas id="clicks_chart"></canvas>
    </div>

    <div class="row my-5">
        <div class="col-12 col-md mr-md-4 custom-row">
            <h3><?= $this->language->link->statistics->referer ?></h3>
            <p class="text-muted mb-3"><?= $this->language->link->statistics->referer_help ?></p>

            <?php foreach($data->logs_data['referer'] as $key => $value): ?>
            <div class="row">
                <div class="col">

                    <?php if($key == 'false'): ?>
                        <span><?= $this->language->link->statistics->referer_direct ?></span>
                    <?php else: ?>
                        <img src="https://www.google.com/s2/favicons?domain=<?= $key ?>" class="img-fluid mr-1" />
                        <a href="<?= $key ?>" title="<?= $key ?>"><?= string_truncate($key, 48) ?></a>
                    <?php endif ?>

                </div>

                <div class="col-auto">
                    <span class="badge badge-pill badge-primary"><?= nr($value) ?></span>
                </div>
            </div>
            <?php endforeach ?>
        </div>

        <div class="col-12 col-md ml-md-4 custom-row">
            <h3><?= $this->language->link->statistics->location ?></h3>
            <p class="text-muted mb-3"><?= $this->language->link->statistics->location_help ?></p>

            <?php foreach($data->logs_data['location'] as $key => $value): ?>
                <div class="row">
                    <div class="col">
                        <?php if($key != 'false'): ?>
                            <img src="https://www.countryflags.io/<?= $key ?>/flat/16.png" class="img-fluid mr-1" />
                            <?= get_country_from_country_code($key) ?>
                        <?php else: ?>
                            N/A
                        <?php endif ?>
                    </div>

                    <div class="col-auto">
                        <span class="badge badge-pill badge-primary"><?= nr($value) ?></span>
                    </div>
                </div>
            <?php endforeach ?>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-md mr-md-4 custom-row">
            <h3><?= $this->language->link->statistics->browser ?></h3>
            <p class="text-muted mb-3"><?= $this->language->link->statistics->browser_help ?></p>

            <div class="chart-container">
                <canvas id="browser_chart"></canvas>
            </div>

            <?php foreach($data->logs_data['browser'] as $key => $value): ?>
                <div class="row">
                    <div class="col">
                        <?= $key == 'false' ? 'N/A' : $key ?>
                    </div>

                    <div class="col-auto">
                        <span class="badge badge-pill badge-primary"><?= nr($value) ?></span>
                    </div>
                </div>
            <?php endforeach ?>
        </div>

        <div class="col-12 col-md ml-md-4 custom-row">
            <h3><?= $this->language->link->statistics->os ?></h3>
            <p class="text-muted mb-3"><?= $this->language->link->statistics->os_help ?></p>

            <div class="chart-container">
                <canvas id="os_chart"></canvas>
            </div>

            <?php foreach($data->logs_data['os'] as $key => $value): ?>
                <div class="row">
                    <div class="col">
                        <?= $key == 'false' ? 'N/A' : $key ?>
                    </div>

                    <div class="col-auto">
                        <span class="badge badge-pill badge-primary"><?= nr($value) ?></span>
                    </div>
                </div>
            <?php endforeach ?>
        </div>
    </div>

<?php endif ?>

<?php ob_start() ?>
<script src="<?= url(ASSETS_URL_PATH . 'js/libraries/Chart.bundle.min.js') ?>"></script>
<script src="<?= url(ASSETS_URL_PATH . 'js/libraries/datepicker.min.js') ?>"></script>
<script src="<?= url(ASSETS_URL_PATH . 'js/libraries/i18n/datepicker.en.js') ?>"></script>

<script>
    /* Datepicker */
    $('#datepicker_input').datepicker({
        language: 'en',
        dateFormat: 'yyyy-mm-dd',
        autoClose: true,
        timepicker: false,
        toggleSelected: false,
        minDate: new Date($('#datepicker_input').data('min')),
        maxDate: new Date()
    });

    $('#datepicker_form').on('submit', (event) => {
        let date = $('#datepicker_input').val();

        let [ date_start, date_end ] = date.split(',');

        if(typeof date_end == 'undefined') {
            date_end = date_start
        }

        /* Redirect */
        fade_out_redirect({ url: `${$('#base_controller_url').val()}/statistics/${date_start}/${date_end}`, full: true });

        event.preventDefault();
    });

    /* Charts */
    <?php if(count($data->logs)): ?>
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
                display: true,
                text: <?= json_encode($this->language->link->statistics->clicks_chart) ?>
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

    <?php $browser_colors = Colors\RandomColor::many(count($data->logs_data['browser']), ['hue'=>'blue']); ?>

    let browser_chart = document.getElementById('browser_chart').getContext('2d');

    new Chart(browser_chart, {
        type: 'doughnut',
        data: {
            labels: <?= json_encode(array_keys($data->logs_data['browser'])) ?>,
            datasets: [{
                label: '',
                data: <?= json_encode(array_values($data->logs_data['browser'])) ?? '[]' ?>,
                backgroundColor: <?= json_encode($browser_colors) ?>,
                borderColor: <?= json_encode($browser_colors) ?>,
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

                        return `${nr(value)} ${data.labels[tooltipItem.index]}`;
                    }
                }
            },
            title: {
                display: false,
            },
            legend: {
                display: true
            },
            responsive: true,
            maintainAspectRatio: false
        }
    });

    <?php $os_colors = Colors\RandomColor::many(count($data->logs_data['os']), ['hue'=>'red']); ?>

    let os_chart = document.getElementById('os_chart').getContext('2d');

    new Chart(os_chart, {
        type: 'doughnut',
        data: {
            labels: <?= json_encode(array_keys($data->logs_data['os'])) ?>,
            datasets: [{
                label: '',
                data: <?= json_encode(array_values($data->logs_data['os'])) ?? '[]' ?>,
                backgroundColor: <?= json_encode($os_colors) ?>,
                borderColor: <?= json_encode($os_colors) ?>,
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

                        return `${nr(value)} ${data.labels[tooltipItem.index]}`;
                    }
                }
            },
            title: {
                display: false,
            },
            legend: {
                display: true
            },
            responsive: true,
            maintainAspectRatio: false
        }
    });
    <?php endif ?>
</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
