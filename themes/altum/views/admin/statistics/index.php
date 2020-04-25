<?php defined('ALTUMCODE') || die(); ?>

<?php ob_start() ?>
<link href="<?= url(ASSETS_URL_PATH . 'css/datepicker.min.css') ?>" rel="stylesheet" media="screen">
<?php \Altum\Event::add_content(ob_get_clean(), 'head') ?>

<?php ob_start() ?>
<script src="<?= url(ASSETS_URL_PATH . 'js/libraries/datepicker.min.js') ?>"></script>
<script src="<?= url(ASSETS_URL_PATH . 'js/libraries/i18n/datepicker.en.js') ?>"></script>
<script src="<?= url(ASSETS_URL_PATH . 'js/libraries/Chart.bundle.min.js') ?>"></script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>

<div class="d-flex flex-column flex-lg-row justify-content-between mb-5">
    <div>
        <h1><span class="underline"><?= sprintf($this->language->admin_statistics->header) ?></span></h1>
        <p class="text-muted"><?= $this->language->admin_statistics->subheader ?></p>
    </div>

    <div class="col-auto p-0">
        <form class="form-inline" id="datepicker_form">
            <input type="hidden" id="base_controller_url" value="<?= url('admin/statistics') ?>" />

            <div class="input-group mr-sm-2">
                <div class="input-group-prepend">
                    <div class="input-group-text"><i class="fa fa-calendar-alt"></i></div>
                </div>

                <input
                        type="text"
                        class="form-control"
                        id="datepicker_input"
                        data-range="true"
                        data-max="<?= (new \DateTime())->format('Y-m-d') ?>"
                        name="date_range"
                        value="<?= $data->date->input_date_range ?>"
                        placeholder="<?= $this->language->admin_statistics->display->date_range ?>"
                >
            </div>

            <button type="submit" class="btn btn-dark"><?= $this->language->global->date_range_selector ?></button>
        </form>
    </div>
</div>

<?php display_notifications() ?>

<?php ob_start() ?>
<script>
    /* Datepicker */
    $('#datepicker_input').datepicker({
        language: 'en',
        dateFormat: 'yyyy-mm-dd',
        autoClose: true,
        timepicker: false,
        toggleSelected: false,
        minDate: false,
        maxDate: new Date($('#datepicker_input').data('max')),
    });


    $('#datepicker_form').on('submit', (event) => {
        let date = $("#datepicker_input").val();

        let [ date_start, date_end ] = date.split(',');

        if(typeof date_end == 'undefined') {
            date_end = date_start
        }

        let base_controller_url = $('#base_controller_url').val();

        /* Redirect */
        fade_out_redirect({ url: `${base_controller_url}/${date_start}/${date_end}`, full: true });


        event.preventDefault();
    });
</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>


<?php if($this->settings->payment->is_enabled): ?>

    <?php ob_start() ?>
    <script>
        /* Display chart */
        new Chart(document.getElementById('payments').getContext('2d'), {
            type: 'line',
            data: {
                labels: <?= $logs_chart['labels'] ?? '[]' ?>,
                datasets: [{
                    label: <?= json_encode($this->language->admin_statistics->sales->chart_total_sales) ?>,
                    data: <?= $logs_chart['total_sales'] ?? '[]' ?>,
                    backgroundColor: '#237f52',
                    borderColor: '#237f52',
                    fill: false
                },
                {
                    label: <?= json_encode($this->language->admin_statistics->sales->chart_total_earned) ?>,
                    data: <?= $logs_chart['total_earned'] ?? '[]' ?>,
                    backgroundColor: '#37D28D',
                    borderColor: '#37D28D',
                    fill: false
                }]
            },
            options: {
                tooltips: {
                    mode: 'index',
                    intersect: false
                },
                title: {
                    text: '',
                    display: true
                },
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
                            },
                        }
                    }],
                    xAxes: [{
                        gridLines: {
                            display: false
                        }
                    }]
                },
                responsive: true,
                maintainAspectRatio: false
            }
        });
    </script>
    <?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
<?php endif ?>

