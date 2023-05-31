<?php
namespace Wikimedia\TorProxy;

/**
 * Access to global configs
 */
class Settings
{

    private $db = null;
    private $logger = null;
    private $oauthConfig = null;
    private $wikiConfig = null;
    private $proxyConfig = null;
    private $connection = null;

    private static $instance = null;

    public static function getInstance()
    {
        if (self::$instance === null ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function setVars( $db, $logger, $oauthConfig, $wikiConfig, $proxyConfig, $connection )
    {
        $this->db = $db;
        $this->logger = $logger;
        $this->oauthConfig = $oauthConfig;
        $this->wikiConfig = $wikiConfig;
        $this->proxyConfig = $proxyConfig;
        $this->connection = $connection;
    }

    public function getLogger()
    {
        return $this->logger;
    }

    public function getDB()
    {
        return $this->db;
    }

    public function getOAuthConfig()
    {
        return $this->oauthConfig;
    }

    public function getWikiConfig()
    {
        return $this->wikiConfig;
    }

    public function getProxyConfig()
    {
        return $this->proxyConfig;
    }

    public function getConnection()
    {
        return $this->connection;
    }

}
