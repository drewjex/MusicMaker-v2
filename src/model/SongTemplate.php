<?php

class SongTemplate {
    
    public $title;
    public $author;
    public $part_templates = array();
    public $structure = array();
    
    public function __construct($title, $author, $part_templates, $structure) {
        $this->title = $title;
        $this->author = $author;
        $this->part_templates = $part_templates;
        $this->structure = $structure;   
    }
    
}

?>