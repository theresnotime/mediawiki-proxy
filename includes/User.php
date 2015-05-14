<?php

namespace Wikimedia\TorProxy;

class User {

	private $db;

	private function __construct( $db ) {
		$this->db = $db;
	}

	public static function getUser( array $session, Database $db ) {
		return new User( $db );
	}

	public function exists() {
		return false;
	}

	public function authenticated() {
		return false;
	}
}
