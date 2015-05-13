<?php
/**
 * @package woo-upsell-switcher
 * @version 1.0
 */
/*
Plugin Name: Woo Upsell Switcher
Plugin URI: http://github.com/nlk-plugins/woo-upsell-switcher
Description: Plugin to handle the idea of different Upsells product links based on different customer locations? [upsellswitch default_product_id="75" intl_product_id="77" ca_product_id="79"][upsell product_id="75" button_text="Buy This Other Product Now!" test="true"][/upsellswitch]
Version: 1.0
Author: Ninthlink, Inc.
Author URI: http://www.ninthlink.com
License: GPL2
*/

/**
 * Handle admin notices to complain if some other plugin inst found
 * ( copied from infusionsoft-one-click-upsell )
 */
function woo_upsell_switcher_admin_notices() {
	// Check if the Infusionsoft SDK plugin is active and configured
	if( !is_plugin_active( 'infusionsoft-sdk/infusionsoft-sdk.php' )){
		// Display an error message if the SDK plugin isn't active.
		echo "<div class=\"error\"><p><strong><em>Woo Upsell Switcher</em> requires the <em>Infusionsoft SDK</em> plugin. Please install and activate the <em>Infusionsoft SDK</em> plugin.</strong></p></div>";
	} elseif (!get_option('infusionsoft_sdk_app_name') || !get_option('infusionsoft_sdk_api_key')) {
		// Display an error message if the app name and API key aren't configured.
		echo "<div class=\"error\"><p><strong><em>Infusionsoft One-click Upsell</em> requires the <em>Infusionsoft SDK</em> plugin. Please set your Infusionsoft app name and API key on the <em>Infusionsoft SDK</em> <a href=\"" . admin_url( 'options-general.php?page=infusionsoft-sdk/infusionsoft-sdk.php' ) . "\">settings page.</a></strong></p></div>";
	} elseif( !is_plugin_active( 'infusionsoft-one-click-upsell/infusionsoft-one-click-upsell.php' )){
		// Display an error message if the SDK plugin isn't active.
		echo "<div class=\"error\"><p><strong><em>Woo Upsell Switcher</em> requires the <em>Infusionsoft One-click Upsell</em> plugin at this time. Please install and activate the <em>Infusionsoft One-click Upsell</em> plugin.</strong></p></div>";
	}
}
add_action( 'admin_notices', 'woo_upsell_switcher_admin_notices' );

/**
 * Callback for our shortcode wrapper
 *
 * [upsellswitch def="75" intl="77" ca="79"][upsell product_id="75" button_text="Buy This Other Product Now!" test="true"][/upsellswitch]
 */
function woo_upsell_switcher_shortcode( $atts, $content = "" ) {
	$ifoid = 0;
	if ( isset( $_GET['orderId'] ) ) {
		$ifoid = absint( $_GET['orderId'] );
	}
	
	extract( shortcode_atts( array(
		'def' => 0,
		'intl' => 0,
		'ca' => 0
	), $atts ) );
	
	// sanitize product_ids must be #s
	$def_pid = absint( $def );
	$ca_pid = absint( $ca );
	$intl_pid = absint( $intl );
	
	// by default, replace whatever upsell product_id="" with our default pid
	$replace_pid = $def_pid;
	
	//$oot = '<strong>SWITCH : '. $ifoid;
	//$oot .= ' : def '. $def_pid .' , ca '. $ca_pid .' ,  intl '. $intl_pid;
	//$oot .= '</strong> '. $content .' <strong>/SWITCH</strong>';
	
	if ( $ifoid != 0 ) {
      if ( class_exists( 'Infusionsoft_Job' ) ) {
        // haha! borrowed idea from infusionsoft-one-click-upsell plugin..
        $order = new Infusionsoft_Job($ifoid);
        if ( isset( $_GET['contactId'] ) ) {
          $contact_id = $_GET['contactId'];
          if($order->ContactId == $contact_id){
            //$oot .= '<div style="display:none" title="order"><pre>'. print_r($order,true) .'</pre></div>';
			//$oot .= '<strong>Country = '. $order->ShipCountry .' , State = '. $order->ShipState .'</strong>';
			
			if ( $order->ShipCountry != 'United States' ) {
				$replace_pid = $intl_pid;
			} elseif ( $order->ShipState == 'CA' ) {
				$replace_pid = $ca_pid;
			}
		  }
		}
	  }
	}
	//$oot .= '<strong>UPSELLSWITCH replace product_id '. $def_pid .' with '. $replace_pid .'</strong><br />';
	if ( ( $def_pid != 0 ) && ( $replace_pid != 0 ) ) {
		$content = str_replace( ' product_id="'. $def_pid .'"', ' product_id="'. $replace_pid .'"', $content );
	}
	//$oot .= $content;
	return do_shortcode( $content );
}
add_shortcode('upsellswitch', 'woo_upsell_switcher_shortcode');