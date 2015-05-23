<?php

namespace Wikimedia\TorProxy;

class User {

	private $db;

	private $sessionCache = array();

	// @var int
	private $wikiId = null;

	private $username = null;

	private $oauthToken = null;

	private $authorized = null;

	const UNAUTHORIZED = 0;
	const WAITING = 1;
	const AUTHORIZED = 2;


	private function __construct( $db ) {
		$this->db = $db;
		$this->loadFromSession();
	}

	public static function getUser( Database $db ) {
		return new User( $db );
	}

	public function getOAuthToken() {
		return $this->oauthToken;
	}

	public function setWikiId( $id ) {
		$this->wikiId = $id;
		$this->storeInSession( 'wiki_id', $id );
	}

	public function getWikiId() {
		return $this->wikiId;
	}

	public function getUsername() {
		return $this->username;
	}

	private function loadFromSession() {
		$this->wikiId = $this->getFromSession( 'wiki_id' );
		$this->username = $this->getFromSession( 'username' );
		$this->oauthToken = $this->getFromSession( 'oauthtoken' );
	}

	private function load() {
		if ( is_null( $this->wikiId ) ) {
			$this->wikiId = $this->getFromSession( 'wiki_id' );
		}

		$stage = 'unauthorized';
		if ( $this->wikiId !== null && $this->wikiId !== '' ) {
			$stmt = $this->db->getDb()->prepare( 'select `authz_stage` from `users` where `wiki_id` = ?' );
			$stmt->bind_param('i', $this->wikiId);
			$stmt->execute();
			$stmt->bind_result( $dbstage );
			$stmt->fetch();
			$stmt->close();

			if ( $dbstage === 1 ) {
				$stage = 'waiting';
			} elseif ( $dbstage === 2 ) {
				$stage = 'authorized';
			}
		}
		$this->authorized = $stage;
	}

	/**
	 *
	 * @returns string one of 'unauthorized', 'waiting', 'authorized'
	 */
	public function authorized() {
		$this->load();
		return $this->authorized;
	}

	public function initAuthz() {
		$this->authorized = User::WAITING;
		$stmt = $this->db->getDb()->prepare( 'insert into `users` ( `wiki_id`, `authz_stage` ) values ( ?, ? )' );
		$stmt->bind_param( 'ii', $this->wikiId, $this->authorized );
		$stmt->execute();
		$stmt->close();
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
		$token = isset( $_SESSION['token'] ) ? $_SESSION['token'] : null;
		if ( is_null( $token ) ) {
			Settings::getInstance()->getLogger()->log( 'Generating token' );
			$token = 'A1234';
			$this->storeInSession( 'token', $token );
		}
		return sha1( $salt . $token );
	}

	public function validateToken( $token, $salt ) {
		return $this->getToken( $salt ) === $token;
	}

	public function getUserStats() {
		$queue = array( 1 => 0, 2 => 0 );
		$stage = false;
		$count = false;
		$stmt = $this->db->getDb()->prepare( 'SELECT COUNT(*) as count, `authz_stage` as stage FROM `users` GROUP BY `authz_stage`' );
		$stmt->execute();
		$stmt->bind_result( $count, $stage );
		while ( $stmt->fetch() ) {
			$queue[$stage] = $count;
		}
		$stmt->close();
		Settings::getInstance()->getLogger()->log( __METHOD__ . " queue is: " . print_r( $queue, true) );
		return $queue;
	}
}
