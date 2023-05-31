<?php
$TorProxyTemplateConfig = array(
    'templateDir' => __DIR__ . '/templates/',
);

$TorProxyLogConfig = array(
    'filename' => __DIR__ . 'out.log',
);

$TorProxyDBConfig = array(
    'host' => '',
    'user' => '',
    'pass' => '',
    'db' => '',
);
$TorProxyOAuthConfig = array(
    'key' => '',
    'secret' => '',
);
$TorProxyWikiConfig = array(
    'canonical_url' => 'https://en.wikipedia.org/',
    'base_url' => 'https://en.wikipedia.org/w/',
    'base_url_clean' => 'https://en.wikipedia.org/wiki/',
    'notification_page' => 'User:TorProxy/AccessRequests',
);
$TorProxyConfig = array(
    'base_url' => 'http://localhost:8080/',
);