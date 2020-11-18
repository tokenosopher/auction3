<?php include_once('auction_functions.php') ?>
<?php include_once('db_con/db_li.php')?>
<?php include_once('email_functions.php')?>

<?php

$query = " SELECT itemTitle , itemId,  U.EmailAddress FROM AuctionItems left join Sellers S on AuctionItems.sellerId = S.sellerId left join Users2 U on S.userId = U.UserID where itemEndDate <= GETDATE() and itemEndDate > DATEADD( MINUTE, -1, GETDATE())  ";

global $conn;
$results = sqlsrv_query($conn, $query);

WHILE ($row = sqlsrv_fetch_array($results)) {
    $seller_email = $row["EmailAddress"];
    echo $seller_email;

    $item_id = $row['itemId'];
    echo $item_id;

    $item_title=$row['itemTitle'];
    echo $item_title;

    $current_winner = getcurrentwinninguser($item_id);
    echo $current_winner;

    $winner_email= $current_winner['EmailAddress'];
    echo $winner_email;

    $winning_bid=$current_winner['WinningBidAmt'];
    echo $winning_bid;

    $seller_subject= sprintf(' Your Auction for %s has ended ',$item_title);
    $seller_body = sprintf(
                            'Your auction for %s has ended, and the winner is %s . 
                            Your item sold for %s ',$item_title,$winner_email , $winning_bid
    );

    $buyer_subject = sprintf(" Congratulations, you have won your auction for %s",$item_title);
    $buyer_body = sprintf(" Your bid of %s has won this auction! 
                            Expect the seller %s to contact you soon. ",$winning_bid,$seller_email

    );

    sendEmail($seller_email,$seller_subject,$seller_body);
    sendEmail($winner_email,$buyer_subject,$buyer_body);
}

echo "working";
?>
