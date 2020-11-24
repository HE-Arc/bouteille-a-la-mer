mapboxgl.accessToken = 'pk.eyJ1IjoibGFtb3Vzc2VhdWxpbmkiLCJhIjoiY2tobTgxOXliMGU1bzJ3cm5xaGZ2b2d0NiJ9.B46q3gvGFh55OpLpZmhLDQ';
var map = new mapboxgl.Map({
    container: 'map',
    style: 'mapbox://styles/mapbox/streets-v11', // stylesheet location
    center: [-74.5, 40], // starting position [lng, lat]
    zoom: 9 // starting zoom
});

//Resize the map at load
map.on('load', function () {
    map.resize();
});

let sm = new SocketMessage(onMessage);

function onMessage(event) {
    switch (event.type) {
        default:
            console.log(event.data);
            break;
    }


    //TODO marti fonction pour ajouter dans la vue :)
}

function postConversation() {
    var data = getFormData($('#conversationForm'));

    sm.send("conversation", data);

    return false;
}

function getFormData($form) {
    var unindexed_array = $form.serializeArray();
    var indexed_array = {};

    $.map(unindexed_array, function (n, i) {
        indexed_array[n['name']] = n['value'];
    });

    return indexed_array;
}