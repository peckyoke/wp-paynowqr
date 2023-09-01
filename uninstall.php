<?php
remove_shortcode( 'paynow_img' );
remove_shortcode( 'paynow_qrcode' );
wp_dequeue_script( 'paynow-script' );
wp_dequeue_script( 'qrcode-script' );
?>