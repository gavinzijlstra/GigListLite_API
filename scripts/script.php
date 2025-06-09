<?php
require_once('../classes/class.logger.php');
require_once('../classes/class.tokens.php');
session_start();

// Auth-check (simpele token)
$clsTokens = new Tokens();
$tokens = $clsTokens->getTokens();

// Get Logger
$logger = new Logger(__DIR__,'backuppert');
$logger->addLog("Start backup.");
echo "Start backup", PHP_EOL;

// Get Generic Variables
$doelMap = '/var/www/clients/client43128/web95203/giglistlite/backups/';
$datum = date('Ymd');

// Create Backup-map if not exists
if (!is_dir($doelMap)) {
    mkdir($doelMap, 0777, true);
}

// Backup the .dat file for each known token.
foreach ($tokens as $token) {
    $bron = "/var/www/clients/client43128/web95203/giglistlite/data/giglist_${token}.dat";
    $doelBestand = $doelMap . 'backup_' . $datum . '_' . $token . '.dat';
    $logger->addLog("Copy [${bron}] to [${doelBestand}]");
    echo $doelBestand,PHP_EOL;
    copy($bron, $doelBestand);
}

// Feedback
$logger->addLog("Finish backup.");
echo "Finish backup", PHP_EOL;

?>