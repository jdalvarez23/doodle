<?php
include('../config.php');

// check if POST request has variable linkId
if (isset($_POST['imageUrl'])) {

    // prepare sql statement
    $query = $con -> prepare("UPDATE images SET clicks = clicks + 1 WHERE imageUrl = :imageUrl");

    // bind parameter
    $query -> bindParam(":imageUrl", $_POST['imageUrl']);

    // execute query
    $query -> execute();

} else {
    echo "No image passed to page.";
}