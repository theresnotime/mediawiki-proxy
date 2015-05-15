<?php

namespace Wikimedia\TorProxy;

class User {

	private $db;

	private $sessionCache = array();

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
		return ( $this->getFromSession( 'username' ) !== '' );
	}

	public function storeInSession( $key, $value ) {
		$_SESSION[$key] = $value;
		$sessionCache[$key] = $value;
	}

	public function getFromSession( $key ) {
		if ( isset( $sessionCache[$key] ) ) {
			return $sessionCache[$key];
		} elseif ( !isset( $_SESSION[$key] ) ) {
			return '';
		}
		return $_SESSION[$key];
	}

	public function deleteFromSession( $key ) {
		if ( isset( $sessionCache[$key] ) ) {
			unset( $sessionCache[$key] );
		}
		unset( $_SESSION[$key] );
	}

	/**
	 * TODO: replace this with MW one
	 */
	public function getToken( $salt ) {
		$token = $_SESSION['token'];
		if ( is_null( $token ) ) {
			$token = 'A1234';
			$this->storeInSession( 'token', $token );
		}
		return sha1( $salt . $token );
	}

	public function validateToken( $token, $salt ) {
		return $this->getToken( $salt ) === $token;
	}
}
