<?php

function get_email_template($email_template_subject_array = [], $email_template_subject, $email_template_body_array = [], $email_template_body) {

    $email_template_subject = str_replace(
        array_keys($email_template_subject_array),
        array_values($email_template_subject_array),
        $email_template_subject
    );

    $email_template_body = str_replace(
        array_keys($email_template_body_array),
        array_values($email_template_body_array),
        $email_template_body
    );

    return (object) [
        'subject' => $email_template_subject,
        'body' => $email_template_body
    ];
}

function send_server_mail($to, $from, $title, $content) {

    $headers = "From: " . strip_tags($from) . "\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

    /* Check if receipient is array or not */
    $to_processed = $to;

    if(is_array($to)) {
        $to_processed = '';

        foreach($to as $address) {
            $to_processed .= ',' . $address;
        }

    }

    return mail($to_processed, $title, $content, $headers);
}

function send_mail($settings, $to, $title, $content, $test = false) {

    /* Templating for the title */
    $replacers = [
        '{{WEBSITE_TITLE}}' => $settings->title
    ];

    $title = str_replace(
        array_keys($replacers),
        array_values($replacers),
        $title
    );

    /* Template and content preparing */
    $email_template_raw = file_get_contents(THEME_PATH . 'views/partials/email.html');

    $replacers = [
        '{{CONTENT}}'   => $content,
        '{{URL}}'       => url(),
        '{{WEBSITE_TITLE}}' => $settings->title,
        '{{HEADER}}'    => '<a href="' . url() . '">' . (!empty($settings->logo) ? '<img src="' . url(UPLOADS_URL_PATH . 'logo/' . $settings->logo) . '" class="logo" />' : '<h2>' . $settings->title .  '</h2>') . '</a>',
        '{{FOOTER}}'    => 'Copyright © <a href="' . url() . '">' . $settings->title . '</a>'
    ];

    $email_template = str_replace(
        array_keys($replacers),
        array_values($replacers),
        $email_template_raw
    );


    if(!empty($settings->smtp->host)) {

        try {
            $mail = new \PHPMailer\PHPMailer\PHPMailer();
            $mail->CharSet = 'UTF-8';
            $mail->isSMTP();
            $mail->SMTPDebug = 0;

            if ($settings->smtp->encryption != '0') {
                $mail->SMTPSecure = $settings->smtp->encryption;
            }

            $mail->SMTPAuth = $settings->smtp->auth;
            $mail->isHTML(true);

            $mail->Host = $settings->smtp->host;
            $mail->Port = $settings->smtp->port;
            $mail->Username = $settings->smtp->username;
            $mail->Password = $settings->smtp->password;

            $mail->setFrom($settings->smtp->from, $settings->title);
            $mail->addReplyTo($settings->smtp->from, $settings->title);

            /* Check if receipient is array or not */
            if(is_array($to)) {
                foreach($to as $address) {
                    $mail->addAddress($address);
                }
            } else {
                $mail->addAddress($to);
            }

            $mail->Subject = $title;

            $mail->Body = $email_template;

            $send = $mail->send();

            return $test ? $mail : $send;

        } catch (Exception $e) {

            return $test ? $mail : false;

        }

    } else {
        return send_server_mail($to, $settings->smtp->from, $title, $email_template);
    }

}
