<?php

include_once '../settings.php';

$music_maker = new MusicMaker();

$data = json_decode(stripslashes($_POST['data']));
$action = $data->action;
if (isset($data->params))
    $params = $data->params;

switch ($action) {
    case 'play':
        echo $params->file;
        $music_maker->play($params->file);
    break;
    case 'generate':
        unlink('../songs/'.$_SESSION['song_title'].'.mid');
        $song = $music_maker->readGlobal('../songs/'.$params->file);
        if ($params->style == 'random') {
            $new_song = $music_maker->makeRandom($song, $params->structure, $params->similarity);
        } else if ($params->style == 'smart') {
            $new_song = $music_maker->makeSmart($song, $params->structure, $params->similarity);
        } else if ($params->style == 'smarter') {
            $new_song = $music_maker->makeSmarter($song, $params->structure, $params->similarity);
        }
        $music_maker->writeGlobal($new_song, false);

        $_SESSION['song_title'] = $new_song->title;

        //echo "<pre>";
        //echo print_r($new_song->structure);
        //echo "</pre>";

        //$music_maker->play("Brand New Song.mid");
    break;
    case 'getTitle':
        echo $_SESSION['song_title'];
    break;
    default:
        error_log("Error");
    break;
}

?>