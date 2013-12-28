<?php
session_start();

// Calls to the PayPal API
if ( ! class_exists('ms_paypal_api') ) {
    
    class ms_paypal_api {
  
        // Button has been clicked, send settings to PayPal, get token, then redirect to PayPal
        function StartExpressCheckout() {
      
            $fields = array(
                'USER' => urlencode(MS_PAYPAL_USERNAME),
                'PWD' => urlencode(MS_PAYPAL_PASSWORD),
                'SIGNATURE' => urlencode(MS_PAYPAL_SIGNATURE),
                'VERSION' => urlencode('106.0'),
                'PAYMENTREQUEST_0_PAYMENTACTION' => urlencode('Sale'),
                'PAYMENTREQUEST_0_AMT0' => urlencode($_POST['AMT']),
                'PAYMENTREQUEST_0_AMT' => urlencode($_POST['AMT']),
                'PAYMENTREQUEST_0_ITEMAMT' => urlencode($_POST['AMT']),
                'ITEMAMT' => urlencode($_POST['AMT']),
                'PAYMENTREQUEST_0_CURRENCYCODE' => urlencode($_POST['CURRENCYCODE']),
                'RETURNURL' => urlencode(get_template_directory_uri().'/ms-paypal-button-handler.php?func=confirm'),
                'CANCELURL' => urlencode(get_template_directory_uri().'/ms-paypal-button-handler.php'),
                'METHOD' => urlencode('SetExpressCheckout')
            );

            if ( isset($_POST['PAYMENTREQUEST_0_DESC']) )
                $fields['PAYMENTREQUEST_0_DESC'] = urlencode($_POST['PAYMENTREQUEST_0_DESC']);

            if ( isset($_POST['RETURN_URL']) )
                $fields['RETURNURL'] = urlencode($_POST['RETURN_URL'].'?func=confirm');

            if ( isset($_POST['CANCEL_URL']) )
                $fields['CANCELURL'] = urlencode($_POST['CANCEL_URL']);

            if ( isset($_POST['PAYMENTREQUEST_0_QTY']) ) {
                $fields['PAYMENTREQUEST_0_QTY0'] = $_POST['PAYMENTREQUEST_0_QTY'];
                $fields['PAYMENTREQUEST_0_AMT'] = $fields['PAYMENTREQUEST_0_AMT'] * $_POST['PAYMENTREQUEST_0_QTY'];
                $fields['PAYMENTREQUEST_0_ITEMAMT'] = $fields['PAYMENTREQUEST_0_ITEMAMT'] * $_POST['PAYMENTREQUEST_0_QTY'];
                $fields['ITEMAMT'] = $fields['ITEMAMT'] * $_POST['PAYMENTREQUEST_0_QTY'];
            }

            if ( isset($_POST['TAXAMT']) ) {
                $fields['PAYMENTREQUEST_0_TAXAMT'] = $_POST['TAXAMT'];
                $fields['PAYMENTREQUEST_0_AMT'] += $_POST['TAXAMT'];
            }


            if ( isset($_POST['HANDLINGAMT']) ) {
                $fields['PAYMENTREQUEST_0_HANDLINGAMT'] = $_POST['HANDLINGAMT'];
                $fields['PAYMENTREQUEST_0_AMT'] += $_POST['HANDLINGAMT'];
            }

            if ( isset($_POST['SHIPPINGAMT']) ) {
                $fields['PAYMENTREQUEST_0_SHIPPINGAMT'] = $_POST['SHIPPINGAMT'];
                $fields['PAYMENTREQUEST_0_AMT'] += $_POST['SHIPPINGAMT'];
            }

            $fields_string = '';

            foreach ( $fields as $key => $value ) 
                $fields_string .= $key.'='.$value.'&';

            rtrim($fields_string,'&');

            // CURL
            $ch = curl_init();
            
            if (MS_PAYPAL_LIVE != 'true')
                curl_setopt($ch, CURLOPT_URL, 'https://api-3t.sandbox.paypal.com/nvp');
            else
                curl_setopt($ch, CURLOPT_URL, 'https://api-3t.paypal.com/nvp');

            curl_setopt($ch, CURLOPT_POST, count($fields));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            if (MS_CURL_NO_SSL == 'true')
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            
            //execute post
            $result = curl_exec($ch);

            //close connection
            curl_close($ch);

            parse_str($result, $result);

            if ($result['ACK'] == 'Success') {
                if (MS_PAYPAL_LIVE != 'true')
                    header('Location: https://www.sandbox.paypal.com/webscr?cmd=_express-checkout&useraction=commit&token='.$result['TOKEN']);
                else
                    header('Location: https://www.paypal.com/webscr?cmd=_express-checkout&useraction=commit&token='.$result['TOKEN']);
                exit;
            } else {
                print_r($result);
            }
        }

        // Confirm that the PayPal checkout portion has been completed
        function ConfirmExpressCheckout() {

            $fields = array(
                'USER' => urlencode(MS_PAYPAL_USERNAME),
                'PWD' => urlencode(MS_PAYPAL_PASSWORD),
                'SIGNATURE' => urlencode(MS_PAYPAL_SIGNATURE),
                'VERSION' => urlencode('106.0'),
                'TOKEN' => urlencode($_GET['token']),
                'METHOD' => urlencode('GetExpressCheckoutDetails')
            );

            $fields_string = '';
            foreach ( $fields as $key => $value )
                $fields_string .= $key.'='.$value.'&';
            rtrim($fields_string,'&');

            // CURL
            $ch = curl_init();

            if (MS_PAYPAL_LIVE != 'true')
                curl_setopt($ch, CURLOPT_URL, 'https://api-3t.sandbox.paypal.com/nvp');
            else
                curl_setopt($ch, CURLOPT_URL, 'https://api-3t.paypal.com/nvp');

            curl_setopt($ch, CURLOPT_POST, count($fields));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            if (MS_CURL_NO_SSL == 'true')
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            //execute post
            $result = curl_exec($ch);
            //close connection
            curl_close($ch);

            parse_str($result, $result);
            
            if ($result['ACK'] == 'Success') {
                $result['do_result'] = ms_paypal_api::DoExpressCheckout($result);
            }

            return $result;
        }

        // Complete the transaction
        function DoExpressCheckout($result) {

            $fields = array(
                'USER' => urlencode(MS_PAYPAL_USERNAME),
                'PWD' => urlencode(MS_PAYPAL_PASSWORD),
                'SIGNATURE' => urlencode(MS_PAYPAL_SIGNATURE),
                'VERSION' => urlencode('106.0'),
                'PAYMENTREQUEST_0_PAYMENTACTION' => urlencode('Sale'),
                'PAYERID' => urlencode($result['PAYERID']),
                'TOKEN' => urlencode($result['TOKEN']),
                'PAYMENTREQUEST_0_AMT' => urlencode($result['AMT']),
                'PAYMENTREQUEST_0_CURRENCYCODE' => urlencode($result['CURRENCYCODE']),
                'METHOD' => urlencode('DoExpressCheckoutPayment')
            );

            $fields_string = '';
            foreach ( $fields as $key => $value)
                $fields_string .= $key.'='.$value.'&';
            rtrim($fields_string,'&');

            // CURL
            $ch = curl_init();

            if (MS_PAYPAL_LIVE != 'true')
                curl_setopt($ch, CURLOPT_URL, 'https://api-3t.sandbox.paypal.com/nvp');
            else
                curl_setopt($ch, CURLOPT_URL, 'https://api-3t.paypal.com/nvp');

            curl_setopt($ch, CURLOPT_POST, count($fields));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            if (MS_CURL_NO_SSL == 'true')
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            //execute post
            $result = curl_exec($ch);
            //close connection
            curl_close($ch);

            parse_str($result, $result);

            return $result;
        }

        // Save payment into database
        function SavePayment($result, $consultant_id) {
            global $wpdb;

            $insert_data = array('consultant_id' => $consultant_id,
                                 'transaction_id' => $result['do_result']['PAYMENTINFO_0_TRANSACTIONID'],
                                 'token' => $result['TOKEN'],
                                 'amount' => $result['AMT'],
                                 'amount_item' => $result['ITEMAMT'],
                                 'amount_tax' => $result['TAXAMT'],
                                 'currency' => $result['CURRENCYCODE'],
                                 'firstname' => $result['FIRSTNAME'],
                                 'lastname' => $result['LASTNAME'],
                                 'email' => $result['EMAIL'],
                                 'description' => $result['PAYMENTREQUEST_0_DESC'],
                                 'summary' => serialize($result),
                                 'created' => time());

            $insert_format = array('%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d');

            $wpdb->insert('ms_payment_history', $insert_data, $insert_format);
        }
        
        // Save form data in case they cancel
        function SaveFormData($formdata) {
            global $wpdb;

            $insert_data = array('formdata' => serialize($formdata),
                                 'created' => time());

            $insert_format = array('%s', '%d');

            $wpdb->insert('ms_cancelled_signups', $insert_data, $insert_format);
            
            return $wpdb->insert_id;
        }
        
        // Delete form data entry once payment has gone through
        function RemoveFormData($id) {
            global $wpdb;

            $delete_data = array('id' => $id);

            $delete_format = array('%d');

            $wpdb->delete('ms_cancelled_signups', $delete_data, $delete_format);
        }
    }
}