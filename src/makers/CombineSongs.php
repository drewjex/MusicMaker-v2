<?php

require 'settings.php';

class CombineSongs extends Maker implements IMaker {

    public $base_songs = array();
    public $music_generator;

    public function __construct($params) {
        $this->base_songs = $params['songs'];
        $this->music_generator = new MusicGenerator();
    }

    public function geneticAlgorithm($proposed_pattern_structures, $proposed_rhythm_structures, $patterns) {
        $iterations = 0;
        $population = $this->getRandomPopulation(10); //start with random chain from random song.

        while ($iterations < 100) {
            $selected = $this->getIndividualsWithBestPopulation($population, 2);
            $selected = $this->performCrossOver($selected);
            $selected = $this->performMutation($selected);
            $population = $selected;
            $iterations++;
        }

        return $this->getIndividualsWithBestPopulation($population, 1);
    }

    public function getRandomPopulation($number, &$proposed_pattern_structures) {
        $population = array();

        for ($i=0; $i<$number; $i++) {
            $rand_song = rand(0, count($proposed_pattern_structures)-1);
            $rand_measure = rand(0, count($proposed_pattern_structures[$rand_song][0])-1);
            $rand_chain = $proposed_pattern_structures[$rand_song][0][$rand_measure]; //for now, only first track
            $population[] = array($rand_chain);
        }

        return $population;
    }

    public function getIndividualsWithBestPopulation($population, $number) {
        //returns two individuals in population with highest score.
    }

    public function performCrossOver($selected) {

    }

    public function performMutation($selected) {

    }

    public function makeSameKey($songs, $increment, $similarity) {
        //$rhythm_patterns = array();
        $patterns = array();

        foreach ($songs as $song) {
            //$rhythm_patterns[] = $this->music_generator->getRhythmicPatternsByIncrement($song, $increment, $similarity);
            $patterns[] = $this->music_generator->getPatternsByIncrement($song, $increment, $similarity);
        }

        //take the first song
        $root = $patterns[0];

        $root_note_sack = array();
        foreach ($root as $track) {
            foreach ($track->patterns as $p) {
                foreach ($p->note_sack as $note) {
                    $root_note_sack[] = $note;  
                }
            }
        }

        //echo "<pre>";
        //echo print_r($root_note_sack);
        //echo "</pre>";


        $increment_values = array();
        foreach ($patterns as $key => $value) {
            if ($key == 0)
                continue; //skip the first

            $max_similarity = 0;
            $max_increment = 0;
            
            for ($i=0; $i<12; $i++) {

                $song_root_sack = array();
                foreach ($value as $v) {
                    foreach ($v->patterns as $p) {
                        foreach ($p->note_sack as $note) {
                            $song_root_sack[] = $note+$i;  
                        }
                    }
                }

                $similarity = count(array_intersect($root_note_sack, $song_root_sack));

                echo "SIMILARITY:".$similarity."<br>";

                if ($similarity > $max_similarity) {
                    $max_similarity = $similarity;
                    $max_increment = $i;
                }
            }

            $increment_values[$key] = $max_increment;
            echo "INCREMENT:".$max_increment."<br>";
        }

        //make it all the same key
        foreach ($increment_values as $key => $value) {
            foreach ($patterns[$key] as $key4 => $value4) {
                foreach ($patterns[$key][$key4]->patterns as $key2 => $value2) {
                    foreach ($value2->note_sack as $key3 => $value3) {
                        $patterns[$key][$key4]->patterns[$key2]->note_sack[$key3] = $value3+1; //$value
                    }
                }
            }
        }

        return $patterns;

    }

    public function combineIntoOneSong($songs, $increment, $similarity) {
        $rhythm_patterns = array();
        $patterns = $this->makeSameKey($songs, $increment, $similarity);

        foreach ($songs as $song) {
            $rhythm_patterns[] = $this->music_generator->getRhythmicPatternsByIncrement($song, $increment, $similarity);
            $patterns[] = $this->music_generator->getPatternsByIncrement($song, $increment, $similarity);
        }

        $iteration = 0;
        foreach ($rhythm_patterns as $key => $value) { //each song
            $pattern = $value->patterns;
            $structure = $value->structure;
            $new_pattern = array();
            foreach ($pattern as $key2 => $value2) {
                $new_pattern[$iteration] = $pattern[$key2];
                $iteration++;
            }
            $rhythm_patterns[$key]->patterns = $new_pattern;
        }

        $iteration = 0;
        foreach ($patterns as $key => $value) {
            $pattern = $value->patterns;
            $structure = $value->structure;
            $new_pattern = array();
            foreach ($pattern as $key2 => $value2) {
                $new_pattern[$iteration] = $pattern[$key2];
                $iteration++;
            }
            $patterns[$key]->patterns = $new_pattern;
        }

        return array($rhythm_patterns, $patterns);
    }

