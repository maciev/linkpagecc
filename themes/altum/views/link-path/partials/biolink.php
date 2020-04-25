<?php defined('ALTUMCODE') || die() ?>

<?php ob_start() ?>

<body class="link-body <?= $link->design->background_class ?>" style="<?= $link->design->background_style ?>">
    <div class="container animated fadeIn">
        <div class="row d-flex justify-content-center text-center">
            <div class="col-md-8 link-content">

                <header class="d-flex flex-column align-items-center" style="<?= $link->design->text_style ?>">

                    <?php if(!empty($link->settings->image) && file_exists(UPLOADS_PATH . 'avatars/' . $link->settings->image)): ?>
                        <img id="image" src="<?= url(UPLOADS_URL_PATH . 'avatars/' . $link->settings->image) ?>" alt="<?= \Altum\Language::get()->link->biolink->image_alt ?>" class="link-image" />
                    <?php endif ?>

                    <div class="d-flex flex-row align-items-center mt-4">
                        <h1 id="title"><?= $link->settings->title ?></h1>

                        <?php if($user->package_settings->verified): ?>
                        <span data-toggle="tooltip" title="<?= \Altum\Language::get()->global->verified ?>" class="link-verified ml-1"><i class="fa fa-check-circle fa-1x"></i></span>
                        <?php endif ?>
                    </div>

                    <p id="description"><?= $link->settings->description ?></p>
                </header>

                <main id="links" class="mt-4">

                    <?php if($links_result): ?>
                        <?php while($row = $links_result->fetch_object()): ?>

                            <?php

                            /* Check if its a scheduled link and we should show it or not */
                            if(!empty($row->start_date) && !empty($row->end_date) && (new \DateTime() < new \DateTime($row->start_date) || new \DateTime() > new \DateTime($row->end_date))) {
                                continue;
                            }

                            ?>

                            <div data-link-id="<?= $row->link_id ?>">
                                <?= \Altum\Link::get_biolink_link($row, $user)->html ?? null ?>
                            </div>

                        <?php endwhile ?>
                    <?php endif ?>
                </main>

                <footer class="link-footer">

                    <?php if($link->settings->display_branding): ?>
                        <?php if(isset($link->settings->branding, $link->settings->branding->name, $link->settings->branding->url) && !empty($link->settings->branding->name) && !empty($link->settings->branding->url)): ?>
                            <a id="branding" href="<?= $link->settings->branding->url ?>" style="<?= $link->design->text_style ?>"><?= $link->settings->branding->name ?></a>
                        <?php else: ?>
                            <a id="branding" href="<?= url() ?>" style="<?= $link->design->text_style ?>"><?= \Altum\Language::get()->link->branding ?></a>
                        <?php endif ?>
                    <?php endif ?>

                </footer>

            </div>
        </div>
    </div>
</body>

<?php ob_start() ?>
<script>
    /* Internal tracking for biolink links */
    $('[data-location-url]').on('click', event => {

        let base_url = $('[name="url"]').val();
        let url = $(event.currentTarget).data('location-url');

        $.ajax(`${base_url}${url}?no_redirect`);
    });
</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>

<?php if($user->package_settings->google_analytics && !empty($link->settings->google_analytics)): ?>
    <?php ob_start() ?>

    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?= $link->settings->google_analytics ?>"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', '<?= $link->settings->google_analytics ?>');
    </script>

    <?php \Altum\Event::add_content(ob_get_clean(), 'head') ?>
<?php endif ?>

<?php if($user->package_settings->facebook_pixel && !empty($link->settings->facebook_pixel)): ?>
    <?php ob_start() ?>

    <!-- Facebook Pixel Code -->
    <script>
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
            n.callMethod.apply(n,arguments):n.queue.push(arguments)};
            if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
            n.queue=[];t=b.createElement(e);t.async=!0;
            t.src=v;s=b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t,s)}(window, document,'script',
            'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '<?= $link->settings->facebook_pixel ?>');
        fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=<?= $link->settings->facebook_pixel ?>&ev=PageView&noscript=1"/></noscript>
    <!-- End Facebook Pixel Code -->

    <?php \Altum\Event::add_content(ob_get_clean(), 'head') ?>
<?php endif ?>

<?php $html = ob_get_clean(); ?>

<?php return (object) ['html' => $html] ?>

