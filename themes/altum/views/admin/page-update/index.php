<?php defined('ALTUMCODE') || die() ?>

<div class="d-flex justify-content-between">
    <div class="d-flex align-items-center">
        <h1><span class="underline mr-3"><?= $this->language->admin_page_update->header ?></span></h1>

        <?= get_admin_options_button('page', $data->page->page_id) ?>
    </div>

    <div><?= get_back_button('admin/pages') ?></div>
</div>

<?php display_notifications() ?>

<div class="card border-0 shadow-sm mt-5">
    <div class="card-body">

        <form action="" method="post" role="form">
            <input type="hidden" name="token" value="<?= \Altum\Middlewares\Csrf::get() ?>" />

            <div class="form-group">
                <label><?= $this->language->admin_pages->input->title ?></label>
                <input type="text" name="title" class="form-control" value="<?= $data->page->title ?>" />
            </div>

            <div class="form-group">
                <label id="url_label"><?= $data->page->type == 'INTERNAL' ? $this->language->admin_pages->input->url_internal : $this->language->admin_pages->input->url_external ?></label>
                <input type="text" name="url" class="form-control" value="<?= $data->page->url ?>" />
            </div>

            <div id="description_container">
                <div class="form-group">
                    <label><?= $this->language->admin_pages->input->description ?></label>
                    <textarea id="description" name="description" class="form-control"><?= $data->page->description ?></textarea>
                </div>
            </div>

            <div class="form-group">
                <label><?= $this->language->admin_pages->input->position ?></label>
                <select class="form-control" name="position">
                    <option value="1" <?= $data->page->position == '1' ? 'selected="true"' : null ?>><?= $this->language->admin_pages->input->position_top ?></option>
                    <option value="0" <?= $data->page->position == '0' ? 'selected="true"' : null ?>><?= $this->language->admin_pages->input->position_bottom ?></option>
                </select>
            </div>

            <div class="form-group">
                <label><?= $this->language->admin_pages->input->type ?></label>
                <select class="form-control" name="type">
                    <option value="INTERNAL" <?= $data->page->type == 'INTERNAL' ? 'selected="true"' : null ?>><?= $this->language->admin_pages->input->type_internal ?></option>
                    <option value="EXTERNAL" <?= $data->page->type == 'EXTERNAL' ? 'selected="true"' : null ?>><?= $this->language->admin_pages->input->type_external ?></option>
                </select>
            </div>

            <div class="text-center mt-3">
                <button type="submit" name="submit" class="btn btn-default"><?= $this->language->global->submit ?></button>
            </div>
        </form>

    </div>
</div>


<?php ob_start() ?>
<script src="<?= url(ASSETS_URL_PATH . 'js/libraries/tinymce/tinymce.min.js') ?>"></script>
<script>
    tinymce.init({
        selector: '#description',
        plugins: 'preview fullpage autolink directionality  visualblocks visualchars fullscreen image link media codesample table hr pagebreak nonbreaking toc advlist lists imagetools',
        toolbar: 'formatselect | bold italic strikethrough forecolor backcolor | link | alignleft aligncenter alignright alignjustify  | numlist bullist outdent | removeformat',
    });

    $('[name="type"]').on('change', (event) => {

        let selectedOption = $(event.currentTarget).find(':selected').attr('value');

        switch(selectedOption) {

            case 'INTERNAL':

                $('#url_label').html(<?= json_encode($this->language->admin_pages->input->url_internal) ?>);
                $('#description_container').show();

                break;

            case 'EXTERNAL':

                $('#url_label').html(<?= json_encode($this->language->admin_pages->input->url_external) ?>);
                $('#description_container').hide();

                break;
        }

    });
</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
