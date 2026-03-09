<?php
session_start();
\n// DB Config
define('DB_HOST', 'localhost');
define('DB_NAME', 'dovehaven_farms');
define('DB_USER', 'dovehaven_farms');
define('DB_PASS', 'XVW4OWxmvcIg');\n\n// Connect to DB
function connectDB() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}