    public function make() {

        $increment = 8;
        $similarity = 4;
        $songs = $this->base_songs;

        //echo print_r($songs);

        //$result = $this->combineIntoOneSong($songs, $increment, $similarity);
        $result = $this->makeSameKey($songs, $increment, $similarity);
        $rhythm_patterns = $this->music_generator->getRhythmicPatternsByIncrement($songs[1], $increment, $similarity);
        $patterns = $result[1];
        

        //echo "<pre>";
        //echo print_r($result);
        //echo "</pre>";

        //$piece_per_measure = floor(16/$increment);

        //$rhythm_patterns = array();
        //$patterns = array();

        /*foreach ($songs as $song) {
            $rhythm_patterns[] = $this->music_generator->getRhythmicPatternsByIncrement($song, $increment, $similarity);
            $patterns[] = $this->music_generator->getPatternsByIncrement($song, $increment, $similarity);
        }

        $num_tracks = count($songs[rand(0, count($songs)-1)]->structure);

        //take the first song
        $root = $patterns[0];

        $root_note_sack = array();
        foreach ($root->patterns as $p) {
            foreach ($p->note_sack as $note) {
                $root_note_sack[] = $note;  
            }
        }


        $increment_values = array();
        foreach ($patterns as $key => $value) {
            if ($key == 0)
                continue; //skip the first

            $max_similarity = 0;
            $max_increment = 0;
            
            for ($i=0; $i<12; $i++) {

                $song_root_sack = array();
                foreach ($value->patterns as $p) {
                    foreach ($p->note_sack as $note) {
                        $song_root_sack[] = $note+$i;  
                    }
                }

                $similarity = count(array_intersect($root_note_sack, $song_root_sack));

                if ($similarity > $max_similarity) {
                    $max_similarity = $similarity;
                    $max_increment = $i;
                }
            }

            $increment_values[$key] = $max_increment;
        }

        //make it all the same key
        foreach ($increment_values as $key => $value) {
            foreach ($patterns[$key]->patterns as $key2 => $value2) {
                foreach ($value2->note_sack as $key3 => $value3) {
                    $patterns[$key]->patterns[$key2]->note_sack[$key3] = $value3+$value;
                }
            }
        }*/

        /*$proposed_rhythm_structures = array(); //CONTINUE HERE LATER
        $proposed_pattern_structures = array();

        $counter = 0;
        foreach ($rhythm_patterns as $rhythm_pattern) {
            foreach ($rhythm_pattern as $rhythm_key => $object) {
                $rhythm_pieces = array();
                $pattern = $object->patterns;
                $rhythm_structure = $object->structure;

                //echo "<pre>";
                //echo print_r($rhythm_structure);
                //echo "</pre>";

                $rhythm_proposed_structure = array();
                $str = null;
                for ($i=0; $i<max(array_keys($rhythm_structure))+1+(16/$increment); $i++) {
                    if ($i % $piece_per_measure == 0) {
                        if ($str != null)
                            $rhythm_proposed_structure[] = substr($str, 0, -1);
                        $str = null;
                    }
                    if (array_key_exists($i, $rhythm_structure)) {
                        $str .= (string) $rhythm_structure[$i].'|';
                    } else {
                        $str .= 'N|';
                    }
                } //may need to add padding at end if it ends mid-measure - keep in mind

                $proposed_rhythm_structures[] = $rhythm_proposed_structure;
            }
        }

        foreach ($patterns as $individual_pattern) {
            foreach ($individual_pattern as $rhythm_key => $object) {
                $pattern = $object->patterns;
                $structure = $object->structure;

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

                $proposed_pattern_structures[] = $proposed_structure;
            }
        }*/

        //now, we have structures for each thing, and the notes. Now we can run our genetic algorithm.

        //we just need to find the combination that works best when combining songs.

        //we can define structure, or just set one. 

        /*$overall_song_structure = array(0, 1, 0, 1); //ABAB
        $unique_parts = array_unique($overall_song_structure);

        $final_patterns = array();
        $final_proposed_structure = array();

        $parts = array();

        //just for one track for now.

        foreach ($unique_parts as $key => $value) {

            $num_measures = 10;

            $part_structure = array();

            for ($i=0; $i<$num_measures; $i++) {
                $song_index = rand(0, count($songs)-1);
                $measure_index = rand(0, count($proposed_pattern_structures[$song_index][0])-1);

                $part_structure[] = $proposed_pattern_structures[$song_index][0][$measure_index];
                //use genetic algorithm for each part, then simply combine them

                $song_index++;
                $measure_index++;
            }

            $parts[$value] = $part_structure;
        }*/
        
        //$final_proposed_structure

        //now, get the proposed structure of the first song (notes and rhythm). For each pattern it refers to, look through each of the patterns for song two (or remaining?)
        //and see if there are any note_sacks that exists that are similar to the note_sack of the pattern. If there are, then replace rhythm and note patterns for that song.

        //things to decide
        //how many tracks in final piece? - pick random number out of those provided.
        //put everything in the same key. Key of C
        //how do we combine patterns? - simply d ump notes together, and only play notes together where that exists in the actual song
        //how do we combine rhythm patterns?

        $num_tracks = count($songs[rand(0, count($songs)-1)]->structure);

        $the_pattern;
        $the_rhythms;

        //$rhythm_patterns = $this->music_generator->getRhythmicPatternsByIncrement($song, $increment, $similarity);
        //$patterns = $this->music_generator->getPatternsByIncrement($song, $increment, $similarity);
        $music_changer = new MusicChanger();

        $piece_per_measure = floor(16/$increment);

        $song_structure = array();
        $parts = array();

        $first_loop = true;
        $aligned_notes = array();

        $rhythm_tracks = array();
        $rhythm_track_structures = array();
        foreach ($rhythm_patterns as $rhythm_key => $object) {
            $rhythm_pieces = array();
            $pattern = $object->patterns;
            $rhythm_structure = $object->structure;

            //echo "<pre>";
            //echo print_r($rhythm_structure);
            //echo "</pre>";

            $rhythm_proposed_structure = array();
            $str = null;
            for ($i=0; $i<max(array_keys($rhythm_structure))+1+(16/$increment); $i++) {
                if ($i % $piece_per_measure == 0) {
                    if ($str != null)
                        $rhythm_proposed_structure[] = substr($str, 0, -1);
                    $str = null;
                }
                if (array_key_exists($i, $rhythm_structure)) {
                    $str .= (string) $rhythm_structure[$i].'|';
                } else {
                    $str .= 'N|';
                }
            } //may need to add padding at end if it ends mid-measure - keep in mind

            //now go through and assign each value a new number if it's the first time it's been seen
            $new_structure = array();
            foreach ($rhythm_proposed_structure as $key => $value) {
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

            //echo "<pre>";
            //echo print_r($new_structure);
            //echo "</pre>";

            foreach ($pattern as $key => $value) {
                //echo "KEY:".$key."<br>";
                $max_score = -1;
                $max_piece = null;
                for ($j=0; $j<100; $j++) {
                    $piece = array();
                    if ($first_loop) {
                        for ($i=0; $i<$value->num_notes; $i++) {
                            $piece = $music_changer->addNoteFromSackInPiece($piece, $value->note_sack, $increment);
                        }
                    } else {
                        for ($i=0; $i<$value->num_notes; $i++) {
                            $aligned_piece = $aligned_notes[$key];
                            $rand_note_index = rand(0, count($aligned_piece)-1);
                            $time = $aligned_piece[$rand_note_index]->time;
                            $piece = $music_changer->addNoteFromSackInPieceAt($piece, $value->note_sack, $time, $increment);
                        }
                    }
                    $part = new Part();
                    $measures = array();
                    $chords = array();
                    $chords[] = new Chord(0, "MAJOR", $increment);
                    $measures[] = new Measure($piece, $chords);

                    $part = Part::setMeasures($measures);
                    $part->instrument = 1;
                    $score = MusicAnalyzer::getScore($part);
                    if ($score > $max_score) {
                        $max_score = $score;
                        $max_piece = $piece;
                    }
                }
                if ($first_loop) {
                    $aligned_notes[$key] = $max_piece;
                    $first_loop = true; //false
                }
                $rhythm_pieces[] = $max_piece;
            }

            $rhythm_tracks[$rhythm_key] = $rhythm_pieces;
            $rhythm_track_structures[$rhythm_key] = $rhythm_structure;
        }

        $increment = 8;
        $similarity = 1;

        foreach ($patterns as $rhythm_key => $object) {
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

            //echo "<pre>";
            //echo print_r($proposed_structure);
            //echo "</pre>";

            $pieces = array();
            foreach ($pattern as $key => $value) {
                $piece = array();
                $clone = $value->note_sack;
                for ($i=0; $i<$value->num_notes; $i++) {
                    $pattern_val = $rhythm_track_structures[$rhythm_key][array_search($key, $structure)];
                    $time = $rhythm_tracks[$rhythm_key][$pattern_val][$i]->time;
                    if ($i == 0) {
                        $piece = $music_changer->addNoteFromSackInPieceAt($piece, $clone, $time, $increment);
                    } else {
                        $piece = $music_changer->addClosestNoteFromSackInPieceAt($piece, $clone, $piece[$i-1]->note[count($piece[$i-1]->note)-1], $time, $increment);
                    }
                    if(($found_key = array_search($piece[$i-1]->note[count($piece[$i-1]->note)-1], $clone)) !== false) {
                        unset($clone[$found_key]);
                    }
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