"use strict";

mapboxgl.accessToken = 'pk.eyJ1IjoibGFtb3Vzc2VhdWxpbmkiLCJhIjoiY2tobTgxOXliMGU1bzJ3cm5xaGZ2b2d0NiJ9.B46q3gvGFh55OpLpZmhLDQ';

var app = new Vue({
    el: "#app",
    data() {
        return {
            connected: true,
            username: "",
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
    mounted() {
        setTimeout(() => {
            let data = this._vnode.data.attrs.mdata;
            console.log(data);
            this.username = data.username;
        });
    }
})

let map = new mapboxgl.Map({
    container: 'map',
    style: 'mapbox://styles/mapbox/streets-v11', // stylesheet location
    zoom: 12 // starting zoom
});

//Set the side nav draggable
let elem = $('.sidenav').sidenav();
let instance = M.Sidenav.getInstance(elem);
instance.isDragged = true;

function showPosition(position) {
  x.innerHTML = "Latitude: " + position.coords.latitude +
  "<br>Longitude: " + position.coords.longitude;
}

//Center the map to our current location
if (navigator.geolocation) 
{
    navigator.geolocation.getCurrentPosition(
        function(position)
        {
            //Set center location
            map.setCenter([position.coords.longitude, position.coords.latitude])
        }     
    );
}
else
{
    alert("Geolocation is not supported by this browser.");
}


//At load time :
map.on('load', function () {
    //Resize the map
    map.resize();
});

//Init time picker
$(document).ready(function(){
    /*let picker = $('.timepicker').timepicker(
         {
             defaultTime : '00:00',
             twelveHour : false,
         }
    );*/
    let picker = $('.timepicker').timepicker();
});

function test()
{
    $("#drop-page").toggleClass("hide-drop-page");
    $("#drop-page").toggleClass("display-drop-page");
}

$('#return-to-map-btn').click(test);
$('#drop-btn').click(test);



var connection = new WebSocket('ws://localhost:8080');
connection.onopen = function(e) {
    console.log("Connection established!");
};

function postConversation() {
    /*$.post("postConversation", $('#conversationForm').serialize(), (result) => {

        console.log(result);
        if (result.success === true) {
            window.location.replace("/");
        } else {
            if (result.error === "wrongPassword") {
                displayError("Wrong password. Try again.");
            } else if (result.error === "wrongUsername") {
                displayError("This username doesn't exist. Try again or <a href='signup'>sign up</a>");
            } else {
                displayError("Something went wrong");
            }
        }
    });
    return false;*/

    var data = JSON.stringify( getFormData($('#conversationForm')));
    console.log(data);

    return false;

    //conn.send(data);
}

function getFormData($form){
    var unindexed_array = $form.serializeArray();
    var indexed_array = {};

    $.map(unindexed_array, function(n, i){
        indexed_array[n['name']] = n['value'];
    });

    return indexed_array;
}