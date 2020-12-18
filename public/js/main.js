"use strict";

let currentLocation = [];
let sm = new SocketMessage(onMessage, () => {
    wsReady = true;
    onReady();
});

let vueReady = false;
let wsReady = false;

//List of object representing a conversation
let conversations = [];

let app = new Vue({
    el: "#app",
    data() {
        return {
            username: "",
            email: "nico@gmail.com",
            conversations: conversations,
            updating: 0,
            updateMessage: true,
            map: null,
            currentConversation: null,
            bottleMarkers: [],
            myPositionMarker: null
        }
    },
    mounted() {
        setTimeout(() => {
            let data = this._vnode.data.attrs.mdata;
            console.log(data);
            this.username = data.username;
            this.id = data.id;
        });
        
        this.$nextTick(function () {
            // Code that will run only after the
            // entire view has been rendered
            vueReady = true;
            onReady();
            window.scrollTo(0, 0);
        });
        setTimeout(() => {
            window.scrollTo(0, 0);
        }, 200);
        
        setInterval(() => {
            this.updating++;
        }, 6e4);
    },
    methods: {
        getTimeLeftStr(timeOfDeath) {
            this.updating
            let timeLeft = new Date(new Date(timeOfDeath).getTime() - new Date().getTime());

            try {

                timeLeft = timeLeft.setHours(timeLeft.getHours()-1);
                return this.timeToStr(timeLeft);
            } catch (Exception) {
                return "00:00";
            }
        },
        timeToStr(time) {
            time = new Date(time);
            
            //Format
            return time.toLocaleTimeString('ch-FR', { hour: '2-digit', minute: '2-digit' });
        },
        toggleDropPage() {
            this.$refs.drop_page.classList.toggle("hide-drop-page");
            this.$refs.drop_page.classList.toggle("display-drop-page");
            if (this.$refs.drop_page.classList.contains("display-drop-page")) {
                document.getElementById('life-time-input').value = "00:30";
                document.getElementById('first-message-input').value = "";
            }
        },
        toggleMessagePage(conversationId) {
            //If conversastionID is set, change current conversation
            if(conversationId >= 0)
            {
                //Set the new current conversation
                this.currentConversation = this.conversations.find(conv => conv.id == conversationId);

                //Remove text in text area
                this.clearTextInput(this.$refs.textareamessage);
                
                //Remove image in text area
                this.clearTextInput(this.$refs.uploadImageName);
            }
            setTimeout(() => {
                this.$refs.message_page.classList.toggle("hide-message-page");
                this.$refs.message_page.classList.toggle("display-message-page");
            }, 1);
            
            

            //Scrool the message page to the end
            this.scrollDownConversation(1000);       
        },
        sendMessage() {
            //Get text
            let text = this.$refs.textareamessage.value;
            
            //Get the image
            let image = this.$refs.uploadImage.files[0];
            
            //let image64 = image == undefined ? null : "data:image/png;base64," + window.btoa(image);
            encode(this.$refs.uploadImage.files, (image64) => {                
                //If the message is not empty or the image not null
                if(text != "" || image != null)
                {
                    //Remove text in text area
                    this.clearTextInput(this.$refs.textareamessage);
                    
                    //Remove image in text area
                    this.clearTextInput(this.$refs.uploadImageName);

                    //Rest file input
                    this.$refs.uploadImage.value = null;

                    sm.send('message', {'message': text, 'parent': this.currentConversation.id, 'image': image64});
                    
                }
            });
        },
        triggerUpload() {
            this.$refs.uploadImage.click();
        },
        likeMessage(messageId) {
            if (Number.isInteger(messageId)) {
                sm.send('likeMessage', {'messageID': messageId});
            }
        },
        clearTextInput(element) {
            if (element != undefined) {
                element.value = "";
                element.classList.remove("active");
                element.style.height = null;
                M.updateTextFields();
            }
        },
        scrollDownConversation(updateTime = 1) {
            setTimeout(() => {                         
                app.$refs.conversation.scrollTo({
                    top: Number.MAX_SAFE_INTEGER,
                    behavior: 'smooth'
                });

            }, updateTime);
        },
        centerOnMe() {
            //Center the map to our current location
            app.map.setCenter([currentLocation[0], currentLocation[1]]);
        },
    },
    computed: {
        getMyBottles() {
            let myBottles = [];

            this.conversations.forEach((conv) => {
                //Add to the return array if the first message is your
                if(conv.messages[0].author == this.id)
                    myBottles.push(conv);
            });

            return myBottles;
        }
    },
    watch: {
        conversations: {
            //When the conversations is updated
            handler: function (newConversations) {
                for (let m of this.bottleMarkers) {
                    m.remove();
                }
                this.bottleMarkers = [];
                
                //Foreach conversations
                newConversations.forEach((conversation) => {
                    let location = [conversation.long, conversation.lat];
                    
                    // create a HTML element for each feature
                    let el = document.createElement('div');
                    el.className = 'marker_message';
                    
                    //Add an event when we click on the marker
                    el.onclick = () => {this.toggleMessagePage(conversation.id)};
                    
                    //Add the bottle to the map
                    this.bottleMarkers.push(new mapboxgl.Marker(el)
                    .setLngLat(location)
                    .addTo(this.map));
                });
            }, deep: true
        }
    }
});




