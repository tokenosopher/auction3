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
//echo("Connection to Azure DB successful!");
/*  How to use this:
    On top of the page paste this:

    <?php include_once 'db_con/db_li.php'?>

    What it does is essentially import the file and allows you
    to use the $conn variable to connect to the database.

    When you want to get data from the DB on webpage of your choice,
    you can run:

    sqlsrv_query($conn, $query);

    Where $query is the query statement you had written in SQL.

*/



