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
            echo '<section class="section"> ;
            <div class="centered">
            <h1><span>You are now logged in!</span></h1>
            <p><span>You will be redirected in a second</span></p>
            </div>';
            $_SESSION['user_id'] = $row['UserID'];
            $_SESSION['email'] = $row['EmailAddress'];
            $_SESSION['logged_in'] = true ;
            //querying to see if user is buyer or seller:
            $params_account_type = array($_SESSION['user_id']);
            $query_account_type_buyer = "SELECT userId, buyerId FROM Buyers
                                          WHERE userId = ?";
            $querying_account_type_buyer = sqlsrv_query($conn, $query_account_type_buyer, $params_account_type, $cursorType);
            if (sqlsrv_has_rows($querying_account_type_buyer))
            {
                $_SESSION['account_type'] = 'buyer';
                $row_buyer = sqlsrv_fetch_array($querying_account_type_buyer);
                $_SESSION['buyer_id'] = $row_buyer['buyerId'];
            } else {
                $_SESSION['account_type'] = 'seller';
                $query_account_type_seller = "SELECT userId, sellerId FROM Sellers
                                          WHERE userId = ?";
                $querying_account_type_seller = sqlsrv_query($conn, $query_account_type_seller, $params_account_type, $cursorType);
                $row_seller = sqlsrv_fetch_array($querying_account_type_seller);
                $_SESSION['seller_id'] = $row_seller['sellerId'];
                echo $_SESSION['seller_id'];
            }

//
//            $_SESSION['account_type'] = "seller";
            // Redirect to index after 5 seconds
//            header("refresh:5;url=index.php");

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