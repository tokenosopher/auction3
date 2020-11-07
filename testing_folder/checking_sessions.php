

<?php


session_start();
// This are the session variables coming from logging in (login_result.php)
// and from registration (process_registration.php):
echo ( $_SESSION['user_id'])."<br>";
echo ($_SESSION['email'])."<br>";
echo ($_SESSION['logged_in'])."<br>";
echo ($_SESSION['account_type'])."<br>";

// you can use seller@test.com - password 123 or buyer@test.com
// to try either accounts


?>