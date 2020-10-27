<?php
$user = array();
$user["EmailAddress"] = $_POST["email"];
$user["Password"] = $_POST["Password"];

// TODO: Extract $_POST variables, check they're OK, and attempt to create
// an account. Notify user of success/failure and redirect/give navigation 
// options.

$serverName = "databaseucl.database.windows.net";
$connectionOptions = array(
    "Database" => "databaseucl",
    "Uid" => "narcis",
    "PWD" => "P4ssword"
);

$conn = sqlsrv_connect($serverName, $connectionOptions)
or die('Error connecting to the server.' . sqlsrv_errors()['message']);

$query = "INSERT INTO Users (EmailAddress, Password)".
    "VALUES ('${user['EmailAddress']}', '${user['Password']}')";

$result = sqlsrv_query($conn, $query)
or die('Error making saveToDatabase query');
sqlsrv_close($conn);

if ($result==true)
    echo ("You have successfully registered")

?>