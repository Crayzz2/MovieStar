<?php
    session_start();

    $host = 'localhost';
    $user = 'root';
    $pass = '';
    $dbname = 'moviestar';

    $conn = new PDO("mysql:dbname=$dbname;host=$host", $user, $pass);

    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);