<?php
$logs_chart = [];
$result = $this->database->query("    
    SELECT 
        formatted_date, 
        SUM(users) AS `users`, 
        SUM(projects) AS `projects`,
        SUM(links) AS `links`
    FROM (
        SELECT DATE_FORMAT(`date`, '%Y-%m-%d') AS `formatted_date`, COUNT(*) AS `users`, 0 AS `projects`, 0 AS `links`
        FROM `users`
        WHERE `date` BETWEEN '{$data->date->start_date_query}' AND DATE_ADD('{$data->date->end_date_query}', INTERVAL 1 DAY)
        GROUP BY `formatted_date`
        
        UNION ALL
        
        SELECT DATE_FORMAT(`date`, '%Y-%m-%d') AS `formatted_date`, 0 AS `users`, COUNT(*) AS `projects`, 0 AS `links`
        FROM `projects`
        WHERE `date` BETWEEN '{$data->date->start_date_query}' AND DATE_ADD('{$data->date->end_date_query}', INTERVAL 1 DAY)
        GROUP BY `formatted_date`
        
        UNION ALL
        
        SELECT DATE_FORMAT(`date`, '%Y-%m-%d') AS `formatted_date`, 0 AS `users`, 0 AS `projects`, COUNT(*) AS `links`
        FROM `links`
        WHERE `date` BETWEEN '{$data->date->start_date_query}' AND DATE_ADD('{$data->date->end_date_query}', INTERVAL 1 DAY)
        GROUP BY `formatted_date`
    ) AS `altumcode`
    
    GROUP BY `formatted_date`;
");
while($row = $result->fetch_object()) {

    $logs_chart[$row->formatted_date] = [
        'users' => $row->users,
        'projects' => $row->projects,
        'links' => $row->links
    ];

}

$logs_chart = get_chart_data($logs_chart);
?>

<div class="card border-0 shadow-sm mb-5">
    <div class="card-body">
        <h2><i class="fa fa-seedling fa-xs text-muted"></i> <?= $this->language->admin_statistics->growth->header ?></h2>
        <p class="text-muted"><?= $this->language->admin_statistics->growth->subheader ?></p>

        <div class="chart-container">
            <canvas id="growth"></canvas>
        </div>

    </div>
</div>

<?php ob_start() ?>
<script>
    /* Display chart */
    new Chart(document.getElementById('growth').getContext('2d'), {
        type: 'bar',
        data: {
            labels: <?= $logs_chart['labels'] ?>,
            datasets: [{
                label: <?= json_encode($this->language->admin_statistics->growth->chart_users) ?>,
                data: <?= $logs_chart['users'] ?? '[]' ?>,
                backgroundColor: '#007bff',
                borderColor: '#007bff',
                fill: false
            },
            {
                label: <?= json_encode($this->language->admin_statistics->growth->chart_projects) ?>,
                data: <?= $logs_chart['projects'] ?? '[]' ?>,
                backgroundColor:'#9684F7',
                borderColor:'#9684F7',
                fill: false
            },
            {
                label: <?= json_encode($this->language->admin_statistics->growth->chart_links) ?>,
                data: <?= $logs_chart['links'] ?? '[]' ?>,
                backgroundColor: '#f75581',
                borderColor: '#f75581',
                fill: false
            }]
        },
        options: {
            tooltips: {
                mode: 'index',
                intersect: false
            },
            title: {
                text: '',
                display: true
            },
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
                        },
                    }
                }],
                xAxes: [{
                    gridLines: {
                        display: false
                    }
                }]
            },
            responsive: true,
            maintainAspectRatio: false
        }
    });
</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>


<?php
/* Get data needed for statistics from the database */
$logs = [];
$logs_chart = [];
$logs_data = [
    'location'      => [],
    'os'            => [],
    'browser'       => [],
    'referer'       => []
];

