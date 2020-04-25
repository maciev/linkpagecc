<?php defined('ALTUMCODE') || die() ?>

<input type="hidden" id="base_controller_url" name="base_controller_url" value="<?= url('link/' . $data->link->link_id) ?>" />

<header class="header">
    <div class="container">

        <div class="d-flex flex-column flex-md-row justify-content-between">
            <div class="d-flex align-items-center">
                <h1 class="mr-3"><?= sprintf($this->language->link->header->header, $data->link->url) ?></h1>

                <div class="custom-control custom-switch mr-3">
                    <input
                            type="checkbox"
                            class="custom-control-input"
                            id="link_is_enabled_<?= $data->link->link_id ?>"
                            data-row-id="<?= $data->link->link_id ?>"
                            onchange="ajax_call_helper(event, 'link-ajax', 'is_enabled_toggle')"
                        <?= $data->link->is_enabled ? 'checked="true"' : null ?>
                    >
                    <label class="custom-control-label clickable" data-toggle="tooltip" title="<?= $this->language->project->links->is_enabled_tooltip ?> " for="link_is_enabled_<?= $data->link->link_id ?>"></label>
                </div>

                <div class="dropdown">
                    <a href="#" data-toggle="dropdown" class="text-secondary dropdown-toggle dropdown-toggle-simple">
                        <i class="fas fa-ellipsis-v"></i>

                        <div class="dropdown-menu dropdown-menu-right">
                            <a href="<?= url('link/' . $data->link->link_id) ?>" class="dropdown-item"><i class="fa fa-pencil-alt"></i> <?= $this->language->global->edit ?></a>
                            <a href="<?= url('link/' . $data->link->link_id . '/statistics') ?>" class="dropdown-item"><i class="fa fa-chart-bar"></i> <?= $this->language->link->statistics->link ?></a>
                            <a href="#" class="dropdown-item" data-delete="<?= $this->language->global->info_message->confirm_delete ?>" data-row-id="<?= $data->link->link_id ?>"><i class="fa fa-times"></i> <?= $this->language->global->delete ?></a>
                        </div>
                    </a>
                </div>
            </div>

            <div>
                <?php if(($data->link->type == 'biolink' && $data->link->subtype == 'base') || $data->link->type == 'link'): ?>
                    <a href="<?= url('project/' . $data->link->project_id) ?>" class="btn btn-default rounded-pill mr-3"><i class="fa fa-list-ul"></i> <?= $this->language->link->header->links ?></a>

                    <?php if($data->method != 'statistics'): ?>
                    <a href="<?= url('link/' . $data->link->link_id . '/statistics') ?>" class="btn btn-info rounded-pill mr-3"><i class="fa fa-chart-bar"></i> <?= $this->language->link->statistics->link ?></a>
                    <?php endif ?>

                    <?php if($data->method != 'settings'): ?>
                    <a href="<?= url('link/' . $data->link->link_id . '/settings') ?>" class="btn btn-info rounded-pill mr-3"><i class="fa fa-cogs"></i> <?= $this->language->link->settings->link ?></a>
                    <?php endif ?>
                <?php else: ?>
                    <a href="<?= url('link/' . $data->link->biolink_id) ?>" class="btn btn-default rounded-pill mr-3"><i class="fa fa-list-ul"></i> <?= $this->language->link->header->biolinks_settings ?></a>
                <?php endif ?>
            </div>
        </div>

        <div class="d-flex align-items-center">
            <span data-toggle="tooltip" title="<?= $this->language->link->{$data->link->type}->name ?>">
                <i class="fa fa-circle fa-sm mr-3" style="color: <?= $this->language->link->{$data->link->type}->color ?>"></i>
            </span>

            <p class="text-muted mb-0">
                <?= sprintf($this->language->link->header->subheader, '<strong><a href="' . $data->link->full_url . '" target="_blank">' . $data->link->full_url . '</a></strong>') ?>

                <button
                        type="button"
                        class="btn btn-link"
                        data-toggle="tooltip"
                        title="<?= $this->language->global->clipboard_copy ?>"
                        aria-label="<?= $this->language->global->clipboard_copy ?>"
                        data-clipboard-text="<?= $data->link->full_url ?>"
                >
                    <i class="fa fa-copy"></i>
                </button>
            </p>
        </div>
    </div>
</header>

<?php require THEME_PATH . 'views/partials/ads_header.php' ?>

<section class="container">

    <?php display_notifications() ?>

    <?= $this->views['method'] ?>

</section>

<?php ob_start() ?>
<link href="<?= url(ASSETS_URL_PATH . 'css/pickr.min.css') ?>" rel="stylesheet" media="screen">
<link href="<?= url(ASSETS_URL_PATH . 'css/datepicker.min.css') ?>" rel="stylesheet" media="screen">
<link href="<?= url(ASSETS_URL_PATH . 'css/bootstrap-iconpicker.min.css') ?>" rel="stylesheet" media="screen">
<?php \Altum\Event::add_content(ob_get_clean(), 'head') ?>

<?php ob_start() ?>


<script>
    let clipboard = new ClipboardJS('[data-clipboard-text]');

    /* Delete handler for the notification */
    $('[data-delete]').on('click', event => {
        let message = $(event.currentTarget).attr('data-delete');

        if(!confirm(message)) return false;

        /* Continue with the deletion */
        ajax_call_helper(event, 'link-ajax', 'delete', (event, data) => {
            fade_out_redirect({ url: data.details.url, full: true });
        });

    });
</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
