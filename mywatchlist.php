<?php include_once("header.php")?>
<?php require("utilities.php")?>
<?php include_once 'db_con/db_li.php'?>

    <div class="container">

    <h2 class="my-3">My WatchList</h2>

<?php
// This page is for showing a user the auction listings they've made.
// It will be pretty similar to browse.php, except there is no search bar.
// This can be started after browse.php is working with a database.
// Feel free to extract out useful functions from browse.php and put them in
// the shared "utilities.php" where they can be shared by multiple files.


// TODO: Check user's credentials (cookie/session).
$user_id=5;
$isbuyerstring = "SELECT TOP 1 BuyerID FROM Buyers WHERE Buyers.UserID =".$user_id;
$isbuyerresults = sqlsrv_query($conn, $isbuyerstring);
$buyer_id = sqlsrv_fetch_array($isbuyerresults)['BuyerID'];
sqlsrv_free_stmt($isbuyerresults);

$query = "SELECT AI.itemID, AI.ItemTitle, CAST(AI.ItemDescription AS VARCHAR(1000)) Description,AI.ItemEndDate, MAX(B.BidValue) MaxBid,COUNT(B.BidValue) NoOfBids
FROM AuctionItems AI
LEFT JOIN Bids B ON AI.itemID = B.itemID
WHERE AI.itemID IN (SELECT W.ItemID FROM WatchList W WHERE W.BuyerID = ".$buyer_id.")
GROUP BY AI.itemID, AI.ItemTitle, CAST(AI.ItemDescription AS VARCHAR(1000)), AI.ItemEndDate;";

$getResults= sqlsrv_query($conn, $query);

WHILE ($row = sqlsrv_fetch_array($getResults)) {

    $item_id = $row['itemID'];
    $title = $row['ItemTitle'];
    $desc = $row['Description'];
    $end_time = $row['ItemEndDate'];
    $price = $row['MaxBid'];
    $num_bids = $row['NoOfBids'];

    print_listing_li($item_id, $title, $desc, $price, $num_bids, $end_time);
}

sqlsrv_free_stmt($getResults);

?>
<?php include_once("footer.php")?>