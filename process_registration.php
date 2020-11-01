<?php
$user = array();
$user["EmailAddress"] = $_POST["email"];
$user["Password"] = $_POST["Password"];
$user["passwordConfirmation"] = $_POST["passwordConfirmation"];

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

if(!filter_var($user["EmailAddress"], FILTER_VALIDATE_EMAIL)) {
    exit('Invalid email address');
}
//$sel2= "SELECT FROM Users (EmailAddress)."
//    "WHERE VALUES ('${user['EmailAddress']}"
//query2 = "SELECT EmailAddress FROM databaseucl.dbo.Users WHERE EmailAddress = $user["EmailAddress"]"

$params = array($user["EmailAddress"]);
$tsql = "SELECT EmailAddress FROM databaseucl.dbo.Users
        WHERE EmailAddress = ?";
$cursorType = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
$select = sqlsrv_query($conn, $tsql, $params, $cursorType);

if (sqlsrv_has_rows($select)) {
    exit("This email is already being used");
}

if ($user["Password"] != $user["passwordConfirmation"]) {
    exit("Password and Password confirmation did not match");
}
$user['hashed_pass'] = password_hash($user['Password'], PASSWORD_DEFAULT);

$query = "INSERT INTO Users (EmailAddress, Password)".
    "VALUES ('${user['EmailAddress']}', '${user['hashed_pass']}')";

$result = sqlsrv_query($conn, $query)
or die('Error making saveToDatabase query');
sqlsrv_close($conn);

if ($result==true)
    echo ("You have successfully registered")

?>