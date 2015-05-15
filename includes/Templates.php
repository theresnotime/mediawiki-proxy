<?php

namespace Wikimedia\TorProxy;

class Templates {

	public static function getTemplate( $name, $templateConfig ) {
		$name = strtolower( preg_replace( '![^\w]!', '', $name ) );
		return file_get_contents( $templateConfig['templateDir'] . $name . '.tmpl' );
	}

	public static function renderTemplate( $template, $data ) {
		$phpStr = \LightnCandy::compile( $template );
		$renderer = \LightnCandy::prepare( $phpStr );
		return $renderer( $data );
	}

}
