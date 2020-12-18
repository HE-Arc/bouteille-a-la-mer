'use strict';

class SocketMessage
{
    constructor(onMessage, onReady) {
        let ws = 'ws';
        let path = ':8080';
        if(window.location.protocol == 'https:') {
            ws += 's'
            path = '/wss';
        }

        let url = ws + '://' + window.location.hostname + path;
        this.ws = new WebSocket(url);

        //Call when connection is made
        this.ws.addEventListener("open", onReady);

        console.log("connecting to web socket " + url + "...");
        this.ws.addEventListener('message', event => {
            let msg = JSON.parse(event.data);
            onMessage(msg.type, msg.data);
        });
        this.ws.addEventListener('error', event => {
            alert("Error connecting to the web socket " + url);
            location.reload();
        });
    }

    send(type, data) {
        if(this.ws.readyState == this.ws.OPEN)
            this.ws.send(JSON.stringify({'type': type, 'data': data}));
        else
            console.log("Web socket is not ready");
    }
}