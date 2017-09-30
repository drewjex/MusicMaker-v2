<?php

class Chord {
    
    public $bass_note;
    public $chord_value;
    //public $time;
    public $length;
    
    public function __construct($bass_note, $chord_value, $length) {
        $this->bass_note = $bass_note;
        $this->chord_value = $chord_value;
        //$this->time = $time;
        $this->length = $length;
    }
}

?>