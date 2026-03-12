<?php
function getDBConnection()
{
    $servername = "localhost";
    $username   = "root";
    $password   = "";
    $dbname     = "cinemax";

    try {
        $conn = new mysqli($servername, $username, $password, $dbname);

        if ($conn->connect_error) {
            throw new Exception("Connection failed: " . $conn->connect_error);
        }

        // Set charset để tránh lỗi tiếng Việt
        $conn->set_charset("utf8mb4");

        return $conn;
    } catch (Exception $e) {
        // Trong môi trường thật nên log thay vì echo
        die("Database connection error: " . $e->getMessage());
    }
}
