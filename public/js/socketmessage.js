'use strict';

class SocketMessage
{
    constructor(onMessage) {
        let ws = 'ws';
        if(window.location.protocol == 'https:')
            ws += 's'

        let url = ws + '://' + window.location.hostname;
        this.ws = new WebSocket(url);
        console.log("connecting...");
        this.ws.addEventListener('message', event => {
            onMessage(JSON.parse(event.data));
        });
    }

    send(type, data) {
        this.ws.send(JSON.stringify({'type': type, 'data': data}));
    }
}