<?php
$serverName = "databaseucl.database.windows.net";
$connectionOptions = array(
    "Database" => "databaseucl",
    "Uid" => "narcis",
    "PWD" => "P4ssword"
);
//Establishes the connection
$conn = sqlsrv_connect($serverName, $connectionOptions)
    or die("Error connecting to the Azure DB" . sqlsrv_errors());
echo("Connection to Azure DB successful!");