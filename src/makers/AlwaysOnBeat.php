<?php

require 'settings.php';

class AlwaysOnBeat extends Maker implements IMaker {

    public $base_song;
    public $music_generator;

    public function __construct($params) {
        $this->base_song = $params['song'];
        $this->music_generator = new MusicGenerator();
    }

    public function make() {

        $increment = 4;
        $similarity = 1;
        $song = $this->base_song;

        $rhythm_patterns = $this->music_generator->getRhythmicPatternsByIncrement($song, $increment, $similarity);
        $patterns = $this->music_generator->getPatternsByIncrement($song, $increment, $similarity);
        $music_changer = new MusicChanger();

        $piece_per_measure = floor(16/$increment);

        $song_structure = array();
        $parts = array();

        $rhythm_pieces = array();
        foreach ($rhythm_patterns as $object) {
            $pattern = $object->patterns;
            $structure = $object->structure;

            /*echo "<pre>";
            echo print_r($structure);
            echo "</pre>";*/

            $proposed_structure = array();
            $str = null;
            for ($i=0; $i<max(array_keys($structure))+1+(16/$increment); $i++) {
                if ($i % $piece_per_measure == 0) {
                    if ($str != null)
                        $proposed_structure[] = substr($str, 0, -1);
                    $str = null;
                }
                if (array_key_exists($i, $structure)) {
                    $str .= (string) $structure[$i].'|';
                } else {
                    $str .= 'N|';
                }
            } //may need to add padding at end if it ends mid-measure - keep in mind

            //now go through and assign each value a new number if it's the first time it's been seen
            $new_structure = array();
            foreach ($proposed_structure as $key => $value) {
                $new_structure[$value][] = $key;
            }

            $final_structure = array();
            $count = 0;
            foreach ($new_structure as $key => $value) {
                foreach ($value as $v) {
                    $final_structure[$v] = $count;
                }
                $count++;
            }

            ksort($final_structure);

            foreach ($final_structure as $key => $value) {
                $final_structure[$key] = $value+count($parts);
            }

            echo "<pre>";
            echo print_r($proposed_structure);
            echo "</pre>";

            foreach ($pattern as $key => $value) {
                $max_score = -1;
                $max_piece = null;
                for ($j=0; $j<1000; $j++) {
                    $piece = array();
                    for ($i=0; $i<$value->num_notes; $i++) {
                        $piece = $music_changer->addNoteFromSackInPiece($piece, $value->note_sack, $this->increment);
                        echo $piece[0]->time."<br>";
                    }
                    $part = new Part();
                    $measures = array();
                    $chords = array();
                    $chords[] = new Chord(0, "MAJOR", $this->increment);
                    $measures[] = new Measure($piece, $chords);

                    $part = Part::setMeasures($measures);
                    $part->instrument = 1;
                    $score = MusicAnalyzer::getScore($part);
                    if ($score > $max_score) {
                        $max_score = $score;
                        $max_piece = $piece;
                    }
                }
                $rhythm_pieces[] = $max_piece;
            }
        }

        foreach ($patterns as $object) {
            $pattern = $object->patterns;
            $structure = $object->structure;

            /*echo "<pre>";
            echo print_r($structure);
            echo "</pre>";*/

            $proposed_structure = array();
            $str = null;
            for ($i=0; $i<max(array_keys($structure))+1+(16/$increment); $i++) {
                if ($i % $piece_per_measure == 0) {
                    if ($str != null)
                        $proposed_structure[] = substr($str, 0, -1);
                    $str = null;
                }
                if (array_key_exists($i, $structure)) {
                    $str .= (string) $structure[$i].'|';
                } else {
                    $str .= 'N|';
                }
            } //may need to add padding at end if it ends mid-measure - keep in mind

            //now go through and assign each value a new number if it's the first time it's been seen
            $new_structure = array();
            foreach ($proposed_structure as $key => $value) {
                $new_structure[$value][] = $key;
            }

            $final_structure = array();
            $count = 0;
            foreach ($new_structure as $key => $value) {
                foreach ($value as $v) {
                    $final_structure[$v] = $count;
                }
                $count++;
            }

            ksort($final_structure);

            foreach ($final_structure as $key => $value) {
                $final_structure[$key] = $value+count($parts);
            }

            echo "<pre>";
            echo print_r($proposed_structure);
            echo "</pre>";

            $pieces = array();
            foreach ($pattern as $key => $value) {
                $piece = array();
                for ($i=0; $i<$value->num_notes; $i++) {
                    $time = $rhythm_pieces[$key][$i]->time;
                    $piece = $music_changer->addNoteFromSackInPieceAt($piece, $value->note_sack, $time, $increment);
                }
                $part = new Part();
                $measures = array();
                $chords = array();
                $chords[] = new Chord(0, "MAJOR", $increment);
                $measures[] = new Measure($piece, $chords);

                $part = Part::setMeasures($measures);
                $part->instrument = 1;

                $pieces[] = $piece;
            }

            foreach ($new_structure as $key => $value) {
                $measures = array();
                $chords = array();
                $notes = array();
                $measure_pieces = explode("|", $key);
                $count = 0;
                foreach ($measure_pieces as $kp => $vp) {
                    $first = true;
                    if ($vp == 'N') {
                        //$notes[] = new Note(array(-1), "NORMAL", $count*$increment, $increment);
                    } else {
                        //make a copy of the piece
                        $copy = array();
                        foreach ($pieces[$vp] as $k => $v) {
                            $copy[$k] = clone $v;
                        }
                        foreach ($copy as $k => $v) {
                            /*if ($first) {
                                $first = false;
                                if ($copy[$k]->time != 0) {
                                    $notes[] = new Note(array(-1), "NORMAL", $count*$increment, $copy[$k]->time);
                                }
                            }*/
                            $copy[$k]->time += $count*$increment;
                            $notes[] = $copy[$k];
                        }
                    }
                    $count++;
                }
                ksort($notes);
                $chords[] = new Chord(0, "MAJOR", 16);
                $measures[] = new Measure($notes, $chords);

                $part = Part::setMeasures($measures);
                $part->instrument = 1;
                
                $parts[] = $part;
            }

            $song_structure[] = $final_structure;
        }

        $part = new Part();
        $measures = array();
        $offset = 0;
        for ($i=0; $i<count($song_structure[0]); $i++) {
            $notes = array();
            $chords = array();
            $chords[] = new Chord(0, "MAJOR", 16);
            for ($j=0; $j<4; $j++) {
                $notes[] = new Note([60], "NORMAL", 4*$j, 4);
            }
                    
            $measures[] = new Measure($notes, $chords);
            
        }

        $part = Part::setMeasures($measures);
        $part->instrument = 117; //10 //99
        $parts[] = $part;
        $metranome_id = count($parts)-1;

        //$song_structure[][0] = $metranome_id;

        $temp = array();
        for ($i=1; $i<count($song_structure); $i++) {
            $temp [] = $song_structure[$i];
            unset($song_structure[$i]);
        }

        $song_structure[][0] = $metranome_id;

        foreach ($temp as $t) {
            $song_structure[] = $t;
        }

        $song = new Song("Brand New Song", "Drew Jex", $song_structure);
        $song->parts = $parts;

        return $song;
    }
}

?>