"use strict";

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

        this.$nextTick(function () {
            // Code that will run only after the
            // entire view has been rendered
            onReady();
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
        },
        toggle() {
            this.$refs.drop_page.classList.toggle("hide-drop-page");
            this.$refs.drop_page.classList.toggle("display-drop-page");
        },
    }
})

let currentLocation = [];


function onReady(){

    let map = new mapboxgl.Map({
        container: 'map',
        //style: 'mapbox://styles/mapbox/streets-v11', // stylesheet location
        style: 'mapbox://styles/lamousseaulini/ckia2cpii1njr1aoign6vi31p',
        zoom: 12 // starting zoom
    });
    
    
    //Set the side nav draggable
    let elem = app.$refs.sidenav
    let instance = M.Sidenav.init(elem);
    instance.isDragged = true;
    
    
    //Listen to change in location
    navigator.geolocation.watchPosition(
        function (position) {
            let myLocation = [position.coords.longitude, position.coords.latitude]
            currentLocation = myLocation;
    
    
    
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

   let picker = M.Timepicker.init(app.$refs.timepicker,
    {
        defaultTime: '00:30',
        twelveHour: false,
    }); 
}


function postConversation() {
    //var data = getFormData(app.$refs.conversationForm)
    let data = {
        "lifetime": document.getElementById('life-time-input').value,
        "message": document.getElementById('first-message-input').value
    };

    let body = {
        conversation: {
            long: currentLocation[0],
            lat: currentLocation[1],
            lifetime: data.lifetime
        },
        message: {
            message: data.message,
            image: null,
            parent: null
        }
    }
    console.log(body)
    sm.send('conversation', body);

    return false;
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