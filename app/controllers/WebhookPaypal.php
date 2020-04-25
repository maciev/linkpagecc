<?php

namespace Altum\Controllers;

use Altum\Database\Database;
use Altum\Models\User;

class WebhookPaypal extends Controller {

    public function index() {

        $payload = @file_get_contents('php://input');
        $data = json_decode($payload);

        if($payload && $data && $data->event_type == 'PAYMENT.SALE.COMPLETED') {

            /* Initiate paypal */
            $paypal = new \PayPal\Rest\ApiContext(new \PayPal\Auth\OAuthTokenCredential($this->settings->paypal->client_id, $this->settings->paypal->secret));
            $paypal->setConfig(['mode' => $this->settings->paypal->mode]);

            /* Get the billing agreement */
            try {
                $agreement = \PayPal\Api\Agreement::get($data->resource->billing_agreement_id, $paypal);
            } catch (Exception $exception) {

                /* Output errors properly */
                if (DEBUG) {
                    error_log($exception->getCode());
                    error_log($exception->getData());
                }

                http_response_code(400);

            }

            /* Get the needed details for the processing */
            $payer_info = $agreement->getPayer()->getPayerInfo();
            $payer_email = $payer_info->getEmail();
            $payer_name = $payer_info->getFirstName() . ' ' . $payer_info->getLastName();
            $payer_id = $payer_info->getPayerId();
            $subscription_id = $agreement->getId();

            $payment_id = $data->resource->id;
            $payment_total = $data->resource->amount->total;
            $payment_currency = $data->resource->amount->currency;

            $extra = explode('###', $agreement->getDescription());

            $user_id = (int) $extra[0];
            $package_id = (int) $extra[1];
            $payment_plan = $extra[2];
            $payment_type = 'RECURRING';
            $payment_subscription_id = 'PAYPAL###' . $subscription_id;

            /* Get the package details */
            $package = Database::get('*', 'packages', ['package_id' => $package_id]);

            /* Just make sure the package is still existing */
            if(!$package) {
                http_response_code(400);
                die();
            }

            /* Make sure the transaction is not already existing */
            if(Database::exists('id', 'payments', ['payment_id' => $payment_id, 'processor' => 'PAYPAL'])) {
                http_response_code(400);
                die();
            }

            // COMMENTED BECAUSE PRICES OF A PLAN MIGHT CHANGE BUT YOU STILL HAVE TO ACCEPT PAYMENTS FROM OLDER PRICES
            /* Make sure the paid amount equals to the current price of the plan */
//            if($package->{$payment_plan . '_price'} != $payment_total) {
//                http_response_code(400);
//                die();
//            }

            /* Make sure the account still exists */
            $user = Database::get(['user_id', 'payment_subscription_id'], 'users', ['user_id' => $user_id]);

            if(!$user) {
                http_response_code(400);
                die();
            }

            /* Unsubscribe from the previous plan if needed */
            if(!empty($user->payment_subscription_id) && $user->payment_subscription_id != $payment_subscription_id) {
                try {
                    (new User(['settings' => $this->settings]))->cancel_subscription($user_id);
                } catch (\Exception $exception) {

                    /* Output errors properly */
                    if (DEBUG) {
                        echo $exception->getCode() . '-' . $exception->getMessage();

                        die();
                    }
                }
            }

            /* Add a log into the database */
            Database::insert(
                'payments',
                [
                    'user_id' => $user_id,
                    'package_id' => $package_id,
                    'processor' => 'PAYPAL',
                    'type' => $payment_type,
                    'plan' => $payment_plan,
                    'email' => $payer_email,
                    'payment_id' => $payment_id,
                    'subscription_id' => $subscription_id,
                    'payer_id' => $payer_id,
                    'name' => $payer_name,
                    'amount' => $payment_total,
                    'currency' => $payment_currency,
                    'date' => \Altum\Date::$date
                ]
            );

            /* Send notification to admin if needed */
            if($this->settings->email_notifications->new_payment && !empty($this->settings->email_notifications->emails)) {

                send_mail(
                    $this->settings,
                    explode(',', $this->settings->email_notifications->emails),
                    sprintf($this->language->global->email_notifications->new_payment_subject, 'PAYPAL', $payment_total, $payment_currency),
                    sprintf($this->language->global->email_notifications->new_payment_body, $payment_total, $payment_currency)
                );

            }

            /* Update the user with the new package */
            switch($payment_plan) {
                case 'monthly':
                    $package_expiration_date = (new \DateTime())->modify('+30 days')->format('Y-m-d H:i:s');
                    break;

                case 'annual':
                    $package_expiration_date = (new \DateTime())->modify('+12 months')->format('Y-m-d H:i:s');
                    break;
            }

            Database::update(
                'users',
                [
                    'package_id' => $package_id,
                    'package_expiration_date' => $package_expiration_date,
                    'package_settings' => $package->settings,
                    'payment_subscription_id' => $payment_subscription_id
                ],
                [
                    'user_id' => $user_id
                ]
            );

            http_response_code(200);
        }

        die();

    }

}
