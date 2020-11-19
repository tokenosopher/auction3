<?php include_once 'db_con/db_li.php'?>
<?php  require_once('PHPMailer/PHPMailerAutoload.php')?>

<?php

function outbidMail($item_id){
    $querystring = sprintf("
                        SELECT
                            DISTINCT TOP(1)  U.EmailAddress, bidValue,itemTitle
                        FROM 
                             Bids 
                        LEFT JOIN Buyers B on Bids.buyerId = B.buyerId
                        LEFT JOIN Users2 U on B.userId = U.UserID
                        LEFT JOIN AuctionItems AI on Bids.itemId = AI.itemId
                        WHERE Bids.itemId =%s and B.buyerId != %s 
                        ORDER BY bidValue DESC 
                        ", $item_id, $_SESSION['buyer_id']);

    global $conn;
    $emailBids = sqlsrv_query($conn, $querystring);
    while($row = sqlsrv_fetch_array($emailBids,SQLSRV_FETCH_ASSOC)) {
        $itemTitle = $row['itemTitle'];
        $bidValue = $row['bidValue'];
        $emailAddress = $row["EmailAddress"];
        $subject= sprintf('You have been outbid on the auction for %s',$itemTitle);
        $body = sprintf('Your bid of %s has been surpassed by another user. If you want to win this auction please bid higher!',$bidValue) ;
        sendEmail($emailAddress,$subject,$body);


    }
    sqlsrv_free_stmt($emailBids);

}



function sendEmail($emailAddress, $subject, $body){

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
        $mail -> Subject = $subject;
        $mail -> Body = $body;
        $mail -> AddAddress ($emailAddress);

        $mail -> Send();
    }


    ?>








