<?php defined('ALTUMCODE') || die() ?>

<header class="header">
    <div class="container">

        <div class="d-flex flex-column flex-md-row justify-content-between">
            <h1><?= sprintf($this->language->dashboard->header->header, $this->settings->title) ?></h1>
        </div>

        <div class="row justify-content-between">
            <div class="col-6 col-md-3 mb-5 mb-md-0">
                <div class="card border-0 bg-gradient-primary text-white h-100 zoomer">
                    <div class="card-body">
                        <div class="card-title h4 mb-3"><?= $this->user->package->name ?> <i class="fa fa-box-open fa-xs"></i></div>

                        <p class="mb-0"><?= $this->language->dashboard->header->package ?></p>
                        <p class="mb-0"><small><a href="<?= url('package/upgrade') ?>" class="text-white"><?= $this->language->dashboard->header->renew ?></a></small></p>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3 mb-5 mb-md-0">
                <div class="card border-0 bg-gradient-danger text-white h-100 zoomer">
                    <div class="card-body">
                        <?php if($this->user->package_id == 'free'): ?>
                            <div class="card-title h4 mb-3"><?= $this->language->dashboard->header->package_expiration_date_never ?> <i class="fa fa-calendar fa-xs"></i></div>
                        <?php else: ?>
                            <div class="card-title h4 mb-3"><?= \Altum\Date::get_time_until($this->user->package_expiration_date) ?> <i class="fa fa-calendar fa-xs"></i></div>

                            <p class="mb-0"><?= $this->language->dashboard->header->package_expiration_date ?></p>
                        <?php endif ?>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3 mb-5 mb-md-0">
                <div class="card border-0 bg-gradient-info text-white h-100 zoomer">
                    <div class="card-body">
                        <div class="card-title h4 mb-3"><?= nr($data->links_total) ?> <i class="fa fa-link fa-xs"></i></div>

                        <p class="mb-0"><?= $this->language->dashboard->header->links ?></p>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3 mb-5 mb-md-0">
                <div class="card border-0 bg-gradient-warning text-white h-100 zoomer">
                    <div class="card-body">
                        <div class="card-title h4 mb-3"><?= nr($data->links_clicks_total) ?> <i class="fa fa-chart-line fa-xs"></i></div>

                        <p class="mb-0"><?= $this->language->dashboard->header->clicks ?></p>
                    </div>
                </div>
            </div>
        </div>


        <?php if(false): ?>
            <div class="row">
                <div class="col-1"><i class="fas fa-hourglass-end"></i></div>
                <div class="col"><?= sprintf($this->language->dashboard->header->package_expiration_date, \Altum\Date::get($this->user->package_expiration_date, true)) ?></div>
            </div>
        <?php endif ?>

    </div>
</header>

<?php require THEME_PATH . 'views/partials/ads_header.php' ?>

<section class="container">

    <?php display_notifications() ?>

    <div class="margin-top-3 d-flex justify-content-between">
        <h2><?= $this->language->dashboard->projects->header ?></h2>

        <div class="col-auto p-0">
            <?php if(false == true && $this->user->package_settings->projects_limit != -1 && $data->projects_total >= $this->user->package_settings->projects_limit): ?>
                <button type="button" data-confirm="<?= $this->language->campaign->error_message->projects_limit ?>"  class="btn btn-success rounded-pill"><i class="fas fa-plus-circle"></i> <?= $this->language->dashboard->projects->create ?></button>
            <?php else: ?>
                <button type="button" data-toggle="modal" data-target="#create_project" class="btn btn-success rounded-pill"><i class="fas fa-plus-circle"></i> <?= $this->language->dashboard->projects->create ?></button>
            <?php endif ?>
        </div>
    </div>

    <?php if($data->projects_result->num_rows): ?>
        <p class="text-muted"><?= $this->language->dashboard->projects->subheader ?></p>

        <div class="table-responsive table-custom-container">
            <table class="table table-custom">
                <thead class="thead-black">
                <tr>
                    <th><?= $this->language->dashboard->projects->name ?></th>
                    <th></th>
                    <th><?= $this->language->dashboard->projects->date ?></th>
                    <th></th>
                </tr>
                </thead>
                <tbody id="tbody_campaign">

                <?php while($row = $data->projects_result->fetch_object()): ?>
                    <?php

                    /* Get the total clicks on the project */
                    $row->clicks = $this->database->query("SELECT SUM(`clicks`) AS `total` FROM `links` WHERE `project_id` = {$row->project_id}")->fetch_object()->total;

                    ?>
                    <tr>
                        <td class="clickable" data-href="<?= url('project/' . $row->project_id) ?>"><?= $row->name ?></td>
                        <td class="clickable" data-href="<?= url('project/' . $row->project_id) ?>"><span data-toggle="tooltip" title="<?= $this->language->project->links->clicks ?>"><i class="fa fa-chart-bar custom-row-statistic-icon"></i> <span class="custom-row-statistic-number"><?= nr($row->clicks) ?></span></span></td>
                        <td class="text-muted clickable" data-href="<?= url('project/' . $row->project_id) ?>"><span><?= \Altum\Date::get($row->date, 2) ?></span></td>
                        <td>
                            <div class="dropdown">
                                <a href="#" data-toggle="dropdown" class="text-secondary dropdown-toggle dropdown-toggle-simple">
                                    <i class="fas fa-ellipsis-v"></i>

                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a href="#" class="dropdown-item" data-delete="<?= $this->language->global->info_message->confirm_delete ?>" data-row-id="<?= $row->project_id ?>"><i class="fa fa-times"></i> <?= $this->language->global->delete ?></a>
                                    </div>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endwhile ?>

                </tbody>
            </table>
        </div>

    <?php else: ?>
        <p class="text-muted"><?= $this->language->dashboard->projects->no_projects ?></p>
    <?php endif ?>

</section>

<?php ob_start() ?>
<script>
    $('[data-delete]').on('click', event => {
        let message = $(event.currentTarget).attr('data-delete');

        if(!confirm(message)) return false;

        /* Continue with the deletion */
        ajax_call_helper(event, 'project-ajax', 'delete', () => {

            /* On success delete the actual row from the DOM */
            $(event.currentTarget).closest('tr').remove();

        });

        event.preventDefault();
    });
</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
