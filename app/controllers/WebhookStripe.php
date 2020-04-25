<?php

namespace Altum\Controllers;

use Altum\Database\Database;
use Altum\Models\User;

class WebhookStripe extends Controller {

    public function index() {

        /* Initiate Stripe */
        \Stripe\Stripe::setApiKey($this->settings->stripe->secret_key);

        $payload = @file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
        $event = null;

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload, $sig_header, $this->settings->stripe->webhook_secret
            );

            if($event->type == 'checkout.session.completed') {
                $session = $event->data->object;

                $payment_id = $session->id;
                $payer_id = $session->customer;
                $payer_object = \Stripe\Customer::retrieve($payer_id);
                $payer_email = $payer_object->email;
                $payer_name = $payer_object->name;

                $payment_total = $session->display_items[0]->amount / 100;
                $payment_currency = strtoupper($session->display_items[0]->currency);

                $extra = explode('###', $session->client_reference_id);

                $user_id = (int) $extra[0];
                $package_id = (int) $extra[1];
                $payment_plan = $extra[2];
                $payment_type = $session->subscription ? 'RECURRING' : 'ONE-TIME';
                $payment_subscription_id =  $payment_type == 'RECURRING' ? 'STRIPE###' . $session->subscription : '';

                /* Get the package details */
                $package = Database::get('*', 'packages', ['package_id' => $package_id]);

                /* Just make sure the package is still existing */
                if(!$package) {
                    http_response_code(400);
                    die();
                }

                /* Make sure the transaction is not already existing */
                if(Database::exists('id', 'payments', ['payment_id' => $payment_id, 'processor' => 'STRIPE'])) {
                    http_response_code(400);
                    die();
                }

                // COMMENTED BECAUSE PRICES OF A PLAN MIGHT CHANGE BUT YOU STILL HAVE TO ACCEPT PAYMENTS FROM OLDER PRICES
                /* Make sure the paid amount equals to the current price of the plan */
//                if($package->{$payment_plan . '_price'} != $payment_total) {
//                    http_response_code(400);
//                    die();
//                }

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
                        'processor' => 'STRIPE',
                        'type' => $payment_type,
                        'plan' => $payment_plan,
                        'email' => $payer_email,
                        'payment_id' => $payment_id,
                        'subscription_id' => $session->subscription,
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
                        sprintf($this->language->global->email_notifications->new_payment_subject, 'STRIPE', $payment_total, $payment_currency),
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

                echo 'successful';
            }

        } catch(\UnexpectedValueException $e) {

            // Invalid payload
            http_response_code(400);
            exit();

        } catch(\Stripe\Error\SignatureVerification $e) {

            // Invalid signature
            http_response_code(400);
            exit();

        }

        die();

    }

}
