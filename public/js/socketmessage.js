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

    //Type => 'image' or 'text'
    send(type, content) {

        if(type == 'image') {
            content = this.toBase64(content);
        }

        this.ws.send(messageString)
    }

    toBase64(image)
    {
        //TODO
        return null;
    }
}