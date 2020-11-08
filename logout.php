<?php include_once("header.php")?>

<?php

unset($_SESSION['logged_in']);
unset($_SESSION['account_type']);
setcookie(session_name(), "", time() - 360);
session_destroy();
echo    '<div class="header">
         <h1>You have now logged out!</h1>
         <h2>Hope to see you again soon</h2>
         </div>';

// Redirect to index
header("refresh:2;url=index.php");

?>