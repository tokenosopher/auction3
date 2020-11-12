<?php include_once("header.php")?>
<?php require("utilities.php")?>
<?php include_once 'db_con/db_li.php'?>

<div class="container">

<h2 class="my-3">My listings</h2>

<?php
  // This page is for showing a user the auction listings they've made.
  // It will be pretty similar to browse.php, except there is no search bar.
  // This can be started after browse.php is working with a database.
  // Feel free to extract out useful functions from browse.php and put them in
  // the shared "utilities.php" where they can be shared by multiple files.


  // TODO: Check user's credentials (cookie/session).
  
  // TODO: Perform a query to pull up their auctions.
$query = "SELECT AI.itemId, AI.itemTitle, CAST(AI.itemDescription AS VARCHAR(1000)) Description,
AI.itemEndDate, MAX(B.bidValue) MaxBid,COUNT(B.bidValue) NoOfBids
FROM AuctionItems AI
LEFT JOIN Bids B ON AI.itemID = B.itemID
WHERE AI.sellerID = 2
GROUP BY AI.itemID, AI.itemTitle, CAST(AI.itemDescription AS VARCHAR(1000)), AI.itemEndDate;";
$getResults= sqlsrv_query($conn, $query);

  // TODO: Loop through results and print them out as list items.

WHILE ($row = sqlsrv_fetch_array($getResults)) {

    $item_id = $row['itemId'];
    $title = $row['itemTitle'];
    $desc = $row['Description'];
    $end_time = $row['itemEndDate'];
    $price = $row['MaxBid'];
    $num_bids = $row['NoOfBids'];

    print_listing_li($item_id, $title, $desc, $price, $num_bids, $end_time);
}
sqlsrv_free_stmt($getResults);
sqlsrv_close( $conn);
?>
<?php include_once("footer.php")?>