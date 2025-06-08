<?php
//--------------------------------------------------------------------------------
// Gig
//--------------------------------------------------------------------------------
class Logger  {

    public string $logDir;    
    public string $today;    
    public string $logPrefix;
    public string $logFile;


    public function __construct(string $path, string $logPrefix)
    {
        
        // Haal laatste slash van pad en voeg de folder logs toe
        $this->logPrefix = $logPrefix;
        $this->logDir =  rtrim($path, '/\\') . DIRECTORY_SEPARATOR . 'logs';        
        $this->today = date('Y-m-d');
        $this->logFile = $this->logDir . '/' . $this->logPrefix . '_' . $this->today . '.log';

        //echo $this->logPad, PHP_EOL;
        //var_dump($this->logPad);

        // Controleer of map bestaat, zo niet: aanmaken
        if (!is_dir($this->logDir)) {
            mkdir($this->logDir, 0777, true);
        }

        // Zoek of er al een logbestand van dit type bestaat
        $logBestand = null;        
        $logFiles = glob($this->logDir . '/' . $this->logPrefix . '_*.log');

        if ($logFiles) {
            foreach ($logFiles as $bestand) {
                // Pak laatste logbestand op naam
                $logBestand = $bestand;
            }
        }

        // Als er geen bestand is, of het is ouder dan vandaag: maak nieuw bestand
        if (!$logBestand || date('Y-m-d', filemtime($logBestand)) < $this->today) {
            $logBestand = $this->logFile;
            file_put_contents($logBestand, "Nieuw logbestand aangemaakt op $this->today\n");
        } else {
            file_put_contents($logBestand, "Bestaand logbestand bijgewerkt op " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
        }
        $this->logFile = $logBestand;

    }

    public function addLog(string $bericht): void
    {
        $regel = "[" . date('Y-m-d H:i:s') . "] " . $bericht . "\n";
        //echo $regel, PHP_EOL;
        file_put_contents($this->logFile, $regel, FILE_APPEND);
    }
}