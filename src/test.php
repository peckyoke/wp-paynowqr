<?php

include 'paynowqr.php';
include 'qrcode.php';

$opts = (object)array(
	'uen' => '201512004RACC', //Required: UEN of company
    'amount' => 50.50,             //Specify amount of money to pay.
    'editable' => false,           //Whether or not to allow editing of payment amount. Defaults to false if amount is specified
    // 'expiry' => '20201231',       //Set an expiry date for the Paynow QR code (YYYYMMDD). If ommitted, defaults to 5 years from now.
    'refNumber' => 'GIT-INV-10001',  //Reference number for Paynow Transaction. Useful if you need to track payments for recouncilation.
    'company' =>  'Asian Pastoral Institute' //Company name to embed in the QR code. Optional.               
);

$qr = new PaynowQR\PaynowQR($opts);
$qrstr = $qr->generate();
print $qrstr . "\n\n";

$imageString = PaynowQR\qrcode($qrstr,  __DIR__.'/api-logo.png');
print $imageString . "\n\n";


?>
