"use strict";

mapboxgl.accessToken = 'pk.eyJ1IjoibGFtb3Vzc2VhdWxpbmkiLCJhIjoiY2tobTgxOXliMGU1bzJ3cm5xaGZ2b2d0NiJ9.B46q3gvGFh55OpLpZmhLDQ';

//List of object representing a conversation
let conversations = [
    {
        id: 0,
        timeOfDeath: new Date("11.25.2020"),
        position: 69,
        messages: [
            {
                text: "yo ?",
                date: 69
            },
            {
                text: "bite ?",
                date: 69
            },
            {
                text: "69",
                date: 69
            }
        ]
    },
    {
        id: 1,
        timeOfDeath: new Date("11.25.2020"),
        position: 69,
        messages: [
            {
                text: "yo ?",
                date: 69
            },
        ]
    }
];

let app = new Vue({
    el: "#app",
    data() {
        return {
            connected: true,
            username: "",
            email: "nico@gmail.com",
            conversations: conversations,
            updating: 0,
        }
    },
    mounted() {
        setTimeout(() => {
            let data = this._vnode.data.attrs.mdata;
            console.log(data);
            this.username = data.username;
        });

        setInterval(() => {
            this.updating++;
        }, 6e4);
    },
    methods: {
        getTimeLeftStr(timeOfDeath) {
            this.updating
            let timeLeft = new Date(timeOfDeath - new Date());
            return timeLeft.getHours() + ':' + timeLeft.getMinutes();
        }
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

//Listen to change in location
navigator.geolocation.watchPosition(
    function (position) {
        let myLocation = [position.coords.longitude, position.coords.latitude]



        // create a HTML element for each feature
        var el = document.createElement('div');
        el.className = 'marker';

        // make a marker for each feature and add to the map
        new mapboxgl.Marker(el)
            .setLngLat(myLocation)
            .addTo(map);

        //Center the map to our current location
        map.setCenter([position.coords.longitude, position.coords.latitude])


    });


if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(
        function (position) {
            //Set center location
            map.setCenter([position.coords.longitude, position.coords.latitude])
        },
    );
}
else {
    alert("Geolocation is not supported by this browser.");
}


//At load time :
map.on('load', function () {
    //Resize the map
    map.resize();
});

//Init time picker
$(document).ready(function () {
    let picker = $('.timepicker').timepicker(
        {
            defaultTime: '00:30',
            twelveHour: false,
        }
    );
});

function test() {
    $("#drop-page").toggleClass("hide-drop-page");
    $("#drop-page").toggleClass("display-drop-page");
}

$('#return-to-map-btn').click(test);
$('#drop-btn').click(test);



var connection = new WebSocket('ws://localhost:8080');
connection.onopen = function (e) {
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

    var data = getFormData($('#conversationForm'));

    sm.send("newConversation", data);

    return false;

    //conn.send(data);
}

function getFormData($form) {
    var unindexed_array = $form.serializeArray();
    var indexed_array = {};

    $.map(unindexed_array, function (n, i) {
        indexed_array[n['name']] = n['value'];
    });

    return indexed_array;
}

let sm = new SocketMessage(onMessage);

function onMessage(event) {
    console.log(event);

    switch (event.type) {
        case 'conversation':
            //If the conversation does no exist
            if (!(event.id in conversations)) {
                // create a HTML element for each feature
                var el = document.createElement('div');
                el.className = 'marker';

                conversations[event.id] = []

                // make a marker for each feature and add to the map
                new mapboxgl.Marker(el)
                    .setLngLat([event.position.longitude, event.position.latitude])
                    .addTo(map);
            }

            //Then in all case, add the message to the convesrations
            conversations[event.id].push(event.message)
            break;
        case 'message':
            
        default:
            
            break;
    }

}