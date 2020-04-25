<?php

namespace Altum\Controllers;

use Altum\Database\Database;
use Altum\Date;
use Altum\Middlewares\Authentication;
use Altum\Middlewares\Csrf;
use Altum\Models\Package;
use Altum\Models\User;
use Exception;
use PayPal\Api\Agreement;
use PayPal\Api\Amount;
use PayPal\Api\Currency;
use PayPal\Api\FlowConfig;
use PayPal\Api\InputFields;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\MerchantPreferences;
use PayPal\Api\Patch;
use PayPal\Api\PatchRequest;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\PaymentDefinition;
use PayPal\Api\PaymentExecution;
use PayPal\Api\Plan;
use PayPal\Api\Presentation;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Api\WebProfile;
use PayPal\Common\PayPalModel;

class Pay extends Controller {

    public function index() {

        Authentication::guard();

        if(!$this->settings->payment->is_enabled) {
            redirect();
        }

        $this->package_id = isset($this->params[0]) ? $this->params[0] : false;
        $this->return_type = isset($this->params[1]) && in_array($this->params[1], ['paypal-success', 'paypal-cancel', 'stripe-success', 'stripe-cancel']) ? $this->params[1] : false;

        /* Make sure it is either the trial / free package or normal packages */
        switch($this->package_id) {

            case 'free':

                /* Get the current settings for the free package */
                $this->package = $this->settings->package_free;

                break;

            case 'trial':

                /* Get the current settings for the trial package */
                $this->package = $this->settings->package_trial;

                break;

            default:

                $this->package_id = (int) $this->package_id;

                /* Check if package exists */
                if(!$this->package = (new Package(['settings' => $this->settings]))->get_package_by_id($this->package_id)) {
                    redirect('package');
                }

                break;
        }

        /* Make sure the package is enabled */
        if(!$this->package->is_enabled) {
            redirect('package');
        }

        /* Form submission processing */
        /* Make sure that this only runs on user click submit post and not on callbacks / webhooks */
        if(!empty($_POST) && !$this->return_type) {

            /* Check for any errors */
            if(!Csrf::check()) {
                $_SESSION['error'][] = $this->language->global->error_message->invalid_csrf_token;
            }

            switch($this->package_id) {

                case 'free':

                    redirect('pay/' . $this->package_id);

                    break;

                case 'trial':

                    if($this->user->package_trial_done) {
                        redirect('pay/' . $this->package_id);
                    }

                    break;

                default:

                    /* Make sure the chosen option comply */
                    if(!in_array($_POST['payment_plan'], ['monthly', 'annual'])) {
                        redirect('pay/' . $this->package_id);
                    }

                    if(!in_array($_POST['payment_processor'], ['paypal', 'stripe'])) {
                        redirect('pay/' . $this->package_id);
                    } else {

                        /* Make sure the payment processor is active */
                        switch($_POST['payment_processor']) {
                            case 'paypal':

                                if(!$this->settings->paypal->is_enabled) {
                                    redirect('pay/' . $this->package_id);
                                }

                                break;

                            case 'stripe':

                                if(!$this->settings->stripe->is_enabled) {
                                    redirect('pay/' . $this->package_id);
                                }

                                break;
                        }

                    }

                    if(!in_array($_POST['payment_type'], ['one-time', 'recurring'])) {
                        redirect('pay/' . $this->package_id);
                    }


                    break;
            }

            if(empty($_SESSION['error'])) {

                switch($this->package_id) {

                    case 'trial':

                        /* Determine the expiration date of the package */
                        $package_expiration_date = (new \DateTime())->modify('+' . $this->package->days . ' days')->format('Y-m-d H:i:s');
                        $package_settings = json_encode($this->settings->package_trial->settings);

                        Database::$database->query("UPDATE `users` SET `package_id` = 'trial', `package_settings` = '{$package_settings}', `package_expiration_date` = '{$package_expiration_date}', `package_trial_done` = '1' WHERE `user_id` = {$this->user->user_id}");

                        /* Set a message */
                        $_SESSION['success'][] = $this->language->pay->success_message->package_trial_start;

                        redirect('dashboard');

                        break;

                    default:

                        switch($_POST['payment_processor']) {
                            case 'paypal':

                                $this->paypal_create();

                                break;

                            case 'stripe':

                                $stripe_session = $this->stripe_create();

                                break;
                        }

                        break;

                }

            }

        }

        /* Include the detection of paypal callbacks processing */
        $this->paypal_process();

        /* Include the detection of stripe callbacks processing */
        $this->stripe_process();

        /* Prepare the View */
        $data = [
            'package_id'    => $this->package_id,
            'package'       => $this->package,
            'stripe_session' => $stripe_session ?? false
        ];

        $view = new \Altum\Views\View('pay/index', (array) $this);

        $this->addViewContent('content', $view->run($data));

    }

