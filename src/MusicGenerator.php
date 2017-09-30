<?php

class MusicGenerator {

    public $population = array();

    public function __construct() {

    }

    public function generatePopulation($size=10) {
        for ($i=0; $i<$size; $i++) {
            $this->population[] = $this->generateRandomSong();
        }
    }

    public function generateRandomSong() {
        return $song;
    }

    public function mutate($part) {

    }

    public function crossover($part1, $part2) {

    }

    public function run($threshold) {

    }

    public function setAnalyzers($analyzers) {
        $this->music_analyzer->setAnalyzers($analyzers);
    }

    public function getPatterns($song) {
        $patterns = array();
        foreach ($song->parts as $part) {
            $patterns[] = MusicAnalyzer::getPatterns($part);
        }

        return $patterns;
    }

    public function getPatternsByIncrement($song, $increment, $similarity) {
        $patterns = array();
        foreach ($song->parts as $part) {
            $patterns[] = MusicAnalyzer::getPatternsByIncrement($part, $increment, $similarity);
        }

        return $patterns;
    }

    public function getRhythmicPatternsByIncrement($song, $increment, $similarity) {
        $patterns = array();
        foreach ($song->parts as $part) {
            $patterns[] = MusicAnalyzer::getRhythmicPatternsByIncrement($part, $increment, $similarity);
        }

        return $patterns;
    }

    public function getSongPatternsByIncrement($song, $increment, $similarity) {
        return array(MusicAnalyzer::getSongPatternsByIncrement($song, $increment, $similarity));
    }

    public function getSongRhythmicPatternsByIncrement($song, $increment, $similarity) {
        return array(MusicAnalyzer::getSongRhythmicPatternsByIncrement($song, $increment, $similarity));
    }

    public function getChords($song) {
        $chords = array();

    }

}

?>