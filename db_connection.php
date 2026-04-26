<?php
// Database connection for ElectronicsStore
function OpenCon() {
    $dbhost = "localhost";
    $dbuser = "root";
    $dbpass = "";
    $dbname = "electronicsstore";

    $conn = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
    if ($conn->connect_error) {
        die("Database connection failed.");
    }
    $conn->set_charset("utf8mb4");
    return $conn;
}

function CloseCon($conn) {
    if ($conn) {
        $conn->close();
    }
}
?>