    private function paypal_create() {

        /* Initiate paypal */
        $paypal = new \PayPal\Rest\ApiContext(new \PayPal\Auth\OAuthTokenCredential($this->settings->paypal->client_id, $this->settings->paypal->secret));
        $paypal->setConfig(['mode' => $this->settings->paypal->mode]);

        /* Payment details */
        $product = $this->package->name;
        $price = (float) $this->package->{$_POST['payment_plan'] . '_price'};
        $shipping = 0;

        switch($_POST['payment_type']) {
            case 'one-time':

                /* Payment experience */
                $flowConfig = new FlowConfig();
                $flowConfig->setLandingPageType('Billing');
                $flowConfig->setUserAction('commit');
                $flowConfig->setReturnUriHttpMethod('GET');

                $presentation = new Presentation();
                $presentation->setBrandName($this->settings->payment->brand_name);

                $inputFields = new InputFields();
                $inputFields->setAllowNote(true)
                    ->setNoShipping(1)
                    ->setAddressOverride(0);

                $webProfile = new WebProfile();
                $webProfile->setName($this->settings->payment->brand_name . uniqid())
                    ->setFlowConfig($flowConfig)
                    ->setPresentation($presentation)
                    ->setInputFields($inputFields)
                    ->setTemporary(true);

                /* Create the experience profile */
                try {
                    $createdProfileResponse = $webProfile->create($paypal);
                } catch (Exception $exception) {

                    /* Output errors properly */
                    if (DEBUG) {
                        echo $exception->getCode();
                        echo $exception->getData();

                        die();
                    } else {

                        $_SESSION['error'][] = $this->language->pay->error_message->failed_payment;
                        redirect('pay/' . $this->package_id);

                    }

                }

                $payer = new Payer();
                $payer->setPaymentMethod('paypal');

                $item = new Item();
                $item->setName($product)
                    ->setCurrency($this->settings->payment->currency)
                    ->setQuantity(1)
                    ->setPrice($price);

                $itemList = new ItemList();
                $itemList->setItems([$item]);

                $amount = new Amount();
                $amount->setCurrency($this->settings->payment->currency)
                    ->setTotal($price);

                $transaction = new Transaction();
                $transaction->setAmount($amount)
                    ->setItemList($itemList)
                    ->setInvoiceNumber(uniqid());

                $redirectUrls = new RedirectUrls();
                $redirectUrls->setReturnUrl(url('pay/' . $this->package_id . '/paypal-success?payment_plan=' . $_POST['payment_plan']))
                    ->setCancelUrl(url('pay/' . $this->package_id . '/paypal-cancel'));

                $payment = new Payment();
                $payment->setIntent('sale')
                    ->setPayer($payer)
                    ->setRedirectUrls($redirectUrls)
                    ->setTransactions([$transaction])
                    ->setExperienceProfileId($createdProfileResponse->getId());

                try {
                    $payment->create($paypal);
                } catch (Exception $exception) {

                    /* Output errors properly */
                    if (DEBUG) {
                        echo $exception->getCode();
                        echo $exception->getData();

                        die();
                    } else {

                        $_SESSION['error'][] = $this->language->pay->error_message->failed_payment;
                        redirect('pay/' . $this->package_id);

                    }
                }

                $payment_url = $payment->getApprovalLink();

                header('Location: ' . $payment_url);

                break;

            case 'recurring':

                $plan = new Plan();
                $plan->setName($product)
                    ->setDescription($product)
                    ->setType('fixed');

                /* Set billing plan definitions */
                $payment_definition = new PaymentDefinition();
                $payment_definition->setName('Regular Payments')
                    ->setType('REGULAR')
                    ->setFrequency($_POST['payment_plan'] == 'monthly' ? 'Month' : 'Year')
                    ->setFrequencyInterval('1')
                    ->setCycles($_POST['payment_plan'] == 'monthly' ? '12' : '1')
                    ->setAmount(new Currency(['value' => $price, 'currency' => $this->settings->payment->currency]));


                /* Set merchant preferences */
                $merchant_preferences = new MerchantPreferences();
                $merchant_preferences->setReturnUrl(url('pay/' . $this->package_id . '/paypal-success?payment_plan=' . $_POST['payment_plan'] . '&payment_type=recurring'))
                    ->setCancelUrl(url('pay/' . $this->package_id . '/paypal-cancel' . '&payment_type=recurring'))
                    ->setAutoBillAmount('yes')
                    ->setInitialFailAmountAction('CONTINUE')
                    ->setMaxFailAttempts('0');

                $plan->setPaymentDefinitions([$payment_definition]);
                $plan->setMerchantPreferences($merchant_preferences);

                /* Create the plan */
                try {
                    $plan = $plan->create($paypal);
                } catch (Exception $exception) {

                    /* Output errors properly */
                    if (DEBUG) {
                        echo '1';
                        echo $exception->getCode();
                        echo $exception->getData();

                        die();
                    } else {

                        $_SESSION['error'][] = $this->language->pay->error_message->failed_payment;
                        redirect('pay/' . $this->package_id);

                    }
                }

                /* Make sure to activate the plan */
                try {
                    $patch = new Patch();
                    $value = new PayPalModel('{"state":"ACTIVE"}');
                    $patch->setOp('replace')
                        ->setPath('/')
                        ->setValue($value);
                    $patchRequest = new PatchRequest();
                    $patchRequest->addPatch($patch);
                    $plan->update($patchRequest, $paypal);
                    $plan = Plan::get($plan->getId(), $paypal);
                } catch (Exception $exception) {

                    /* Output errors properly */
                    if (DEBUG) {
                        echo '2';
                        echo $exception->getCode();
                        echo $exception->getData();

                        die();
                    } else {

                        $_SESSION['error'][] = $this->language->pay->error_message->failed_payment;
                        redirect('pay/' . $this->package_id);

                    }
                }

                /* Start creating the agreement */
                $agreement = new Agreement();
                $agreement->setName($product)
                    ->setDescription($this->user->user_id . '###' . $this->package_id . '###' . $_POST['payment_plan'] . '###' . time())
                    ->setStartDate((new \DateTime())->modify('+5 minute')->format(DATE_ISO8601));

                /* Set the plan id to the agreement */
                $agreement_plan = new Plan();
                $agreement_plan->setId($plan->getId());
                $agreement->setPlan($agreement_plan);

                /* Add Payer */
                $payer = new Payer();
                $payer->setPaymentMethod('paypal');
                $agreement->setPayer($payer);

                /* Create the agreement */
                try {
                    $agreement = $agreement->create($paypal);
                } catch (Exception $exception) {

                    /* Output errors properly */
                    if (DEBUG) {
                        echo '3';
                        echo $exception->getCode();
                        echo $exception->getData();

                        die();
                    } else {

                        $_SESSION['error'][] = $this->language->pay->error_message->failed_payment;
                        redirect('pay/' . $this->package_id);

                    }
                }

                $payment_url = $agreement->getApprovalLink();

                header('Location: ' . $payment_url);

                break;
        }


    }

