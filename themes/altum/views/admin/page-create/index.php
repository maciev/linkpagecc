<?php defined('ALTUMCODE') || die() ?>

<div class="d-flex justify-content-between">
    <h1 class="d-flex">
        <span class="underline mr-3"><?= $this->language->admin_page_create->header ?></span>
    </h1>

    <div><?= get_back_button('admin/pages') ?></div>
</div>

<div class="card border-0 shadow-sm my-5">
    <div class="card-body">

        <form action="" method="post" role="form">
            <input type="hidden" name="token" value="<?= \Altum\Middlewares\Csrf::get() ?>" />

            <div class="row">
                <div class="col-sm-12 col-md-6">
                    <div class="form-group">
                        <label><?= $this->language->admin_pages->input->title ?></label>
                        <input type="text" name="title" class="form-control" value="" />
                    </div>
                </div>

                <div class="col-sm-12 col-md-6">
                    <div class="form-group">
                        <label><?= $this->language->admin_pages->input->position ?></label>
                        <select class="form-control" name="position">
                            <option value="1"><?= $this->language->admin_pages->input->position_top ?></option>
                            <option value="0"><?= $this->language->admin_pages->input->position_bottom ?></option>
                        </select>
                    </div>
                </div>

                <div class="col-sm-12 col-md-6">
                    <div class="form-group">
                        <label id="url_label"><?= $this->language->admin_pages->input->url ?></label>
                        <input type="text" name="url" class="form-control" value="" />
                    </div>
                </div>

                <div class="col-sm-12 col-md-6">
                    <div class="form-group">
                        <label><?= $this->language->admin_pages->input->type ?></label>
                        <select class="form-control" name="type">
                            <option value="INTERNAL"><?= $this->language->admin_pages->input->type_internal ?></option>
                            <option value="EXTERNAL"><?= $this->language->admin_pages->input->type_external ?></option>
                        </select>
                    </div>
                </div>

                <div id="description_container" class="col-12">
                    <div class="form-group">
                        <label><?= $this->language->admin_pages->input->description ?></label>
                        <textarea id="description" name="description" class="form-control"></textarea>
                    </div>
                </div>

            </div>

            <div class="text-center">
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
