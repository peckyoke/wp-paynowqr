<?php

/**
 * Adapted from php-qrcode example QRImageWithLogo.php
 * @see 
 */

/**
 * Class QRImageWithLogo
 *
 * @filesource   QRImageWithLogo.php
 * @created      18.11.2020
 * @package      chillerlan\QRCodeExamples
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2020 smiley
 * @license      MIT
 *
 * @noinspection PhpComposerExtensionStubsInspection
 */

namespace PaynowQR;

use chillerlan\QRCode\{QRCode, QROptions};
use chillerlan\QRCode\Output\{QRCodeOutputException, QRImage};

require_once __DIR__.'/vendor/autoload.php';

// use function imagecopyresampled, imagecreatefrompng, imagesx, imagesy, is_file, is_readable;

/**
 * @property \chillerlan\QRCodeExamples\LogoOptions $options
 */
class QRImageWithLogo extends QRImage{

	/**
	 * @param string|null $file
	 * @param string|null $logo
	 *
	 * @return string
	 * @throws \chillerlan\QRCode\Output\QRCodeOutputException
	 */
	public function dump(string $file = null, string $logo = null):string{
		// set returnResource to true to skip further processing for now
		$this->options->returnResource = true;

		// of course you could accept other formats too (such as resource or Imagick)
		// i'm not checking for the file type either for simplicity reasons (assuming PNG)
		if(!is_file($logo) || !is_readable($logo)){
			throw new QRCodeOutputException('invalid logo');
		}

		$this->matrix->setLogoSpace(
			$this->options->logoSpaceWidth,
			$this->options->logoSpaceHeight
			// not utilizing the position here
		);

		// there's no need to save the result of dump() into $this->image here
		parent::dump($file);

		$im = imagecreatefrompng($logo);

		// get logo image size
		$w = imagesx($im);
		$h = imagesy($im);

		// set new logo size, leave a border of 1 module (no proportional resize/centering)
		$lw = ($this->options->logoSpaceWidth - 2) * $this->options->scale;
		$lh = ($this->options->logoSpaceHeight - 2) * $this->options->scale;

		// get the qrcode size
		$ql = $this->matrix->size() * $this->options->scale;

		// scale the logo and copy it over. done!
		imagecopyresampled($this->image, $im, ($ql - $lw) / 2, ($ql - $lh) / 2, 0, 0, $lw, $lh, $w, $h);

		$imageData = $this->dumpImage();

		if($file !== null){
			$this->saveToFile($imageData, $file);
		}

		if($this->options->imageBase64){
			$imageData = 'data:image/'.$this->options->outputType.';base64,'.base64_encode($imageData);
		}

		return $imageData;
	}

}


class LogoOptions extends QROptions{
	// size in QR modules, multiply with QROptions::$scale for pixel size
	protected int $logoSpaceWidth;
	protected int $logoSpaceHeight;
}


/*
 * Runtime
 */

function qrcode ($qrstr, $logo=null) {

	if (isset($logo)) {
		$options = new LogoOptions;

		// $options->version          = 7;
		$options->eccLevel         = QRCode::ECC_H;
		$options->imageBase64      = true;
		$options->logoSpaceWidth   = 24;
		$options->logoSpaceHeight  = 15;
		// $options->logoSpaceWidth   = 13;
		// $options->logoSpaceHeight  = 13;
		// $options->logoSpaceWidth   = 20;
		// $options->logoSpaceHeight  = 20;
		$options->scale            = 3;
		$options->imageTransparent = false;
		$options->outputType = QRCode::OUTPUT_IMAGE_PNG;
		// $options->pngCompression = 9;

		// header('Content-type: image/png');

		$qrOutputInterface = new QRImageWithLogo($options, (new QRCode($options))->getMatrix($qrstr));

		// dump the output, with an additional logo
		// return $qrOutputInterface->dump(null, __DIR__.'/api-logo.png');		
		return $qrOutputInterface->dump(null, $logo);		
	} else {
		return (new QRCode)->render($qrstr);

	}
}

// $image = qrcode("00020101021226520009SG.PAYNOW010120213201512004RACC03010040820230323520400005303702540450.55802SG5924Asian Pastoral Institute6009Singapore62170113GIT-INV-100016304978C");
// // qrcode("Hello");
// print $image . "\n\n";
?>