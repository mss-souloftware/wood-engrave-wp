<?php

/**
 * Author: M. Sufyan Shaikh
 * Description: Process form and send info to cookie
 * @package Crea Tu Frase
 * @subpackage M. Sufyan Shaikh
 */

function responseForm()
{
    try {
        $response = array('Datos' => confirmAllIsReady());
        $amount = isset($_POST['priceTotal']) ? sanitize_text_field($_POST['priceTotal']) : '';
        $paymentMethod = isset($_POST['paymentType']) ? sanitize_text_field($_POST['paymentType']) : '';

        if (!is_numeric($amount) || floatval($amount) <= 0) {
            wp_send_json(['success' => false, 'message' => 'Invalid amount']);
            return;
        }

        if ($paymentMethod === 'paypal') {
            wp_send_json([
                'success' => true,
                'Datos' => [
                    'inserted_id' => $response['Datos']['inserted_id'],
                    'amount' => $amount,
                    'fname' => sanitize_text_field($_POST['fname']),
                    'message' => 'PayPal payment processing initiated'
                ]
            ]);
            return;
        }

        $paymentObj = new RedsysAPI;
        $formattedAmount = round(floatval($amount) * 100);
        $orderNumberRandom = bin2hex(random_bytes(5));

        $plugin_page = get_option('ctf_settings')['plugin_page'];
        $plugin_payment = get_option('ctf_settings')['plugin_payment'];
        $thank_you_page = get_option('ctf_settings')['thank_you_page'];

        $paymentObj->setParameter("DS_MERCHANT_AMOUNT", $formattedAmount);
        $paymentObj->setParameter("DS_MERCHANT_ORDER", $orderNumberRandom);
        $paymentObj->setParameter("DS_MERCHANT_MERCHANTCODE", "340873405");
        $paymentObj->setParameter("DS_MERCHANT_CURRENCY", "978");
        $paymentObj->setParameter("DS_MERCHANT_TERMINAL", "001");
        $paymentObj->setParameter("DS_MERCHANT_MERCHANTDATA", $response['Datos']['inserted_id']);
        $paymentObj->setParameter("DS_MERCHANT_MERCHANTURL", $plugin_page);
        $paymentObj->setParameter("DS_MERCHANT_URLOK", "$plugin_payment?payment=true");
        $paymentObj->setParameter("DS_MERCHANT_URLKO", $thank_you_page);

        $paymentObj->setParameter("DS_MERCHANT_TRANSACTIONTYPE", $paymentMethod === 'bizum' || $paymentMethod === 'google' ? "7" : "0");

        if ($paymentMethod !== 'redsys') {
            $paymentTypeMapping = [
                'bizum' => ['payMethods' => 'z'],
                'google' => ['payMethods' => 'xpay']
            ];

            if (!array_key_exists($paymentMethod, $paymentTypeMapping)) {
                wp_send_json(['success' => false, 'message' => 'Invalid payment method']);
                return;
            }

            $paymentObj->setParameter("DS_MERCHANT_PAYMETHODS", $paymentTypeMapping[$paymentMethod]['payMethods']);
        }

        $paymentParams = $paymentObj->createMerchantParameters();
        $signature = $paymentObj->createMerchantSignature('sq7HjrUOBfKmC576ILgskD5srU870gJ7');

        wp_send_json([
            'success' => true,
            'Datos' => [
                'merchantParameters' => $paymentParams,
                'signature' => $signature,
                'inserted_id' => $response['Datos']['inserted_id'],
                'amount' => $amount,
                'paymentType' => $paymentMethod,
                'fname' => sanitize_text_field($_POST['fname']),
                'testing' => 'testingParam'
            ]
        ]);

        return;

    } catch (Exception $e) {
        wp_send_json(array('error' => $e->getMessage()));
        exit;
    }
}

function confirmAllIsReady()
{
    $getData = array('priceTotal', 'fname', 'email', 'tel', 'postal', 'city', 'address', 'province', 'message', 'picDate', 'shippingType', 'nonce', 'uoi', 'coupon', 'screens', 'featured', 'affiliateID', 'loggedInUser', 'paymentType');

    $confirm_error = array();

    foreach ($getData as $key) {
        if (isset($_POST[$key])) {
            $confirm_error[$key] = $_POST[$key];
        } else {
            throw new Exception("Missing required field: $key");
        }
    }

    return saveDataInDatabase($confirm_error);
}

