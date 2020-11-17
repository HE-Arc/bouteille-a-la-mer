const { default: Echo } = require("laravel-echo");

Echo.channel('home')
    .listen('NewMessage', (e) => {
        console.log(e);
    });


console.log("yo")