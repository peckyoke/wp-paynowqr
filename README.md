# Wordpress shortcodes for Paynow QR

This plugin provides shortcodes for generating Paynow QR code.

# Example Use

```
[paynow_img][paynow_qrcode uen='my_uen' amount=100 ref='MyReference' ][/paynow_img]
```

# References

The paynow QR code generated are adapted from [PaynowQR](https://github.com/ThunderQuoteTeam/PaynowQR).

The CRC script is taken from https://beccati.com/crc16.php.

This plugin also uses [php-qrcode](https://github.com/chillerlan/php-qrcode) to generate the QR code image.