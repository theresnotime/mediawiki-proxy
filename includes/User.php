<?php

namespace Wikimedia\TorProxy;

class User {

	private $db;

	private $edittoken;

	private function __construct( $db, $session ) {
		$this->db = $db;
		$this->edittoken = $session['token'];
	}

	public static function getUser( $session, Database $db ) {
		return new User( $db, $session );
	}

	public function exists() {
		return false;
	}

	public function authenticated() {
		return false;
	}

	public function getToken( $salt ) {
		
	}
}
