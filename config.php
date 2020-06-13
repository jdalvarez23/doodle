<?php

    // turn on output buffering
    ob_start();

    try {
        // set connection details
        $con = new PDO("mysql:dbname=doodle;host=localhost", "root", "");
        // show errors as warnings
        $con -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
    } catch(PDOExeption $e) {
        echo "Connection failed: " . $e -> getMessage();
    }
