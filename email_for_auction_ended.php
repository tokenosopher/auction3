<?php include_once('auction_functions.php') ?>
<?php include_once('db_con/db_li.php')?>
<?php include_once('email_functions.php')?>

<?php

$query = " SELECT itemTitle , itemId,  U.EmailAddress, itemReservePrice FROM AuctionItems left join Sellers S on AuctionItems.sellerId = S.sellerId left join Users2 U on S.userId = U.UserID where itemEndDate <= GETDATE() and itemEndDate > DATEADD( MINUTE, -1, GETDATE())  ";

global $conn;
$results = sqlsrv_query($conn, $query);

WHILE ($row = sqlsrv_fetch_array($results)) {
    $seller_email = $row["EmailAddress"];
    $item_id = $row['itemId'];
    $item_title = $row['itemTitle'];
    $reserve_price = $row['itemReservePrice'];
    $current_winner = getcurrentwinninguser($item_id);
    $winner_email= $current_winner['EmailAddress'];
    $winning_bid=$current_winner['WinningBidAmt'];
    $seller_subject= sprintf(' Your Auction for %s has ended ',$item_title);

    if(isset($winner_email)){
        if($winning_bid >= $reserve_price){
            $seller_body = sprintf(
                'Your auction for %s has ended, and the winner is %s . 
                            Your item sold for %s ',$item_title,$winner_email , $winning_bid
            );
            $buyer_subject = sprintf(" Congratulations, you have won your auction for %s",$item_title);
            $buyer_body = sprintf(" Your bid of %s has won this auction! 
                            Expect the seller %s to contact you soon. ",$winning_bid,$seller_email);
            sendEmail($seller_email,$seller_subject,$seller_body);
            sendEmail($winner_email,$buyer_subject,$buyer_body);
        }
        else{
            $seller_body = sprintf(
                'Your auction for %s has ended, there were bids, 
                however none met your reserve price of £%s.',$item_title, $reserve_price);
            sendEmail($seller_email,$seller_subject,$seller_body);
        }
    }
    else{
        $seller_body = sprintf(
            'Your auction for %s has ended, there were no bids.',$item_title);
        sendEmail($seller_email,$seller_subject,$seller_body);
    }
}
?>
