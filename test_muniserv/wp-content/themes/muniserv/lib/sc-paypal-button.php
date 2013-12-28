<?php
// Return shortcode for list of consultant categories
function ms_paypal_button_shortcode($atts) {
    
    extract(shortcode_atts(array(
        'amount' => '0',
        'currency' => 'CAD',
        'description' => '',
        'tax' => '',
        'shipping' => '',
        'handling' => '',
        'qty' => '',
        'return_url' => '',
        'cancel_url' => '',
        'button_style' => '',
	), $atts));
    
    ob_start();
    ?>
    <form method="post" action="<?php echo get_template_directory_uri(); ?>/ms-paypal-button-handler.php">
        <input type="hidden" name="AMT" value="<?php echo $amount; ?>" />
        <input type="hidden" name="CURRENCYCODE" value="<?php echo $currency; ?>" />
        <?php if ($description) { ?>
          <input type="hidden" name="PAYMENTREQUEST_0_DESC" value="<?php echo $description; ?>" />
        <?php } ?>
        <?php if ($tax) { ?>
          <input type="hidden" name="TAXAMT" value="<?php echo $tax; ?>" />
        <?php } ?>
        <?php if ($shipping) { ?>
          <input type="hidden" name="SHIPPINGAMT" value="<?php echo $shipping; ?>" />
        <?php } ?>
        <?php if ($handling) { ?>
          <input type="hidden" name="HANDLINGAMT" value="<?php echo $handling; ?>" />
        <?php } ?>
        <?php if ($qty) { ?>
          <input type="hidden" name="PAYMENTREQUEST_0_QTY" value="<?php echo $qty; ?>" />
        <?php } ?>
        <?php if ($return_url) { ?>
          <input type="hidden" name="RETURN_URL" value="<?php echo $return_url; ?>" />
        <?php } ?>
        <?php if ($cancel_url) { ?>
          <input type="hidden" name="CANCEL_URL" value="<?php echo $cancel_url; ?>" />
        <?php } ?>
        <input type="hidden" name="func" value="start" />
        <?php if ($button_style) { ?>
          <?php if ($button_style == 'buy_now') { ?>
            <input type="image" value="" src="http://www.paypalobjects.com/en_US/i/btn/btn_buynow_LG.gif" />
          <?php } elseif ($button_style == 'checkout') { ?>
            <input type="image" value="" src="https://www.paypalobjects.com/en_US/i/btn/btn_xpressCheckout.gif" />
          <?php } ?>
        <?php } else { ?>
          <input type="submit" value="Pay with PayPal" />
        <?php } ?>
    </form>
    <?php
    return ob_get_clean();
}
add_shortcode('ms-paypal', 'ms_paypal_button_shortcode');