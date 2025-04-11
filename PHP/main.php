<?php

include 'dbConnection.php';

echo "DB Connected Successfully!";


$sql = "SELECT * FROM your_table";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "Data: " . $row["column_name"] . "<br>";
    }
} else {
    echo "No data found.";
}

$conn->close();

?>