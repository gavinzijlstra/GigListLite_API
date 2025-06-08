<?php
//--------------------------------------------------------------------------------
// Gig
//--------------------------------------------------------------------------------
class Gig  {

    public $id;
    public $datum;
    public $venue;
    public $zaal;
    public $band;
    public $opmerking;

    function __construct($id,$datum,$venue,$zaal,$band,$opmerking) {
        $this->id = $id;
        $this->datum = $datum;
        $this->venue = $venue;
        $this->zaal = $zaal;
        $this->band = $band;
        $this->opmerking = $opmerking;

    }
    
    public function getId() {
        return $this->id;
    }
    public function getBand() {
        return $this->band;
    }
    
    public function __toString() {
        $format = '%s (%s)';
        return sprintf($format, $this->id, $this->datum, $this->venue, $this->zaal, $this->band, $this->opmerking);
    }

}    
?>
