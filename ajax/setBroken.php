<?php
include('../config.php');

// check if POST request has variable src
if (isset($_POST['src'])) {

    // prepare sql statement
    $query = $con -> prepare("UPDATE images SET broken = 1 WHERE imageUrl = :src");

    // bind parameter
    $query -> bindParam(":src", $_POST['src']);

    // execute query
    $query -> execute();

} else {
    echo "No src passed to page.";
}