function onReady(){    
    
    if(!(vueReady && wsReady))
        return;

    //Init the map
    app.map = new mapboxgl.Map({
        container: 'map',
        style: 'mapbox://styles/lamousseaulini/ckia2cpii1njr1aoign6vi31p', //Style cheet
        zoom: 12 // starting zoom
    });
    
    //Set the side nav draggable
    let elem = app.$refs.sidenav;
    let instance = M.Sidenav.init(elem);
    instance.isDragged = true;

    //Listen to change in location
    navigator.geolocation.watchPosition(function (position) {
        let myLocation = [position.coords.longitude, position.coords.latitude]

        //If first time, center !
        if(currentLocation.length == 0 ){
            currentLocation = myLocation;
            app.centerOnMe();
        }
        else
        {
            currentLocation = myLocation;
        }
        
        // create a HTML element for each feature
        var el = document.createElement('div');
        el.className = 'marker';
        
        //Little popup
        let popup = new mapboxgl.Popup({offset: 25})
        .setText('Your position ;)');
        
        // make a marker for each feature and add to the map
        if (app.myPositionMarker == null) {
            app.myPositionMarker = new mapboxgl.Marker(el)
            .setLngLat(myLocation)
            .setPopup(popup)
            .addTo(app.map);
        } else {
            app.myPositionMarker.setLngLat(myLocation);
        }
        
        sm.send('newpos', {'long': myLocation[0], 'lat': myLocation[1]});
        
    }, (error) =>{
        console.log(error);
    });
    
    
    if (!navigator.geolocation)
        alert("Geolocation is not supported by this browser.");
    
    
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
    if (data.message == "") {
        return;
    }
    
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

    //Remove texts
    app.clearTextInput(app.$refs.firstmessage);
    
    //Hide the create conversation window
    app.toggleDropPage();
    
    return false;
}


function onMessage(type, data) {
    if(type != 'conversations')
        console.log("onMessage", {type, data});

    switch (type) {
        case 'conversation':
            app.conversations.push(data);
            
            //If this is a new conversation conversation and we are the author, display it
            if(data.author == app.id) {
                app.toggleMessagePage(data.id);
            }
        
        case 'message':

            for (let [i, c] of app.conversations.entries()) {
                if (c.id === data.parent) {
                    Vue.set(app.conversations[i].messages, c.messages.length, data);
                    break;
                }
            }
  
            //Scrool the message page to the end
            app.scrollDownConversation(data.image == null ? 1 : 500);

            //Test if the message come from one of our conv
            if(app.getMyBottles.filter( (bottle) => { return bottle.id == data.parent;}).length != 0 && data.author != app.id){
                console.log("here");
                let toToast = '<a onclick="app.toggleMessagePage(' + data.parent + ')">' + 
                (data.content == "" ? "Click to open the image" : data.content) +'</div>';
                console.log(toToast);
                M.toast({html: toToast}); 
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
            }
            // Remove old data if its not sent by server
            if(!found) {
                app.conversations.splice(oldI, 1);
            }
        }
        
        // Create data that didn't exist before
        data.forEach(newConv => {
            Vue.set(app.conversations, app.conversations.length, newConv);
        });
        
        break;
        case 'like':
            for (let [i, c] of app.conversations.entries()) {
                if (c.id === data.convID) {
                    for (let [j, m] of c.messages.entries()) {
                        if (m.id === data.messageID) {
                            Vue.set(app.conversations[i].messages[j], 'nbLike', data.nbLike);
                            break;
                        }
                    }
                    break;
                }
            }
            /*console.log(app.conversations[data.convID]);
            app.conversations[data.convID].messages[data.messageID].nbLike = data.nbLike;*/
        default:
        
        break;
    }
    
}

function encode(selectedfile, callback) {
    if (selectedfile.length > 0) {
        var imageFile = selectedfile[0];
        var fileReader = new FileReader();
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