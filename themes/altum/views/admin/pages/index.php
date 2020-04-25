<?php defined('ALTUMCODE') || die() ?>

<div class="d-flex justify-content-between">
    <h1><span class="underline"><?= $this->language->admin_pages->header ?></span></h1>

    <div class="col-auto">
        <a href="<?= url('admin/page-create') ?>" class="btn btn-success rounded-pill"><i class="fa fa-plus-circle"></i> <?= $this->language->admin_pages->add_new ?></a>
    </div>
</div>

<?php display_notifications() ?>

<div class="mt-5 table-responsive table-custom-container">
    <table class="table table-custom">
        <thead class="thead-black">
        <tr>
            <th><?= $this->language->admin_pages->pages->title ?></th>
            <th><?= $this->language->admin_pages->pages->url ?></th>
            <th><?= $this->language->admin_pages->pages->position ?></th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php while($row = $data->pages_result->fetch_object()): ?>

            <tr>
                <td><?= $row->title ?></td>
                <td>
                    <a href="<?= (strpos($row->url, 'http://') !== false || strpos($row->url, 'https://') !== false) ? $row->url : url('page/' . $row->url) ?>" target="_blank">
                        <i class="fa fa-sm fa-link"></i> <?= $row->url ?>
                    </a>
                </td>
                <td class="d-flex flex-column">
                    <?= $row->position == '0' ? $this->language->admin_pages->pages->position_bottom : $this->language->admin_pages->pages->position_top ?>
                    <small class="text-muted"><?= $this->language->admin_pages->input->{'type_' . strtolower($row->type)} ?></small>
                </td>
                <td><?= get_admin_options_button('page', $row->page_id) ?></td>
            </tr>

        <?php endwhile ?>
        </tbody>
    </table>
</div>

