<?php include_once 'db_con/db_li.php'?>
<?php
require_once('PHPMailer/PHPMailerAutoload.php');


$mail = new PHPMailer();
$mail -> isSMTP();
$mail -> SMTPAuth= true;
$mail -> SMTPSecure = 'ssl';
$mail -> Host = 'smtp.gmail.com';
$mail -> Port = '465';
$mail -> isHTML();
$mail -> Username = 'db.groupproject9@gmail.com';
$mail -> Password = 'DreamTeam1';
$mail -> SetFrom( 'db.groupproject9@gmail.com');
$mail -> Subject = " Checking that this email works ";
$mail -> Body = ' Hi, this is test email ';
$mail -> AddAddress ('ihossain.falcone@gmail.com');

$mail -> Send();

echo 'Email sent successfully';




?>