    private function paypal_process() {

        /* Initiate paypal */
        $paypal = new \PayPal\Rest\ApiContext(new \PayPal\Auth\OAuthTokenCredential($this->settings->paypal->client_id, $this->settings->paypal->secret));
        $paypal->setConfig(['mode' => $this->settings->paypal->mode]);

        /* Return confirmation processing one time payment */
        if($this->return_type && $this->return_type == 'paypal-success' && isset($_GET['payment_plan'])) {
            $payment_plan = $_GET['payment_plan'];

            if(isset($_GET['paymentId'], $_GET['PayerID'])) {

                /* Initiate paypal */
                $paypal = new \PayPal\Rest\ApiContext(new \PayPal\Auth\OAuthTokenCredential($this->settings->paypal->client_id, $this->settings->paypal->secret));
                $paypal->setConfig(['mode' => $this->settings->paypal->mode]);


                $payment_id = $_GET['paymentId'];
                $payer_id = $_GET['PayerID'];
                $payment_type = 'ONE-TIME';

                $subscription_id = '';
                $payment_subscription_id =  '';

                try {
                    $payment = Payment::get($payment_id, $paypal);

                    $payer_info = $payment->getPayer()->getPayerInfo();
                    $payer_email = $payer_info->getEmail();
                    $payer_name = $payer_info->getFirstName() . ' ' . $payer_info->getLastName();

                    $payment_total = $payment->getTransactions()[0]->getAmount()->getTotal();
                    $payment_currency = $payment->getTransactions()[0]->getAmount()->getCurrency();

                    /* Execute the payment */
                    $execute = new PaymentExecution();
                    $execute->setPayerId($payer_id);

                    $result = $payment->execute($execute, $paypal);

                    /* Get status after execution */
                    $payment_status = $payment->getState();

                } catch (Exception $exception) {

                    /* Output errors properly */
                    if (DEBUG) {
                        echo $exception->getCode();
                        echo $exception->getData();

                        die();
                    } else {

                        $_SESSION['error'][] = $this->language->pay->error_message->failed_payment;
                        redirect('pay/' . $this->package_id);

                    }
                }

                /* Make sure the transaction is not already existing */
                if (Database::exists('id', 'payments', ['payment_id' => $payment_id, 'processor' => 'PAYPAL'])) {
                    redirect('pay/' . $this->package_id);
                }

                /* Make sure the payment is approved */
                if ($payment_status != 'approved') {
                    $_SESSION['error'][] = $this->language->pay->error_message->failed_payment;
                    redirect('pay/' . $this->package_id);
                }

                /* Make sure the chosen option comply */
                if (!in_array($payment_plan, ['monthly', 'annual'])) {
                    redirect('pay/' . $this->package_id);
                }

                // COMMENTED BECAUSE PRICES OF A PLAN MIGHT CHANGE BUT YOU STILL HAVE TO ACCEPT PAYMENTS FROM OLDER PRICES
                /* Make sure the paid amount equals to the current price of the plan */
//                if ($this->package->{$payment_plan . '_price'} != $payment_total) {
//                    $_SESSION['error'][] = $this->language->pay->error_message->failed_payment;
//                    redirect('pay/' . $this->package_id);
//                }

                /* Unsubscribe from the previous plan if needed */
                if(!empty($this->user->payment_subscription_id) && $this->user->payment_subscription_id != $payment_subscription_id) {
                    try {
                        (new User(['settings' => $this->settings, 'user' => $this->user]))->cancel_subscription();
                    } catch (\Exception $exception) {

                        /* Output errors properly */
                        if (DEBUG) {
                            echo $exception->getCode() . '-' . $exception->getMessage();

                            die();
                        } else {
                            $_SESSION['error'][] = $this->language->pay->error_message->failed_payment;
                            redirect('pay/' . $this->package_id);
                        }

                    }
                }

                /* Add a log into the database */
                Database::insert(
                    'payments',
                    [
                        'user_id' => $this->user->user_id,
                        'package_id' => $this->package_id,
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
                        'date' => Date::$date
                    ]
                );

                /* Send notification to admin if needed */
                if ($this->settings->email_notifications->new_payment && !empty($this->settings->email_notifications->emails)) {

                    send_mail(
                        $this->settings,
                        explode(',', $this->settings->email_notifications->emails),
                        sprintf($this->language->global->email_notifications->new_payment_subject, 'PAYPAL', $payment_total, $payment_currency),
                        sprintf($this->language->global->email_notifications->new_payment_body, $payment_total, $payment_currency)
                    );

                }


                /* Update the user with the new package */
                switch ($payment_plan) {
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
                        'package_id' => $this->package_id,
                        'package_settings' => json_encode($this->package->settings),
                        'package_expiration_date' => $package_expiration_date
                    ],
                    [
                        'user_id' => $this->user->user_id
                    ]
                );


                /* Success message and redirect */
                $_SESSION['success'][] = $this->language->pay->success_message->package_custom_start;
                redirect('dashboard');

            }

            /* Return confirmation processing recurring payment */
            if (isset($_GET['token'], $_GET['payment_type'])) {

                /* Initiate paypal */
                $paypal = new \PayPal\Rest\ApiContext(new \PayPal\Auth\OAuthTokenCredential($this->settings->paypal->client_id, $this->settings->paypal->secret));
                $paypal->setConfig(['mode' => $this->settings->paypal->mode]);

                $token = $_GET['token'];
                $agreement = new \PayPal\Api\Agreement();
                $payment_type = 'RECURRING';

                try {
                    $agreement->execute($token, $paypal);
                } catch (Exception $exception) {

                    /* Output errors properly */
                    if (DEBUG) {
                        echo $exception->getCode();
                        echo $exception->getData();

                        die();
                    } else {

                        $_SESSION['error'][] = $this->language->pay->error_message->failed_payment;
                        redirect('pay/' . $this->package_id);

                    }
                }

                /* Get details about the executed agreement */
                try {
                    $agreement = \PayPal\Api\Agreement::get($agreement->getId(), $paypal);
                } catch (Exception $exception) {

                    /* Output errors properly */
                    if (DEBUG) {
                        echo $exception->getCode();
                        echo $exception->getData();

                        die();
                    } else {

                        $_SESSION['error'][] = $this->language->pay->error_message->failed_payment;
                        redirect('pay/' . $this->package_id);

                    }
                }

                /* Get the needed details from the agreement */
                $agreement_status = $agreement->getState();

                /* Make sure the payment is approved */
                if ($agreement_status != 'Active') {
                    $_SESSION['error'][] = $this->language->pay->error_message->failed_payment;
                    redirect('pay/' . $this->package_id);
                }

                /* Success message and redirect */
                $_SESSION['success'][] = $this->language->pay->success_message->package_custom_will_start;
                redirect('dashboard');

            }

        }



