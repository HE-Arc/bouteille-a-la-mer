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

function onMessage(message)
{
    console.log(message);

    //TODO marti fonction pour ajouter dans la vue :)
}