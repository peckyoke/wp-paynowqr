<?php

namespace PaynowQR;

include 'crc16.php';

/**
 * Following script adapted from PaynowQR javascript.
 * @see https://github.com/ThunderQuoteTeam/PaynowQR
 */

class PayNowField {
  public $id;
  public $value;

  function __construct($id, $value) {
    $this->id = $id;
    $this->value = $value;
  }
}

class PaynowQR{
  public $qrstring;
  public $opts;

  function __construct($opts) {
    $this->opts = $opts;
  }
  
  function generate( ) {
    $opts = $this->opts;
    // print_r($opts);
    if (! property_exists($opts, 'uen')) {
      throw new Exception("uen property required in opts");
    }

    foreach($opts as $key => $value){
      if(gettype($value) == 'string') {
        $opts->{$key} = trim($value);
      }
    }

    $date = date_create();
    date_add($date, date_interval_create_from_date_string("3 days"));


    $p = array(
      new PayNowField('00', '01'),                            // ID 00: Payload Format Indicator (Fixed to '01')
      new PayNowField('01', '12'),                            // ID 01: Point of Initiation Method 11: static, 12: dynamic
      new PayNowField('26', array(                            // ID 26: Merchant Account Info Template
        new PayNowField('00','SG.PAYNOW'),
        new PayNowField('01', '2'),                           // 0 for mobile, 2 for UEN. 1 is not used.
        new PayNowField('02', strval($opts->uen)),            // PayNow UEN (Company Unique Entity Number)
        new PayNowField('03', strval((!isset($opts->amount) || $opts->editable)? 1 : 0)), // 1 = Payment amount is editable, 0 = Not Editable
        new PayNowField('04', strval($opts->expiry ?? date_format($date,'Ymd')))    // Expiry date (YYYYMMDD)
      )),
      new PayNowField('52', '0000' ),                         // ID 52: Merchant Category Code (not used)
      new PayNowField('53', '702' ),                          // ID 53: Currency. SGD is 702
      new PayNowField('54', strval($opts->amount ?? 0) ),     // ID 54: Transaction Amount
      new PayNowField('58', 'SG' ),                           // ID 58: 2-letter Country Code (SG)
      new PayNowField('59', strval($opts->company ?? 'NA') ), // ID 59: Company Name
      new PayNowField('60', 'Singapore' )                     // ID 60: Merchant City 
    );
 

    if(isset($opts->refNumber)) {
      $p[] = new PayNowField('62', value: array(                     // ID 62: Additional data fields
        new PayNowField('01', strval($opts->refNumber ?? ''))   // ID 01: Bill Number
      ));
    }

    // print_r($p);

    $qrstr = "";
    foreach ($p as $curr) {
      if (gettype($curr->value) == 'array') {
        $str2 = "";
        foreach ($curr->value as $curr2) {
          $len = strlen($curr2->value);
          $str2 .= $curr2->id . str_pad(strval($len),2,"0",STR_PAD_LEFT) . $curr2->value;          
        }
        $len = strlen($str2);
        $qrstr .= $curr->id . str_pad(strval($len),2,"0",STR_PAD_LEFT) . $str2;
      } else {
        // print_r($curr->value . "\n");
        $len = strlen($curr->value);
        $qrstr .= $curr->id . str_pad(strval($len),2,"0",STR_PAD_LEFT) . $curr->value;
      }
    }

    $crc = CRC16HexDigest($qrstr . '6304');

    // Here we add "6304" to the previous string
    // ID 63 (Checksum) 04 (4 characters)
    // Do a CRC16 of the whole string including the "6304"
    // then append it to the end.
    $qrstr .= '6304' . str_pad($crc, 4, '0', STR_PAD_LEFT);
  
    return $qrstr;
  
  }

}

?>