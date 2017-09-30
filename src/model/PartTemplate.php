<?php

class PartTemplate {
    
    public $instrument;
    public $time_signature;
    public $time_increment;
    public $chords = array();
    public $part_type;
    
    public function __construct($part_type, $chords=null, $instrument="Piano", $time_signture="4/4", $time_increment=16) {
        $this->instrument = $instrument;
        $this->time_signature = $time_signature;
        $this->time_increment = $time_increment;
        $this->chords = $chords;
        $this->part_type = $part_type;
    }
    
}

?>