        /* Return confirmation processing if failed */
        if($this->return_type && $this->return_type == 'paypal-cancel') {
            $_SESSION['error'][] = $this->language->pay->error_message->canceled_payment;
            redirect('pay/' . $this->package_id);
        }



    }

    private function stripe_create() {

        /* Initiate Stripe */
        \Stripe\Stripe::setApiKey($this->settings->stripe->secret_key);

        /* Payment details */
        $product = $this->package->name;
        $price = $this->package->{$_POST['payment_plan'] . '_price'};
        $stripe_formatted_price = number_format($price, 2) * 100;
        $shipping = 0;

        switch($_POST['payment_type']) {
            case 'one-time':

                $stripe_session = \Stripe\Checkout\Session::create([
                    'payment_method_types' => ['card'],
                    'line_items' => [[
                        'name' => $product,
                        'description' => $_POST['payment_plan'],
                        'amount' => $stripe_formatted_price,
                        'currency' => $this->settings->payment->currency,
                        'quantity' => 1,
                    ]],
                    'client_reference_id' => $this->user->user_id . '###' . $this->package_id . '###' . $_POST['payment_plan'] . '###' . time(),
                    'success_url' => url('pay/' . $this->package_id . '/stripe-success'),
                    'cancel_url' => url('pay/' . $this->package_id . '/stripe-cancel'),
                ]);

                break;

            case 'recurring':

                /* Try to get the product related to the package */
                try {
                    $stripe_product = \Stripe\Product::retrieve($this->package_id);
                } catch (\Exception $exception) {
                    /* The product probably does not exist */
                }

                if(!isset($stripe_product)) {
                    /* Create the product if not already created */
                    $stripe_product = \Stripe\Product::create([
                        'id'    => $this->package_id,
                        'name'  => $product,
                        'type'  => 'service',
                    ]);
                }

                /* Generate the plan id with the proper parameters */
                $stripe_plan_id = $this->package_id . '_' . $_POST['payment_plan'] . '_' . $stripe_formatted_price . '_' . $this->settings->payment->currency;

                /* Check if we already have a payment plan created and try to get it */
                try {
                    $stripe_plan = \Stripe\Plan::retrieve($stripe_plan_id);
                } catch (\Exception $exception) {
                    /* The plan probably does not exist */
                }

                /* Create the plan if it doesnt exist already */
                if(!isset($stripe_plan)) {
                    try {
                        $stripe_plan = \Stripe\Plan::create([
                            'amount' => $stripe_formatted_price,
                            'interval' => $_POST['payment_plan'] == 'monthly' ? 'month' : 'year',
                            'product' => $stripe_product->id,
                            'currency' => $this->settings->payment->currency,
                            'id' => $stripe_plan_id
                        ]);
                    } catch (\Exception $exception) {
                        $_SESSION['error'][] = $exception->getMessage();
                        redirect('pay/' . $this->package_id);
                    }
                }

                $stripe_session = \Stripe\Checkout\Session::create([
                    'payment_method_types' => ['card'],
                    'subscription_data' => [
                        'items' => [[
                            'plan' => $stripe_plan->id
                        ]]
                    ],
                    'client_reference_id' => $this->user->user_id . '###' . $this->package_id . '###' . $_POST['payment_plan'] . '###' . time(),
                    'success_url' => url('pay/' . $this->package_id . '/stripe-success'),
                    'cancel_url' => url('pay/' . $this->package_id . '/stripe-cancel'),
                ]);

                break;
        }

        return $stripe_session;

    }

    private function stripe_process() {

        /* Return confirmation processing if successfuly */
        if($this->return_type && $this->return_type == 'stripe-success') {

            /* Success message and redirect */
            $_SESSION['success'][] = $this->language->pay->success_message->package_custom_will_start;
            redirect('dashboard');
        }

        /* Return confirmation processing if failed */
        if($this->return_type && $this->return_type == 'stripe-cancel') {
            $_SESSION['error'][] = $this->language->pay->error_message->canceled_payment;
            redirect('pay/' . $this->package_id);
        }

    }
}
