<?php
require_once('classes/class.file.php');
require_once('classes/class.tokens.php');
require_once('classes/class.logger.php');
require_once('classes/class.giglist.php');
session_start();

// Create Logger
$logger = new Logger(__DIR__,'giglistlite');

// Auth-check (simpele token)
$tokens = new Tokens();
$expectedKeys = $tokens->getTokens();
$apiKey = $_SERVER['HTTP_X_API_KEY'] ?? null;
//echo 'API key (' . $apiKey . ')';
if (!in_array($apiKey, $expectedKeys)) {
    http_response_code(403);
    echo 'Ongeldige API key (' . $apiKey . ')';
    exit;
}

// Get Data file
$path = __DIR__ . '/data/giglist_' . $apiKey . '.dat'; // Zorg dat deze map buiten public_html zit als dat kan     
//$logger->addLog('Path (' . $path . ')');
if (!file_exists($path)) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized bro!']);
    exit;
}

// Open file and get the Gigs
$file = new File($path); 
$gigs = new GigList($file); 

// GET = lees JSON
if ($_SERVER['REQUEST_METHOD'] === 'GET') {    
    $logger->addLog("[$apiKey] GET - [ALL]");
    // Zet header voor JSON-response
    header('Content-Type: application/json');
    echo $gigs->getJson();
}

// POST = schrijf JSON
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = file_get_contents('php://input');
    $logger->addLog("[$apiKey] POST - Gigs: [$input]");
    //echo $input;
    if (!json_decode($input)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid JSON']);
    }
    $key = $gigs->getUniqueKey();
    $line = $gigs->jsonNodeToLine($key, json_decode($input, true));
    // Voeg een newline toe vóór de nieuwe regel
    $line = str_replace("\n", "\\n", $line); // escape enters
    $lineNewLine = PHP_EOL . $line;
    file_put_contents($path, $lineNewLine, FILE_APPEND);
    echo json_encode(['_id' => $key]);
    exit;
}

// DELETE = verwijder regel
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    
    if (!isset($_GET['_id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'id is a mandatory field.']);
    } else {        
        // Verwerk het verwijderen met deze $id
        $id = $_GET['_id'];
        $logger->addLog("[$apiKey] DELETE - ID : [$id]");
        $json = $gigs->verwijderNodeOpId($id);
        $gigs->save($json);
        echo json_encode(["success" => "The line with id [$id] has been removed."]);
        exit;
    }
}

// PUT = update line
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $input = file_get_contents('php://input');
    $logger->addLog("[$apiKey] PUT - Gigs: [$input]");
    //echo $input;
    if (!json_decode($input)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid JSON']);
    }
    $json = json_decode($input, true);
    $gigs->update($json);
    $id = $json['_id'];
    echo json_encode(["success" => "The line with id [$id] has been updated."]);
    exit;
}

?>