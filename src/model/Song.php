<?php

class Song {
    
    public $title;
    public $author;
    public $parts = array();
    public $structure = array();
    
    public function __construct($title, $author, $structure) {
        $this->title = $title;
        $this->author = $author;
        $this->structure = $structure;   
    }
    
}

?>