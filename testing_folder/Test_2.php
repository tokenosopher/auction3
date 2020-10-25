<?php
$serverName = "databaseucl.database.windows.net";
$connectionOptions = array(
    "Database" => "databaseucl",
    "Uid" => "narcis",
    "PWD" => "P4ssword"
);
//Establishes the connection
$conn = sqlsrv_connect($serverName, $connectionOptions);
$tsql= "SELECT *
        FROM databaseucl.dbo.Users";
$getResults= sqlsrv_query($conn, $tsql);
echo nl2br("Reading data from table" . PHP_EOL);
if ($getResults == FALSE)
    echo (sqlsrv_errors());
while ($row = sqlsrv_fetch_array($getResults, SQLSRV_FETCH_ASSOC)) {
    echo nl2br($row['FirstName'] . " " . $row['FamilyName'] . PHP_EOL);
}
sqlsrv_free_stmt($getResults);
?>