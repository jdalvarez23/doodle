<?php
include('../config.php');

// check if POST request has variable linkId
if (isset($_POST['linkId'])) {

    // prepare sql statement
    $query = $con -> prepare("UPDATE sites SET clicks = clicks + 1 WHERE id = :id");

    // bind parameter
    $query -> bindParam(":id", $_POST['linkId']);

    // execute query
    $query -> execute();

} else {
    echo "No link passed to page.";
}