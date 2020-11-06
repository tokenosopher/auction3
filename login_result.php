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
$user["hashed_pass"] = password_hash($user["password"], PASSWORD_DEFAULT);
echo $user["email"];
echo $user["password"];
echo $user["hashed_pass"];

$params = array($user["email"], $user["hashed_pass"]);
$tsql = "SELECT UserID, EmailAddress, Passwd FROM databaseucl.dbo.Users2
        WHERE EmailAddress = ?";
$cursorType = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
$select = sqlsrv_query($conn, $tsql, $params, $cursorType);
if (sqlsrv_num_rows($select) == 1){
        $row = sqlsrv_fetch_array($select);
        if (password_verify($user["password"], PASSWORD_DEFAULT)) {
            echo "Password is valid";
        } else {
            echo "Username or password are not valid";
        }

//    session_start();
//    $_SESSION['user_id'] = $row['userID'];
//    $_SESSION['email'] = $row['EmailAddress'];
//    $_SESSION['logged_in'] = true;
//    $_SESSION['account_type'] = "buyer";
    echo ("Login_correct");
} else {
    echo "Invalid username and password, try again.";
}

//session_start();
//$_SESSION['account_type'] = "buyer";
////
//echo('<div class="text-center">You are now logged in! You will be redirected shortly.</div>');
//
//// Redirect to index after 5 seconds
//header("refresh:5;url=index.php");

?>