$logs_result = $this->database->query("
    SELECT
         `location`,
         `os`,
         `browser`,
         `referer`,
         `count`,
         DATE_FORMAT(`date`, '%Y-%m-%d') AS `formatted_date`
    FROM
         `track_links`
    WHERE
         (`date` BETWEEN '{$data->date->start_date_query}' AND '{$data->date->end_date_query}')
    ORDER BY
        `formatted_date`
");

/* Generate the raw chart data and save logs for later usage */
while($row = $logs_result->fetch_object()) {
    $logs[] = $row;

    /* Handle if the date key is not already set */
    if(!array_key_exists($row->formatted_date, $logs_chart)) {
        $logs_chart[$row->formatted_date] = [
            'impression'        => 0,
            'unique'            => 0,
        ];
    }

    /* Distribute the data from the database row */
    $logs_chart[$row->formatted_date]['unique']++;
    $logs_chart[$row->formatted_date]['impression'] += $row->count;

    if(!array_key_exists($row->location, $logs_data['location'])) {
        $logs_data['location'][$row->location ?? 'false'] = 1;
    } else {
        $logs_data['location'][$row->location]++;
    }

    if(!array_key_exists($row->os, $logs_data['os'])) {
        $logs_data['os'][$row->os ?? 'N/A'] = 1;
    } else {
        $logs_data['os'][$row->os]++;
    }

    if(!array_key_exists($row->browser, $logs_data['browser'])) {
        $logs_data['browser'][$row->browser ?? 'N/A'] = 1;
    } else {
        $logs_data['browser'][$row->browser]++;
    }

    if(!array_key_exists($row->referer, $logs_data['referer'])) {
        $logs_data['referer'][$row->referer ?? 'false'] = 1;
    } else {
        $logs_data['referer'][$row->referer]++;
    }
}

$logs_chart = get_chart_data($logs_chart);

arsort($logs_data['referer']);
arsort($logs_data['browser']);
arsort($logs_data['os']);
arsort($logs_data['location']);
?>

<div class="card border-0 shadow-sm mb-5">
    <div class="card-body">
        <h2><i class="fa fa-bell fa-xs text-muted"></i> <?= $this->language->admin_statistics->track_links->header ?></h2>
        <p class="text-muted"><?= $this->language->admin_statistics->track_links->subheader ?></p>

        <div class="chart-container">
            <canvas id="clicks_chart"></canvas>
        </div>

    </div>
</div>

<?php ob_start() ?>
<script>
    /* Display chart */
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
            labels: <?= $logs_chart['labels'] ?>,
            datasets: [{
                label: <?= json_encode($this->language->admin_statistics->track_links->impression) ?>,
                data: <?= $logs_chart['impression'] ?? '[]' ?>,
                backgroundColor: gradient,
                borderColor: '#2BE39B',
                fill: true
            },
                {
                    label: <?= json_encode($this->language->admin_statistics->track_links->unique) ?>,
                    data: <?= $logs_chart['unique'] ?? '[]' ?>,
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
                text: <?= json_encode($this->language->admin_statistics->track_links->clicks_chart) ?>
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
</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>

<div class="row mb-5">
    <div class="col-12 col-md mr-md-4 card shadow-sm border-0">
        <div class="card-body">
            <h3><?= $this->language->admin_statistics->track_links->referer ?></h3>
            <p class="text-muted mb-3"><?= $this->language->admin_statistics->track_links->referer_help ?></p>

            <?php foreach($logs_data['referer'] as $key => $value): ?>
                <div class="row">
                    <div class="col">

                        <?php if($key == 'false'): ?>
                            <span><?= $this->language->admin_statistics->track_links->referer_direct ?></span>
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
    </div>

    <div class="col-12 col-md ml-md-4 card shadow-sm border-0">
        <div class="card-body">
            <h3><?= $this->language->admin_statistics->track_links->location ?></h3>
            <p class="text-muted mb-3"><?= $this->language->admin_statistics->track_links->location_help ?></p>

            <?php foreach($logs_data['location'] as $key => $value): ?>
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
</div>

<div class="row mb-5">
    <div class="col-12 col-md mr-md-4 card shadow-sm border-0">
        <div class="card-body">
            <h3><?= $this->language->admin_statistics->track_links->browser ?></h3>
            <p class="text-muted mb-3"><?= $this->language->admin_statistics->track_links->browser_help ?></p>

            <?php foreach($logs_data['browser'] as $key => $value): ?>
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

    <div class="col-12 col-md ml-md-4 card shadow-sm border-0">
        <div class="card-body">
            <h3><?= $this->language->admin_statistics->track_links->os ?></h3>
            <p class="text-muted mb-3"><?= $this->language->admin_statistics->track_links->os_help ?></p>

            <?php foreach($logs_data['os'] as $key => $value): ?>
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
</div>