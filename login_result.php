<?php include_once("header.php")?>
<?php include_once 'db_con/db_li.php'?>


<?php

// TODO: Extract $_POST variables, check they're OK, and attempt to login.
// Notify user of success/failure and redirect/give navigation options.
// For now, I will just set session variables and redirect.
//$_SESSION['logged_in'] = true;
//$_SESSION['username'] = "test";

$user["email"] = $_POST["email"];
$user["password"] = $_POST["password"];

$params = array($user["email"]);
$tsql = "SELECT UserID, EmailAddress, Passwd FROM databaseucl.dbo.Users2
        WHERE EmailAddress = ?";
$cursorType = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
$select = sqlsrv_query($conn, $tsql, $params, $cursorType);
if (sqlsrv_num_rows($select) == 1){
        $row = sqlsrv_fetch_array($select);
        if (password_verify($user["password"], $row['Passwd'])) {
            echo "You've logged in! You will be redirected in a moment";
            $_SESSION['user_id'] = $row['UserID'];
            $_SESSION['email'] = $row['EmailAddress'];
            $_SESSION['logged_in'] = true ;
            //querying to see if user is buyer or seller:
            $params_account_type = array($_SESSION['user_id']);
            $query_account_type_buyer = "SELECT userID FROM Buyers
                                          WHERE userID = ?";
            $querying_account_type_buyer = sqlsrv_query($conn, $query_account_type_buyer, $params_account_type, $cursorType);
            if (sqlsrv_has_rows($querying_account_type_buyer))
            {
                $_SESSION['account_type'] = 'buyer';
            } else {
                $_SESSION['account_type'] = 'seller';
            }

//
//            $_SESSION['account_type'] = "seller";
            // Redirect to index after 5 seconds
            header("refresh:5;url=index.php");

        } else {
            echo "Invalid Password! Try again";
            // Redirect to index after 5 seconds
            header("refresh:5;url=index.php");
        }

} else {
    echo "Invalid username and password! Try again.";
}

//session_start();
//$_SESSION['account_type'] = "buyer";
////
//echo('<div class="text-center">You are now logged in! You will be redirected shortly.</div>');
//
//// Redirect to index after 5 seconds
//header("refresh:5;url=index.php");

?>