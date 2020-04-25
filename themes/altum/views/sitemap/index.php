<?php defined('ALTUMCODE') || die() ?>
<?= '<?xml version="1.0" encoding="UTF-8"?>' ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc><?= url() ?></loc>
    </url>
    <url>
        <loc><?= url('login') ?></loc>
    </url>
    <url>
        <loc><?= url('register') ?></loc>
    </url>
    <url>
        <loc><?= url('lost-password') ?></loc>
    </url>
    <url>
        <loc><?= url('resend-activation') ?></loc>
    </url>
    <url>
        <loc><?= url('register') ?></loc>
    </url>
    <?php while($row = $data->pages_result->fetch_object()): ?>
        <url>
            <loc><?= url('page/' . $row->url) ?></loc>
        </url>
    <?php endwhile ?>

    <?php while($row = $data->biolinks_result->fetch_object()): ?>
        <url>
            <loc><?= url($row->url) ?></loc>
        </url>
    <?php endwhile ?>
</urlset>
