"use strict";

//List of object representing a conversation
let conversations = [
    {
        id: 0,
        timeOfDeath: new Date("11.25.2020"),
        position: {
            longitude: 47.05896542008401,
            latitude: 6.905909718143659
        },
        messages: [
            {
                id: 0,
                from: "Mathias",
                text: "yo ?",
                date: new Date("11.25.2020:10:02"),
            },
            {
                id: 1,
                from: "Nico",
                text: "Je suis maaauuvvvaiiiisssssssssssssssssssssssssssssss",
                date: new Date("11.25.2020:10:04"),
            },
            {
                id: 2,
                from: "Valentin",
                text: "69",
                date: new Date("11.25.2020:10:10"),
            }
        ]
    },
    {
        id: 1,
        timeOfDeath: new Date("11.25.2020"),
        position: {
            longitude: 47.059923018139436,
            latitude: 6.946781662949565
        },
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
            map: null,
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
        toggleDropPage() {
            this.$refs.drop_page.classList.toggle("hide-drop-page");
            this.$refs.drop_page.classList.toggle("display-drop-page");
        },
        toggleMessagePage() {
            this.$refs.message_page.classList.toggle("hide-message-page");
            this.$refs.message_page.classList.toggle("display-message-page");
        },
    },
    watch: {
        //When the conversations is updated
        conversations: function (newConversations) {
            //Foreach conversations
            newConversations.forEach((conversation) => {
                let location = [conversation.position.latitude, conversation.position.longitude];
        
                // create a HTML element for each feature
                let el = document.createElement('div');
                el.className = 'marker_message';

                //Add the bottle to the map
                new mapboxgl.Marker(el)
                    .setLngLat(location)
                    .addTo(this.map);
            });
        }
    }
})

let currentLocation = [];


function onReady(){    
    
    //Init the map
    app.map = new mapboxgl.Map({
        container: 'map',
        style: 'mapbox://styles/lamousseaulini/ckia2cpii1njr1aoign6vi31p', //Style cheet
        zoom: 12 // starting zoom
    })

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
                .addTo(app.map);
    
            //Center the map to our current location
            app.map.setCenter([position.coords.longitude, position.coords.latitude]) 
        });
    
    
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function (position) {
                //Set center location
                app.map.setCenter([position.coords.longitude, position.coords.latitude])
            },
        );
    }
    else {
        alert("Geolocation is not supported by this browser.");
    }
    
    
    //At load time :
    app.map.on('load', function () {
        //Resize the map
        app.map.resize();
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
                    .addTo(app.map);
            }

            //Then in all case, add the message to the convesrations
            conversations[event.id].push(event.message)
            break;
        case 'message':
            
        default:
            
            break;
    }

}