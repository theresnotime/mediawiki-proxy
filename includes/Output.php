<?php

namespace Wikimedia\TorProxy;

class Output {

	private $html;

	private $redirect;

	private $redirectUrl;

	public function addTemplate( $template, $data ) {
		$this->html .= render( $template, $data );
	}

	public function addHtml( $html ) {
		$this->html .= $html;

	}

	public function setRedirect( $url ) {
		$this->redirect = true;
		$this->redirectUrl = $url;
	}

	public function show() {
		if ( $this->redirect ) {
			self::outputRedirect( $this->redirectUrl );
		} else {
			self::outputHtml( $this->html );
		}
	}

	public static function outputRedirect( $url ) {
		self::outputHeaders();
		header( "Location: $url" );
	}


	public static function outputHtml( $html ) {
		self::outputHeaders();
		self::outputStart();
		echo $html;
		self::outputEnd();
	}

	public static function outputHeaders() {
		$csp = 'default-src \'self\'; object-src \'none\'; media-src \'none\'; img-src \'self\';'
			. 'style-src \'self\'; frame-ancestors \'none\'';

		header( 'Content-Type: text/html; charset=UTF-8' );
		header( 'X-XSS-Protection: 1; mode=block' );
		header( 'X-Content-Type-Options: nosniff' );
		header( 'X-Frame-Options: DENY' );
		header( "Content-Security-Policy: $csp" );
		header( "X-Content-Security-Policy: $csp" );
		header( "X-WebKit-CSP: $csp" );
	}


	public static function outputStart() {
		echo <<<EOD
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
<meta charset="UTF-8" />
<title>TorProxy</title>
</head>
<body>
EOD;

	}

	public static function outputEnd() {
		echo <<<EOD
</body>
</html>
EOD;
	}
}