function confirmViolationOfSequirity($incomingfrase)
{
    $confirmSequirity = preg_match('/[$^\*\(\)=\{\]\{\{\<\>\:\;]/', $incomingfrase);
    if ($confirmSequirity > 0) {
        throw new Exception('Invalid characters in frase');
    } else {
        return $incomingfrase;
    }
}

function saveDataInDatabase($datos)
{
    $sanitizeData = array();

    foreach ($datos as $info => $val) {
        $sanitizeData[$info] = sanitize_text_field($datos[$info]);
    }

    global $wpdb;
    $tablename = $wpdb->prefix . 'chocoletras_plugin';

    $query = $wpdb->prepare(
        "INSERT INTO $tablename (precio, nombre, email, telefono, cp, ciudad, province, message, direccion, nonce, fechaEntrega, express, uoi, coupon, screens, featured, affiliate_id, loggedInUser, selectedMethod) 
        VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
        $sanitizeData['priceTotal'],
        $sanitizeData['fname'],
        $sanitizeData['email'],
        $sanitizeData['tel'],
        $sanitizeData['postal'],
        $sanitizeData['city'],
        $sanitizeData['province'],
        $sanitizeData['message'],
        $sanitizeData['address'],
        $sanitizeData['nonce'],
        $sanitizeData['picDate'],
        $sanitizeData['shippingType'],
        $sanitizeData['uoi'],
        $sanitizeData['coupon'],
        $sanitizeData['screens'],
        $sanitizeData['featured'],
        $sanitizeData['affiliateID'],
        $sanitizeData['loggedInUser'],
        $sanitizeData['paymentType']
    );

    try {
        $result = $wpdb->query($query);

        if ($result === false) {
            throw new Exception("Database error: " . $wpdb->last_error);
        }

        $inserted_id = $wpdb->insert_id;

        if (empty($inserted_id)) {
            throw new Exception("Failed to retrieve inserted ID.");
        }

        // Process affiliate commissions if affiliateID is provided
        if (!empty($sanitizeData['affiliateID'])) {
            $affiliate_table = $wpdb->prefix . 'yith_wcaf_affiliates';
            $affiliate_id = $wpdb->get_var($wpdb->prepare(
                "SELECT ID FROM $affiliate_table WHERE user_id = %d",
                $sanitizeData['affiliateID']
            ));

            if (empty($affiliate_id)) {
                throw new Exception("Affiliate ID not found.");
            }

            $commission_table = $wpdb->prefix . 'yith_wcaf_commissions';
            $last_line_item_id = $wpdb->get_var("SELECT MAX(line_item_id) FROM $commission_table");

            $line_item_id = $last_line_item_id ? $last_line_item_id + 1 : 1;
            $rate = get_option('yith_wcaf_general_rate', 0);
            $amount = $sanitizeData['priceTotal'] * ($rate / 100);
            $current_date = current_time('mysql');

            $commission_query = $wpdb->prepare(
                "INSERT INTO $commission_table (order_id, line_item_id, product_id, product_name, affiliate_id, rate, line_total, amount, refunds, status, created_at, last_edit) 
                VALUES (%d, %d, %d, %s, %d, %f, %f, %f, %d, %s, %s, %s)",
                $inserted_id,
                $line_item_id,
                $inserted_id,
                $sanitizeData['mainText'],
                $affiliate_id,
                $rate,
                $sanitizeData['priceTotal'],
                $amount,
                0,
                'pending',
                $current_date,
                $current_date
            );

            $commission_result = $wpdb->query($commission_query);

            if ($commission_result === false) {
                throw new Exception("Failed to insert commission: " . $wpdb->last_error);
            }
        }

        return array(
            'inserted_id' => $inserted_id,
            'amount' => $sanitizeData['priceTotal']
        );

    } catch (Exception $e) {
        return array(
            'error' => $e->getMessage()
        );
    }
}