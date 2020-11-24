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

    send(text, image) {
        this.ws.send(JSON.stringify({'text': text, 'image': image}))
    }

    toBase64(image) {
        //TODO
        return null;
    }
}