<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "";

try {
    $conn = new mysqli($servername, $username, $password, $dbname);
    // check for connection errors
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    // echo "Connected successfully"; 
} catch (Exception $e) {
    echo "Connection failed: " . $e->getMessage();
}
