<?php include_once("header.php")?>
<?php include_once 'db_con/db_li.php'?>
<?php include('auction_functions.php') ?>
<?php  require_once('PHPMailer/PHPMailerAutoload.php')?>

<?php

$query_imran = " SELECT distinct(EmailAddress) from Users2 U2, Buyers Bu, Bids Bi where Bu.userId=U2.UserID and Bi.buyerId=Bu.buyerId and Bi.itemId=130";

$GetResult = sqlsrv_query($conn, $query_imran);

while($row = sqlsrv_fetch_array($GetResult,SQLSRV_FETCH_ASSOC)) {
    echo $row['EmailAddress']."<br>"; // Print a single column data
          // Print the entire row data

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
    $mail -> Subject = " You have been outbid! ";
    $mail -> Body = ' Dear Customer, We are emailing to notify you that your bid for item ( Get Auction Title) has been outbid ';
    $mail -> AddAddress ($row['EmailAddress']);

    $mail -> Send();

    echo 'Email sent successfully';
}

echo 'Hello World';
?>

//TODO: Make sure the query includes all emails from users which are different from the latest bidder.
//TODO:Make sure the email contains all the information about the auction and is properly formatted.


<?php include_once('footer.php')?>
