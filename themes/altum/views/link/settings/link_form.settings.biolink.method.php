<?php defined('ALTUMCODE') || die() ?>

<form name="update_biolink_" method="post" role="form">
    <input type="hidden" name="token" value="<?= \Altum\Middlewares\Csrf::get() ?>" required="required" />
    <input type="hidden" name="request_type" value="update" />
    <input type="hidden" name="type" value="biolink" />
    <input type="hidden" name="subtype" value="link" />
    <input type="hidden" name="link_id" value="<?= $row->link_id ?>" />

    <div class="d-flex justify-content-between">
        <span class="h4"><?= $this->language->link->links->settings ?></span>
    </div>

    <div class="notification-container"></div>

    <div class="form-group">
        <label><i class="fa fa-signature"></i> <?= $this->language->create_biolink_link_modal->input->location_url ?></label>
        <input type="text" class="form-control" name="location_url" value="<?= $row->location_url ?>" placeholder="<?= $this->language->create_biolink_link_modal->input->location_url_placeholder ?>" required="required" />
    </div>

    <div class="form-group">
        <label><i class="fa fa-link"></i> <?= $this->language->create_biolink_link_modal->input->url ?></label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text"><?= url() ?></span>
            </div>
            <input type="text" class="form-control" name="url" placeholder="<?= $this->language->create_biolink_link_modal->input->url_placeholder ?>" value="<?= $row->url ?>" />
        </div>
        <small class="text-muted"><?= $this->language->create_biolink_link_modal->input->url_help ?></small>
    </div>

    <div class="custom-control custom-switch">
        <input id="schedule_<?= $row->link_id ?>" name="schedule" type="checkbox" class="custom-control-input" <?= !empty($row->start_date) && !empty($row->end_date) ? 'checked="checked"' : null ?> <?= !$this->user->package_settings->scheduling ? 'disabled="disabled"': null ?>>
        <label class="custom-control-label" for="schedule_<?= $row->link_id ?>"><?= $this->language->link->settings->schedule ?></label>
        <small class="form-text text-muted"><?= $this->language->link->settings->schedule_help ?></small>
    </div>

    <div class="row mt-3 schedule_container <?= !$this->user->package_settings->scheduling ? 'container-disabled': null ?>" style="display: none;">
        <div class="col">
            <div class="form-group">
                <label><i class="fa fa-clock"></i> <?= $this->language->link->settings->start_date ?></label>
                <input
                        type="text"
                        class="form-control"
                        name="start_date"
                        value="<?= $row->start_date ?>"
                        placeholder="<?= $this->language->link->settings->start_date ?>"
                        autocomplete="off"
                >
            </div>
        </div>

        <div class="col">
            <div class="form-group">
                <label><i class="fa fa-clock"></i> <?= $this->language->link->settings->end_date ?></label>
                <input
                        type="text"
                        class="form-control"
                        name="end_date"
                        value="<?= $row->end_date ?>"
                        placeholder="<?= $this->language->link->settings->end_date ?>"
                        autocomplete="off"
                >
            </div>
        </div>
    </div>

    <div class="form-group">
        <label><i class="fa fa-paragraph"></i> <?= $this->language->create_biolink_link_modal->input->name ?></label>
        <input type="text" name="name" class="form-control" value="<?= $row->settings->name ?>" required="required" />
    </div>

    <div class="form-group">
        <label><i class="fa fa-globe"></i> <?= $this->language->create_biolink_link_modal->input->icon ?></label>
        <div class="input-group">
            <span class="input-group-prepend">
                <button class="btn btn-secondary" data-icon="<?= $row->settings->icon ?>" data-iconset="fontawesome5" role="iconpicker"></button>
            </span>

            <input type="text" name="icon" class="form-control" value="<?= $row->settings->icon ?>" placeholder="<?= $this->language->create_biolink_link_modal->input->icon_placeholder ?>" />
        </div>
        <small class="text-muted"><?= $this->language->create_biolink_link_modal->input->icon_help ?></small>
    </div>

    <div class="<?= !$this->user->package_settings->custom_colored_links ? 'container-disabled': null ?>">
        <div class="form-group">
            <label><i class="fa fa-paint-brush"></i> <?= $this->language->create_biolink_link_modal->input->text_color ?></label>
            <input type="hidden" name="text_color" class="form-control" value="<?= $row->settings->text_color ?>" required="required" />
            <div class="text_color_pickr"></div>
        </div>

        <div class="form-group">
            <label><i class="fa fa-fill"></i> <?= $this->language->create_biolink_link_modal->input->background_color ?></label>
            <input type="hidden" name="background_color" class="form-control" value="<?= $row->settings->background_color ?>" required="required" />
            <div class="background_color_pickr"></div>
        </div>

        <div class="custom-control custom-switch mr-3 mb-3">
            <input
                    type="checkbox"
                    class="custom-control-input"
                    id="outline_<?= $row->link_id ?>"
                    name="outline"
                <?= $row->settings->outline ? 'checked="true"' : null ?>
            >
            <label class="custom-control-label clickable" for="outline_<?= $row->link_id ?>"><?= $this->language->create_biolink_link_modal->input->outline ?></label>
        </div>

        <div class="form-group">
            <label><?= $this->language->create_biolink_link_modal->input->border_radius ?></label>
            <select name="border_radius" class="form-control">
                <option value="straight" <?= $row->settings->border_radius == 'straight' ? 'selected="true"' : null ?>><?= $this->language->create_biolink_link_modal->input->border_radius_straight ?></option>
                <option value="round" <?= $row->settings->border_radius == 'round' ? 'selected="true"' : null ?>><?= $this->language->create_biolink_link_modal->input->border_radius_round ?></option>
                <option value="rounded" <?= $row->settings->border_radius == 'rounded' ? 'selected="true"' : null ?>><?= $this->language->create_biolink_link_modal->input->border_radius_rounded ?></option>
            </select>
        </div>

        <div class="form-group">
            <label><?= $this->language->create_biolink_link_modal->input->animation ?></label>
            <select name="animation" class="form-control">
                <option value="false" <?= !$row->settings->animation ? 'selected="true"' : null ?>>-</option>
                <option value="bounce" <?= $row->settings->animation == 'bounce' ? 'selected="true"' : null ?>>bounce</option>
                <option value="tada" <?= $row->settings->animation == 'tada' ? 'selected="true"' : null ?>>tada</option>
                <option value="wobble" <?= $row->settings->animation == 'wobble' ? 'selected="true"' : null ?>>wobble</option>
                <option value="swing" <?= $row->settings->animation == 'swing' ? 'selected="true"' : null ?>>swing</option>
                <option value="shake" <?= $row->settings->animation == 'shake' ? 'selected="true"' : null ?>>shake</option>
                <option value="rubberBand" <?= $row->settings->animation == 'rubberBand' ? 'selected="true"' : null ?>>rubberBand</option>
                <option value="pulse" <?= $row->settings->animation == 'pulse' ? 'selected="true"' : null ?>>pulse</option>
                <option value="flash" <?= $row->settings->animation == 'flash' ? 'selected="true"' : null ?>>flash</option>
            </select>
        </div>
    </div>

    <div class="text-center mt-4">
        <button type="submit" name="submit" class="btn btn-primary"><?= $this->language->global->update ?></button>
    </div>
</form>
