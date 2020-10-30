<!DOCTYPE html>
<html lang="">
    <head>
        <title>test websocket</title>
        <meta name="csrf-token" content="{{ csrf_token() }}" />
    </head>

    <body>
        yooooo
        <script type="text/javascript" src="{{ asset('js/app.js') }}"></script>
        <script type="text/javascript" src="{{ URL::asset('js/testSocket.js') }}"></script>
    </body>
</html>