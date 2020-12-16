'use strict';

class SocketMessage
{
    constructor(onMessage) {
        let ws = 'ws';
        let path = ':8080';
        if(window.location.protocol == 'https:') {
            ws += 's'
            path = '/wss';
        }

        let url = ws + '://' + window.location.hostname + path;
        this.ws = new WebSocket(url);
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
        console.log("a??");
        if(this.ws.readyState == this.ws.OPEN)
            this.ws.send(JSON.stringify({'type': type, 'data': data}));
        else
            alert("Erreur de connexions aux websockets");
    }
}