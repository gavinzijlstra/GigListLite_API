<?php
//------------------------------------------------------------------------------------------------------------------
// File reads a file and can return all the lines in an array
//------------------------------------------------------------------------------------------------------------------
class File {
    private $lines;    
    private $json;
    private $path;

    function __construct($path) {        
        $this->path = $path;
        $this->lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        //echo '<pre>'; print_r($this->lines); echo '</pre>';


        // Eerste regel bevat de kolomnamen
        $headers = explode('|', array_shift( $this->lines));

        $data = [];
        foreach ($this->lines as $line) {
            $waarden = explode('|', $line);
            $assoc = array_combine($headers, $waarden);
            // Sleutels in lowercase (optioneel)
            $assoc_lc = array_change_key_case($assoc, CASE_LOWER);
            $data[] = $assoc_lc;
        }

        //echo '<pre>'; print_r($data); echo '</pre>';

        // Geef JSON terug
        $this->json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        //echo '<pre>'; print_r($json); echo '</pre>';
    }

    public function getLines(){
        return $this->lines;
    }

    public function getPath(){
        return $this->path;
    }

    public function getJSON(){
        return $this->json;
    }

   

}    
?>