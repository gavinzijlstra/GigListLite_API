<?php
//------------------------------------------------------------------------------------------------------------------
// File reads a file and can return all the lines in an array.
//------------------------------------------------------------------------------------------------------------------
class GigList {
    
    private $file;

    function __construct($file) {
        $this->file = $file;        
    }

    public function getJson(){
        $file = $this->file;
        $json = $file->getJson();        
        $gigs = json_decode($json, true); 
        
        // Sorteer op datum (meest recent bovenaan)
        usort($gigs, function ($a, $b) {
            $datumA = DateTime::createFromFormat('d-m-Y', $a['datum']);
            $datumB = DateTime::createFromFormat('d-m-Y', $b['datum']);
            
            // Vergelijk aflopend (nieuwste eerst)
            return $datumB <=> $datumA;
        });
        
        $json = json_encode($gigs, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        // Zet "\\n" terug naar "\n"
        $json = str_replace('\\\\n', '\\n', $json);
        return $json;
    }

    function getUniqueKey() {
        $node = $this->findNodeWithHighestId();
        return $node['_id'] + 1;
    }

    function findNodeWithHighestId() {
        $data = json_decode($this->file->getJson(), true); // Decode JSON naar een associatieve array
        $maxNode = null;
        $maxId = -1;

        foreach ($data as $node) {
            $currentId = (int)$node['_id'];
            if ($currentId > $maxId) {
                $maxId = $currentId;
                $maxNode = $node;
            }
        }

        return $maxNode;
    }

    function jsonNodeToLine($id, $node) {
    
        // Stel de outputregel samen
        //$line = $node['id'] . '|' .
        $line = $id . '|' .
                $node['datum'] . '|' .
                $node['venue'] . '|' .
                $node['zaal'] . '|' .
                $node['band'] . '|' .
                $node['opmerking'];

        return $line;
    }

    function verwijderNodeOpId($teVerwijderenId) {
        $data = json_decode($this->file->getJson(), true); // Decodeer JSON naar array

        // Filter de array en verwijder de node met de opgegeven ID
        $nieuweData = array_filter($data, function($item) use ($teVerwijderenId) {
            return $item['_id'] != $teVerwijderenId;
        });

        // Zorg ervoor dat de array opnieuw wordt geïndexeerd (optioneel)
        $nieuweData = array_values($nieuweData);

        // Encodeer opnieuw naar JSON
        return json_encode($nieuweData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    public function save($json) {
        $data = json_decode($json, true); // Decode JSON naar een associatieve array
        
        // Voeg een newline toe vóór de nieuwe regel
        $file = $this->file; 
        $line = '_ID|DATUM|VENUE|ZAAL|BAND|OPMERKING';
        $line = $line . PHP_EOL;
        file_put_contents($file->getPath(), $line);

        foreach ($data as $node) {
            $line = $this->jsonNodeToLine($node['_id'], $node);
            $line = $line . PHP_EOL;
            file_put_contents($file->getPath(), $line, FILE_APPEND); // Add lines to file
        }
    }

    public function update($updateNode) {
        $data = json_decode($this->getJson(), true); // Decode JSON naar een associatieve array
        
        // Voeg een newline toe vóór de nieuwe regel
        $file = $this->file; 
        $line = '_ID|DATUM|VENUE|ZAAL|BAND|OPMERKING';
        $line = $line . PHP_EOL;
        file_put_contents($file->getPath(), $line);

        foreach ($data as $node) {
            echo "Update : ". $node['_id'] . PHP_EOL;
            if ($updateNode['_id'] === $node['_id']) {                                            
                $line = $this->jsonNodeToLine($updateNode['_id'], $updateNode);            
            } else {
                $line = $this->jsonNodeToLine($node['_id'], $node);
            }
            // Zet "\n" om naar "\\n"
            $line = str_replace("\n", "\\n", $line);
            $line = $line . PHP_EOL;
            file_put_contents($file->getPath(), $line, FILE_APPEND); // Add lines to file
        }
    }
}  
?>