<?php
/**
 * Plugin Name: PaynowQR
 * Description: Singapore Paynow QR code
 * Version: 1.0.0
 * Author: Leong Peck Yoke
 * Author URI: https://github.com/peckyoke
 * Text Domain: wp-paynowqr
 * License: GPLv2 or later
 *
 * @package PaynowQR
 */
if ( ! defined( 'WPINC' ) ) {
	die( '-1' );
}

define( 'PAYNOWQR_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'PAYNOWQR_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require_once PAYNOWQR_PLUGIN_DIR . '/src/paynowqr.php';
require_once PAYNOWQR_PLUGIN_DIR . '/src/qrcode.php';

function paynowqr_qrcode_shortcode($atts) {
    $default = array(
        'uen' => '',                // Required: UEN of company
        'amount' => 0,              // Specify amount of money to pay.
        'editable' => null,      // Whether or not to allow editing of payment amount. Defaults to false if amount is specified
        'expiry' => null,           // Set an expiry date for the Paynow QR code (YYYYMMDD). If ommitted, defaults to 3 days from now.
        'ref'=> '',           // Reference number for Paynow Transaction. Useful if you need to track payments for recouncilation.
    );
    $opts = (object)shortcode_atts($default, $atts);
    $opts->refNumber = $opts->ref;
    $qr = new PaynowQR\PaynowQR($opts);
    $qrstr = $qr->generate();
    // return $qrstr;
    $imageString = PaynowQR\qrcode($qrstr, PAYNOWQR_PLUGIN_DIR . '/src/paynow-logo-bw.png');
    // // return '<img src="' . $imageString . '">';
    // // return $qrstr;
    return $imageString;
}

function paynowqr_img_shortcode($atts, $content = null) {
    $content = do_shortcode($content);
    // $imageString = PaynowQR\qrcode($content);
    // return '<img src="' . $imageString . '">';
    return '<img src="' . $content . '">';
}

add_shortcode('paynow_qrcode', 'paynowqr_qrcode_shortcode'); 
add_shortcode('paynow_img', 'paynowqr_img_shortcode'); 

?>