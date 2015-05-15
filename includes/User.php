<?php

namespace Wikimedia\TorProxy;

class User {

	private $db;

	private $edittoken;

	private function __construct( $db ) {
		$this->db = $db;
	}

	public static function getUser( Database $db ) {
		return new User( $db );
	}

	public function exists() {
		return false;
	}

	public function authenticated() {
		return false;
	}

	/**
	 * TODO: replace this with MW one
	 */
	public function getToken( $salt ) {
		$token = $_SESSION['token'];
		if ( is_null( $token ) ) {
			$token = 'A1234';
			$_SESSION['token'] = $token;
		}
		return sha1( $salt . $token );
	}
}
