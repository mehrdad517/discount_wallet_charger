<?php

$file = fopen('output.txt', 'a');

$conn = mysqli_connect('localhost', 'root', '', 'arvan');

// Check connection
if (!$conn) {
    fwrite($file, "Connection failed: " . mysqli_connect_error());
}

$sql = "CALL discount_handle_user_finance('$argv[1]', '$argv[2]')";
$result = $conn->query($sql);

while($row = $result->fetch_array()) {
    fwrite($file, time() . " result for $argv[1]:" . json_encode($row) . PHP_EOL);
}


fclose($file);