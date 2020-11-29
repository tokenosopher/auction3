<?php include_once("header.php")?>
<?php require("utilities.php")?>
<?php include_once 'db_con/db_li.php'?>


<?php
//// TODO: Extract $_POST variables, check they're OK, and attempt to create
////retrieving user info from post:
$user = array();
$user["EmailAddress"] = $_POST["email"];
$user["Password"] = $_POST["Password"];
$user["passwordConfirmation"] = $_POST["passwordConfirmation"];
$user["accountType"] = $_POST["accountType"];


///checking if email is a valid address (this is already checked using html for the form, but it check is there for
///http requrests:
if(!filter_var($user["EmailAddress"], FILTER_VALIDATE_EMAIL)) {
    exit('Invalid email address');
}

///checking if the email address is already in the database:
$params = array($user["EmailAddress"]);
$tsql = "SELECT EmailAddress FROM databaseucl.dbo.Users2
        WHERE EmailAddress = ?";
$cursorType = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
$select = sqlsrv_query($conn, $tsql, $params, $cursorType);

///Displays a splash screen if the email is already being used:
if (sqlsrv_has_rows($select)) {
    echo'<div class="header">
                  <h1>This email is already being used :( </h1>
                  <h2>Sorry! Try again</h2>
                  </div>';
    header("refresh:1.5;url=index.php");
}

//checks if the pass and pass confirmation are the same (also done in html in the header)
if ($user["Password"] != $user["passwordConfirmation"]) {
    exit("Password and Password confirmation did not match");
}

//hashes the password and inserts the email and pass in the database:
$user['hashed_pass'] = password_hash($user['Password'], PASSWORD_DEFAULT);
$query = "INSERT INTO Users2 (EmailAddress, Passwd)".
    "VALUES ('${user['EmailAddress']}', '${user['hashed_pass']}')";
$result = sqlsrv_query($conn, $query) or die('Error making saveToDatabase query');

//retrieving the user's id:
$query_ID = "SELECT UserID FROM databaseucl.dbo.Users2
        WHERE EmailAddress = ?";
$select_ID = sqlsrv_query($conn, $query_ID, $params);
WHILE ($row = sqlsrv_fetch_array($select_ID)) {
    $user["userID"] = $row["UserID"];
        }
//Adding that user ID to the buyer id, if they've selected buyer:
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

//else adding that user ID to the seller id, if they've selected seller:
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

//Inserting first name, if any:
if (isset($_POST['first_name'])) {
    $insert_first_name = "UPDATE databaseucl.dbo.Users2 
                        SET FirstName = '{$_POST['first_name']}' 
                        WHERE UserID = {$user["userID"]}";
    sqlsrv_query($conn, $insert_first_name);
}

//Inserting family name, if any:
if (isset($_POST['family_name'])) {
    $insert_family_name = "UPDATE databaseucl.dbo.Users2 
                        SET FamilyName = '{$_POST['family_name']}' 
                        WHERE UserID = {$user["userID"]}";
    sqlsrv_query($conn, $insert_family_name);
}

//setting session values:
if ($result==true) {
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
    echo'<div class="header">
                  <h1>You have successfuly registered! </h1>
                  <h2>You will be redirected in a second.</h2>
                  </div>';
    header("refresh:2;url=index.php");
}

?>

<?php include_once("footer.php")?>