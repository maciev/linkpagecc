<?php defined('ALTUMCODE') || die() ?>

<div class="d-flex justify-content-between">
    <h1><span class="underline"><?= $this->language->admin_links->header ?></span></h1>
</div>
<p class="text-muted"><?= $this->language->admin_links->subheader ?></p>

<?php display_notifications() ?>

<div class="mt-5">
    <table id="results" class="table table-custom">
        <thead class="thead-black">
        <tr>
            <th><?= $this->language->admin_links->table->type ?></th>
            <th><?= $this->language->admin_links->table->email ?></th>
            <th><?= $this->language->admin_links->table->url ?></th>
            <th><?= $this->language->admin_links->table->clicks ?></th>
            <th><?= $this->language->admin_links->table->is_enabled ?></th>
            <th><?= $this->language->admin_links->table->date ?></th>
            <th></th>
        </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<input type="hidden" name="url" value="<?= url('admin/links/get') ?>" />

<?php ob_start() ?>
<link href="<?= url(ASSETS_URL_PATH . 'css/datatables.min.css') ?>" rel="stylesheet" media="screen">
<?php \Altum\Event::add_content(ob_get_clean(), 'head') ?>

<?php ob_start() ?>
<script src="<?= url(ASSETS_URL_PATH . 'js/libraries/datatables.min.js') ?>"></script>
<script>
let datatable = $('#results').DataTable({
    language: <?= json_encode($this->language->datatable) ?>,
    serverSide: true,
    processing: true,
    ajax: {
        url: $('[name="url"]').val(),
        type: 'POST'
    },
    lengthMenu: [[25, 50, 100], [25, 50, 100]],
    columns: [
        {
            data: 'type',
            searchable: false,
            sortable: false
        },
        {
            data: 'email',
            searchable: true,
            sortable: false
        },
        {
            data: 'url',
            searchable: true,
            sortable: false
        },
        {
            data: 'clicks',
            searchable: true,
            sortable: true
        },
        {
            data: 'is_enabled',
            searchable: false,
            sortable: true
        },
        {
            data: 'date',
            searchable: false,
            sortable: true
        },
        {
            data: 'actions',
            searchable: false,
            sortable: false
        }
    ],
    responsive: true,
    drawCallback: () => {
        $('[data-toggle="tooltip"]').tooltip();
    },
    dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
        "<'table-responsive table-custom-container my-3'tr>" +
        "<'row'<'col-sm-12 col-md-5 text-muted'i><'col-sm-12 col-md-7'p>>"
});
</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
