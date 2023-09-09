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
use chillerlan\QRCode\Common\EccLevel;
use chillerlan\QRCode\Data\QRMatrix;
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

		// of course, you could accept other formats too (such as resource or Imagick)
		// I'm not checking for the file type either for simplicity reasons (assuming PNG)
		if(!is_file($logo) || !is_readable($logo)){
			throw new QRCodeOutputException('invalid logo');
		}

		// there's no need to save the result of dump() into $this->image here
		parent::dump($file);

		$im = imagecreatefrompng($logo);

		// get logo image size
		$w = imagesx($im);
		$h = imagesy($im);

		// set new logo size, leave a border of 1 module (no proportional resize/centering)
		$lw = (($this->options->logoSpaceWidth - 2) * $this->options->scale);
		$lh = (($this->options->logoSpaceHeight - 2) * $this->options->scale);

		// get the qrcode size
		$ql = ($this->matrix->getSize() * $this->options->scale);

		// scale the logo and copy it over. done!
		imagecopyresampled($this->image, $im, (($ql - $lw) / 2), (($ql - $lh) / 2), 0, 0, $lw, $lh, $w, $h);

		$imageData = $this->dumpImage();

		$this->saveToFile($imageData, $file);

		if($this->options->outputBase64){
			$imageData = $this->toBase64DataURI($imageData, 'image/'.$this->options->outputType);
		}

		return $imageData;
	}

}


class LogoOptions extends QROptions{
	// size in QR modules, multiply with QROptions::$scale for pixel size
	// protected int $logoSpaceWidth;
	// protected int $logoSpaceHeight;
}


/*
 * Runtime
 */

function qrcode ($qrstr, $logo=null) {

	if (isset($logo)) {
		$options = new QROptions;

		$color = [144, 19, 123];
		$white = [255, 255, 255];
		$black = [0, 0, 0];

		// $options->version          = 7;
		$options->eccLevel         = EccLevel::H;
		$options->imageBase64      = true;
		$options->logoSpaceWidth   = 24;
		$options->logoSpaceHeight  = 15;
		// $options->logoSpaceWidth   = 13;
		// $options->logoSpaceHeight  = 13;
		// $options->logoSpaceWidth   = 20;
		// $options->logoSpaceHeight  = 20;
		$options->scale            = 3;
		$options->imageTransparent = false;
		// $options->drawLightModules    = true;
		// $options->outputType = QRCode::OUTPUT_IMAGE_PNG;
		// $options->pngCompression = 9;
		$options->bgColor = [255,0,255];
		$options->moduleValues        = [
			// finder
			QRMatrix::M_FINDER_DARK    => $color, // dark (true)
			QRMatrix::M_FINDER_DOT     => $color, // finder dot, dark (true)
			QRMatrix::M_FINDER         => $white, // light (false), white is the transparency color and is enabled by default
			// // alignment
			QRMatrix::M_ALIGNMENT_DARK => $color,
			QRMatrix::M_ALIGNMENT      => $white,
			// // timing
			QRMatrix::M_TIMING_DARK    => $color,
			QRMatrix::M_TIMING         => $white,
			// // format
			QRMatrix::M_FORMAT_DARK    => $color,
			QRMatrix::M_FORMAT         => $white,
			// // version
			QRMatrix::M_VERSION_DARK   => $color,
			QRMatrix::M_VERSION        => $white,
			// data
			QRMatrix::M_DATA_DARK      => $color,
			QRMatrix::M_DATA           => $white,
			// // darkmodule
			QRMatrix::M_DARKMODULE     => [0, 0, 0],
			// // separator
			QRMatrix::M_SEPARATOR      => $white,
			// // quietzone
			QRMatrix::M_QUIETZONE      => $white,
			// // logo (requires a call to QRMatrix::setLogoSpace()), see QRImageWithLogo
			QRMatrix::M_LOGO           => $white,
		];
		

		// header('Content-type: image/png');

		$qrcode = new QRCode($options);
		$len = strlen($qrstr);
		for ($pos=0; $pos<$len; $pos+=100) {
			$qrcode->addByteSegment(substr($qrstr, $pos, 100));
		}
		// $qrcode->addByteSegment('https://github.com');
		$qrOutputInterface = new QRImageWithLogo($options, $qrcode->getQRMatrix());

		// $qrOutputInterface = new QRImageWithLogo($options, (new QRCode($options))->getMatrix($qrstr));

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