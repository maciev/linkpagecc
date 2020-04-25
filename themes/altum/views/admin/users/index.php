<?php defined('ALTUMCODE') || die() ?>

<div class="d-flex justify-content-between">
    <h1><span class="underline"><?= $this->language->admin_users->header ?></span></h1>

    <div class="col-auto">
        <a href="<?= url('admin/user-create') ?>" class="btn btn-success rounded-pill"><i class="fa fa-plus-circle"></i> <?= $this->language->admin_user_create->menu ?></a>
    </div>
</div>

<?php display_notifications() ?>

<div class="mt-5">
    <table id="results" class="table table-custom">
        <thead class="thead-black">
        <tr>
            <th><?= $this->language->admin_users->table->email ?></th>
            <th><?= $this->language->admin_users->table->name ?></th>
            <th><?= $this->language->admin_users->table->active ?></th>
            <th><?= $this->language->admin_users->table->package_id ?></th>
            <th><?= $this->language->admin_users->table->registration_date ?></th>
            <th class="disable_export"></th>
        </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<input type="hidden" name="url" value="<?= url('admin/users/get') ?>" />

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
    autoWidth: false,
    lengthMenu: [[25, 50, 100], [25, 50, 100]],
    buttons: [
        {
            extend: 'csvHtml5',
            exportOptions: {
                modifier: {
                    search: 'none'
                },
                columns: ':not(.disable_export)'
            }
        }
    ],
    columns: [
        {
            data: 'email',
            searchable: true,
            sortable: true
        },
        {
            data: 'name',
            searchable: true,
            sortable: true
        },
        {
            data: 'active',
            searchable: false,
            sortable: true
        },
        {
            data: 'package_id',
            searchable: false,
            sortable: false
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
    dom: "<'row'<'col-sm-12 col-md-6'<'row'<'col-auto'B><'col-auto'l>>><'col-sm-12 col-md-6'f>>" +
        "<'table-responsive table-custom-container my-3'tr>" +
        "<'row'<'col-sm-12 col-md-5 text-muted'i><'col-sm-12 col-md-7'p>>"
});
</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
