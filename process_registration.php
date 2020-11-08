<?php include_once("header.php")?>
<?php require("utilities.php")?>
<?php include_once 'db_con/db_li.php'?>

<?php
$user = array();
$user["EmailAddress"] = $_POST["email"];
$user["Password"] = $_POST["Password"];
$user["passwordConfirmation"] = $_POST["passwordConfirmation"];
$user["accountType"] = $_POST["accountType"];

//
//// TODO: Extract $_POST variables, check they're OK, and attempt to create
//// an account. Notify user of success/failure and redirect/give navigation
//// options.
//
//$serverName = "databaseucl.database.windows.net";
//$connectionOptions = array(
//    "Database" => "databaseucl",
//    "Uid" => "narcis",
//    "PWD" => "P4ssword"
//);
//
//$conn = sqlsrv_connect($serverName, $connectionOptions)
//or die('Error connecting to the server.' . sqlsrv_errors()['message']);

if(!filter_var($user["EmailAddress"], FILTER_VALIDATE_EMAIL)) {
    exit('Invalid email address');
}
//$sel2= "SELECT FROM Users (EmailAddress)."
//    "WHERE VALUES ('${user['EmailAddress']}"
//query2 = "SELECT EmailAddress FROM databaseucl.dbo.Users WHERE EmailAddress = $user["EmailAddress"]"

$params = array($user["EmailAddress"]);
$tsql = "SELECT EmailAddress FROM databaseucl.dbo.Users2
        WHERE EmailAddress = ?";
$cursorType = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
// $conn is derived from db_li php that is included at the beginning of the file
$select = sqlsrv_query($conn, $tsql, $params, $cursorType);

if (sqlsrv_has_rows($select)) {
    exit("This email is already being used");
}

if ($user["Password"] != $user["passwordConfirmation"]) {
    exit("Password and Password confirmation did not match");
}
$user['hashed_pass'] = password_hash($user['Password'], PASSWORD_DEFAULT);

$query = "INSERT INTO Users2 (EmailAddress, Passwd)".
    "VALUES ('${user['EmailAddress']}', '${user['hashed_pass']}')";

$result = sqlsrv_query($conn, $query) or die('Error making saveToDatabase query');

//Querying for the userID below, then adding that user ID to the buyer or seller table
$query_ID = "SELECT UserID FROM databaseucl.dbo.Users2
        WHERE EmailAddress = ?";

$select_ID = sqlsrv_query($conn, $query_ID, $params);

WHILE ($row = sqlsrv_fetch_array($select_ID)) {
    $user["userID"] = $row["UserID"];
        }

if ($user["accountType"] == "buyer") {
    $insert_buyer = "INSERT INTO Buyers (UserID)".
        "VALUES ('${user['userID']}')";
    sqlsrv_query($conn, $insert_buyer);
    //retrieving user's buyer id below:
    $params_account_type = array($user['userID']);
    $query_account_type_buyer = "SELECT userId, buyerId FROM Buyers
                                          WHERE userId = ?";
    $querying_account_type_buyer = sqlsrv_query($conn, $query_account_type_buyer, $params_account_type);
    WHILE ($row = sqlsrv_fetch_array($querying_account_type_buyer)) {
        $user["buyer_id"] = $row["buyerId"];
    }
}

else if ($user["accountType"] == "seller") {
    $insert_seller = "INSERT INTO Sellers (UserID)".
        "VALUES ('${user['userID']}')";
    sqlsrv_query($conn, $insert_seller);
    //retrieving user's seller id below:
    $params_account_type = array($user['userID']);
    $query_account_type_seller = "SELECT userId, sellerId FROM Sellers
                                          WHERE userId = ?";
    $querying_account_type_seller = sqlsrv_query($conn, $query_account_type_seller, $params_account_type);
    WHILE ($row = sqlsrv_fetch_array($querying_account_type_seller)) {
        $user["seller_id"] = $row["sellerId"];
    }
}


if ($result==true) {
//    session_start();
    $_SESSION['email'] = $user["EmailAddress"];
    $_SESSION['account_type'] = $user["accountType"];
    $_SESSION['user_id'] = $user["userID"];
    $_SESSION['logged_in'] = true ;
    if (isset($user['buyer_id'])) {
        $_SESSION['buyer_id'] = $user['buyer_id'];
    }
    else {
        $_SESSION['seller_id'] = $user['seller_id'];
    }
    echo("You have successfully registered");
    header("refresh:2;url=index.php");
}

?>

<?php include_once("footer.php")?>
