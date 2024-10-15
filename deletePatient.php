<?php
    include "dbConnect.php";

    if (isset($_GET['id'])) {
        $id = $_GET['id'];

        $sql = "DELETE FROM patient WHERE id = $id";

        // Execute the query
        if (mysqli_query($conn, $sql)) {
            echo "Record deleted successfully";
        } else {
            echo "Error deleting record: " . mysqli_error($conn);
        }
        header("Location: patients.php");

        exit();
    } else {
        echo "Invalid request";
    }

?>
