"use strict";

mapboxgl.accessToken = 'pk.eyJ1IjoibGFtb3Vzc2VhdWxpbmkiLCJhIjoiY2tobTgxOXliMGU1bzJ3cm5xaGZ2b2d0NiJ9.B46q3gvGFh55OpLpZmhLDQ';

var app = new Vue({
    el: "#app",
    data(){
        return {
            connected: true,
            pseudo: "NicoJsBad",
            email : "nico@gmail.com",
            conversations: [
                { 
                    lastmessage: "J'adore le design",
                    key: 0,
                    strlefttime: "12:59"
                },
                { 
                    lastmessage: "NICO A LEPPFFLL XDDDDDDDDDDDDDDD",
                    key: 1,
                    strlefttime: "10:02"
                },
                { 
                    lastmessage: "yo ?",
                    key: 2,
                    strlefttime: "1:19"
                },
              ],
        }   
    },
})

let map = new mapboxgl.Map({
    container: 'map',
    style: 'mapbox://styles/mapbox/streets-v11', // stylesheet location
    center: [-74.5, 40], // starting position [lng, lat]
    zoom: 9 // starting zoom
});

//Set the side nav draggable
let elem = $('.sidenav').sidenav();
let instance = M.Sidenav.getInstance(elem);
instance.isDragged = true;

//Resize the map at load
map.on('load', function () {
    map.resize();
});