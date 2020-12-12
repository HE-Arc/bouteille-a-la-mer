"use strict";

let currentLocation = [];
let sm = new SocketMessage(onMessage);

//List of object representing a conversation
let conversations = [
    {
        id: 0,
        time_of_death: new Date("11.25.2020"),
        long: 47.05896542008401,
        lat: 6.905909718143659,
        messages: [
            {
                id: 0,
                author: "Mathias",
                content: "yo ?",
                posted: new Date("11.25.2020:10:02"),
            },
            {
                id: 1,
                author: "Nico",
                content: "Je suis maaauuvvvaiiiisssssssssssssssssssssssssssssss",
                posted: new Date("11.25.2020:10:04"),
            },
            {
                id: 2,
                author: "Valentin",
                content: "69",
                posted: new Date("11.25.2020:10:10"),
            }
        ]
    },
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
            currentConversation: {messages : []}
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
            let timeLeft = new Date(timeOfDeath) - new Date();

            return this.timeToStr(timeLeft);
        },
        timeToStr(time) {
            
            time = new Date(time);

            //Format
            return time.toLocaleTimeString('ch-FR', { hour: '2-digit', minute: '2-digit' });
        },
        toggleDropPage() {
            this.$refs.drop_page.classList.toggle("hide-drop-page");
            this.$refs.drop_page.classList.toggle("display-drop-page");
        },
        toggleMessagePage(conversationId) {
            //If conversastionID is set, change current conversation
            if(conversationId >= 0)
            {
                //Set the new current conversation
                this.currentConversation = this.conversations.find(conv => conv.id == conversationId);
            }

            this.$refs.message_page.classList.toggle("hide-message-page");
            this.$refs.message_page.classList.toggle("display-message-page");
        },
        sendMessage() {
            //Get text
            let text = this.$refs.textareamessage.value;

            //If the message is not empty
            if(text != "")
            {
                //Remove text in text area
                this.$refs.textareamessage.value = "";
            }
        }
    },
    watch: {
        //When the conversations is updated
        conversations: function (newConversations) {

            console.log(newConversations)
            
            //Foreach conversations
            newConversations.forEach((conversation) => {
                let location = [conversation.long, conversation.lat];
        
                // create a HTML element for each feature
                let el = document.createElement('div');
                el.className = 'marker_message';

                //Add an event when we click on the marker
                el.onclick = () => {this.toggleMessagePage(conversation.id)};

                //Add the bottle to the map
                new mapboxgl.Marker(el)
                    .setLngLat(location)
                    .addTo(this.map);
            });
        }
    }
})




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
    
            //Little popup
            let popup = new mapboxgl.Popup({offset: 25})
            .setText('Your position ;)');

            // make a marker for each feature and add to the map
            new mapboxgl.Marker(el)
                .setLngLat(myLocation)
                .setPopup(popup)
                .addTo(app.map);
    
            //Center the map to our current location
            app.map.setCenter([position.coords.longitude, position.coords.latitude])
    
            sm.send('newpos', {'long': myLocation[0], 'lat': myLocation[1]});
    
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


function onMessage(type, data) {
    switch (type) {
        case 'conversation':
            
        /*
            //If the conversation does no exist
            if (!(data.id in conversations)) {
                // create a HTML element for each feature
                var el = document.createElement('div');
                el.className = 'marker';

                conversations[data.id] = []

                // make a marker for each feature and add to the map
                new mapboxgl.Marker(el)
                    .setLngLat([data.position.longitude, data.position.latitude])
                    .addTo(app.map);
            }

            //Then in all case, add the message to the convesrations
            conversations[data.id].push(data.message)
            break;
            */
        case 'message':
            break;

        case 'conversations':
            app.conversations = data;
            break;
        default:
            
            break;
    }

}