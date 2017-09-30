function playSong() {
    var song_file = document.getElementById('select_song').value;
    MIDIjs.play('../songs/'+song_file);
}

function stopSong() {
    MIDIjs.stop();
}

function generateSong() {
    var song_file = document.getElementById('select_song').value;
    var structure_elem = document.getElementById('structure').value;
    var similar_elem = document.getElementById('similarity').value;
    var creation_style = document.getElementById('select_style').value;

    if (!structure_elem || !similar_elem) {
        alert("Invalid parameters");
        return;
    }

    $('#loading').show();
    $('#view_struc').hide();

    $.ajax({
        type: "POST",
        url: "/maker2/client/engine.php",
        data: {
            data : JSON.stringify({
                "action" : 'generate',
                "params" : {
                    file : song_file,
                    structure : structure_elem,
                    similarity : similar_elem,
                    style : creation_style
                }
            })
        }, 
        cache: false,

        success: function(response) {
            $('#view_structure').html(response);
            $.ajax({
                type: "POST",
                url: "/maker2/client/engine.php",
                data: {
                    data : JSON.stringify({
                        "action" : 'getTitle',
                    })
                }, 
                cache: false,

                success: function(response) {
                    $('#loading').hide();
                    $('#view_struc').show();
                    MIDIjs.play('../songs/'+response+'.mid');
                }
            });
        }
    });
}

function downloadSong() {
    $.ajax({
        type: "POST",
        url: "/maker2/client/engine.php",
        data: {
            data : JSON.stringify({
                "action" : 'getTitle',
            })
        }, 
        cache: false,

        success: function(response) {
            document.getElementById('iframe').src = '/maker2/songs/'+response+'.mid';
        }
    });
}

function viewSongStructure(e) {
    e.preventDefault();
    if ($('#view_struc').text() == "View Song Structure") {
        $('#view_structure').show();
        $('#view_struc').text("Hide Song Structure");
    } else {
        $('#view_structure').hide();
        $('#view_struc').text("View Song Structure");
    }
}