

<?php


session_start();
// This are the session variables coming from logging in (login_result.php)
// and from registration (process_registration.php):
echo nl2br("user id is {$_SESSION['user_id']} \n");
echo nl2br("email is {$_SESSION['email']} \n");
echo nl2br("the user is logged in: {$_SESSION['logged_in']} \n");
echo nl2br("account type is: {$_SESSION['account_type']} \n");
if (isset($_SESSION['buyer_id'])) {
echo "buyer id is {$_SESSION['buyer_id']}";
}
else {
    echo "seller id is {$_SESSION['seller_id']}";
}
// you can use seller@test.com - password 123 or buyer@test.com
// to try either accounts


?>