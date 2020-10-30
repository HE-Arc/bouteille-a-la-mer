Echo.channel('home')
    .listen('NewMessage', (e) => {
        console.log(e);
    });
console.log("yo")