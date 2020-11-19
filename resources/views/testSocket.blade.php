
<?php
use Illuminate\Support\Facades\DB;

$users = DB::select('select * from users');
var_dump($users);

?>
yoooooooo

<script>
var conn = new WebSocket('ws://localhost:8080');
conn.onopen = function(e) {
    console.log("Connection established!");
};

conn.onmessage = function(e) {
    console.log(e.data);
};

</script>