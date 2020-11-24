'use strict';

class SocketMessage
{
    constructor(onMessage) {
        let url = 'ws://' + window.location.hostname + ':8080';
        this.ws = new WebSocket(url);
        this.ws.addEventListener('message', event => {
            onMessage(JSON.parse(event.data));
        });
    }

    send(type, data) {
        this.ws.send(JSON.stringify({'type': type, 'data': data}));
    }
}