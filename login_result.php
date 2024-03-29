<?php include_once("header.php")?>
<?php include_once 'db_con/db_li.php'?>


<?php

// TODO: Extract $_POST variables, check they're OK, and attempt to login.
// Notify user of success/failure and redirect/give navigation options.
// For now, I will just set session variables and redirect.
//$_SESSION['logged_in'] = true;
//$_SESSION['username'] = "test";

//extracting post variables:
$user["email"] = $_POST["email"];
$user["password"] = $_POST["password"];

//querying database for the email to see if the username exists and retrieve details:
$params = array($user["email"]);
$tsql = "SELECT UserID, EmailAddress, Passwd FROM databaseucl.dbo.Users2
        WHERE EmailAddress = ?";
$cursorType = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
$select = sqlsrv_query($conn, $tsql, $params, $cursorType);

//if the user exists, verify the password using the password_verify function:
if (sqlsrv_num_rows($select) == 1){
        $row = sqlsrv_fetch_array($select);
        //if the password is correct, display splash screen and add the relevant user info into the session:
        if (password_verify($user["password"], $row['Passwd'])) {
            echo '<div class="header">
                  <h1>You are now logged in!</h1>
                  <h2>You will be redirected in a second.</h2>
                  </div>';
            $_SESSION['user_id'] = $row['UserID'];
            $_SESSION['email'] = $row['EmailAddress'];
            $_SESSION['logged_in'] = true ;
            //querying to see if user is buyer or seller:
            $params_account_type = array($_SESSION['user_id']);
            $query_account_type_buyer = "SELECT userId, buyerId FROM Buyers
                                          WHERE userId = ?";
            $querying_account_type_buyer = sqlsrv_query($conn, $query_account_type_buyer, $params_account_type, $cursorType);
            //if user is buyer, retrieve the buyer id. Else, retrieve the seller id.
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
                sqlsrv_free_stmt($querying_account_type_seller);
            }
            // Redirect to index after 1.5 seconds
            header("refresh:1.5;url=index.php");
            //freeing the $querying_account_type_buyer resource
            sqlsrv_free_stmt($querying_account_type_buyer);
        //if username valid but password invalid, display splash screen and redirect to index:
        } else {
            echo '<div class="header">
                  <h1>Invalid password! :( </h1>
                  <h2>Try again</h2>
                  </div>';
            // Redirect to index after 1.5 seconds
            header("refresh:1.5;url=index.php");
        }
//if username invalid, display splash screen and redirect to index:
} else {
    echo '<div class="header">
                  <h1>Invalid username and password! :( </h1>
                  <h2>Try again</h2>
                  </div>';
    // Redirect to index after 1.5 seconds
    header("refresh:1.5;url=index.php");
}
//freeing the $select resource:
sqlsrv_free_stmt($select);

?>