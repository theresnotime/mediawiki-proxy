<?php

namespace Wikimedia\TorProxy;

class Database {

	private $db;

	private $logger;

	public function __construct( $config, Logger $logger ) {
		$this->logger = $logger;
		$this->db = new \mysqli(
			$config['host'],
			$config['user'],
			$config['pass'],
			$config['db']
		);
		if ($this->db->connect_errno) {
			$this->logger->log( "Failed to connect to MySQL: (" .
				$this->db->connect_errno . ") " . $this->db->connect_error );
			throw new \Exception( "Failed to load DB" );
		}
	}

}
