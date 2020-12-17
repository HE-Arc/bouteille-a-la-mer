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
            updateMessage: true,
            map: null,
            currentConversation: {messages : []}
        }
    },
    mounted() {
        document.body.style.overflow = "hidden";
        setTimeout(() => {
            let data = this._vnode.data.attrs.mdata;
            console.log(data);
            this.username = data.username;
            this.id = data.id;
        });
        
        this.$nextTick(function () {
            // Code that will run only after the
            // entire view has been rendered
            onReady();
            window.scrollTo(0, 0);
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
            
            //Get the image
            let image = this.$refs.uploadImage.files[0];
            console.log(image);
            //let image64 = image == undefined ? null : "data:image/png;base64," + window.btoa(image);
            encode(this.$refs.uploadImage.files, (image64) => {                
                //If the message is not empty or the image not null
                if(text != "" || image != null)
                {
                    //Remove text in text area
                    this.$refs.textareamessage.value = "";
                    
                    //Remove image in text area
                    this.$refs.uploadImageName.value = ""; 
                    
                    sm.send('message', {'message': text, 'parent': this.currentConversation.id, 'image': image64});
                    
                }
            });
        },
    },
    watch: {
        conversations: {
            //When the conversations is updated
            handler: function (newConversations) {
                console.log("conversation Changed");
                
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
            }, deep: true
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
    navigator.geolocation.watchPosition(function (position) {
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
        navigator.geolocation.getCurrentPosition(function (position) {
            //Set center location
            app.map.setCenter([position.coords.longitude, position.coords.latitude])
        });
    }
    else {
        alert("Geolocation is not supported by this browser.");
    }
    
    
    //At load time :
    app.map.on('load', function () {
        //Resize the map
        app.map.resize();
    });
    
    let picker = M.Timepicker.init(app.$refs.timepicker, {
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
    
    sm.send('conversation', body);
    
    //Hide the create conversation window
    app.toggleDropPage();
    
    return false;
}


function onMessage(type, data) {
    console.log("onMessage", {type, data});
    
    switch (type) {
        case 'conversation':
        app.conversations.push(data);
        
        console.log(data.author, app.id);
        //If this is a new conversation conversation and we are the author, display it
        if(data.author == app.id) {
            app.toggleMessagePage(data.id);
        }
        
        case 'message':
        //console.log(app.conversations);
        
        for (let [i, c] of app.conversations.entries()) {
            if (c.id === data.parent) {
                Vue.set(app.conversations[i].messages, c.messages.length, data);
                break;
            }
        }
        break;
        
        case 'conversations':
        //app.conversations = data;
        
        //Update existing data
        for (let [oldI, oldConv] of app.conversations.entries()) {
            let found = false;
            for(let newI = data.length-1; newI >= 0; --newI) {
                let newConv = data[newI];
                if(newConv.id === oldConv.id) {
                    found = true;
                    oldConv.messages = newConv.messages;
                    data.splice(newI, 1);
                }
                
                // Remove old data if its not sent by server
                if(!found)
                app.conversations.splice(oldI, 1);
            }
        }
        
        // Create data that didn't exist before
        data.forEach(newConv => {
            Vue.set(app.conversations, app.conversations.length, newConv);
        });
        
        break;
        default:
        
        break;
    }
    
}

function encode(selectedfile, callback) {
    if (selectedfile.length > 0) {
        var imageFile = selectedfile[0];
        var fileReader = new FileReader();
        console.log("yo");
        fileReader.onload = function(fileLoadedEvent) {
            var srcData = fileLoadedEvent.target.result;

            return callback(srcData);
            /*var newImage = document.createElement('img');
            newImage.src = srcData;
            document.getElementById("dummy").innerHTML = newImage.outerHTML;
            document.getElementById("txt").value = document.getElementById("dummy").innerHTML;*/
        }
        fileReader.readAsDataURL(imageFile);
    } else {
        return callback(null);
    }
}