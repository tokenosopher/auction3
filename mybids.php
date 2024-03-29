<?php include_once("header.php")?>
<?php require("utilities.php")?>
<?php include_once('auction_functions.php')?>

<div class="container">

<h2 class="my-3">My bids</h2>

<?php
  // This page is for showing a user the auctions they've bid on.
  // It will be pretty similar to browse.php, except there is no search bar.
  // This can be started after browse.php is working with a database.
  
  
  // Checks user's credentials (cookie/session).
if (isset($_SESSION['logged_in']) and $_SESSION['account_type'] == 'buyer') {
    $user_id = $_SESSION['user_id'];
    $buyer_id = $_SESSION['buyer_id'];

    // Performs a query to pull up the auctions user has bidded on.
    $getResults = getmybids();
    $empty = true;

    // Loops through results and prints them out as list items.
    WHILE ($row = sqlsrv_fetch_array($getResults)) {

        $item_id = $row['itemId'];
        $title = $row['itemTitle'];
        $desc = $row['Description'];
        $end_time = $row['itemEndDate'];
        $price = $row['MaxBid'];
        $num_bids = $row['NoOfBids'];
        $usermaxbid = $row['UserMaxBid'];

        $auction_status = getauctionstatus($item_id);
        $empty = false;

        print_bids_li($item_id, $title, $desc, $price, $num_bids, $end_time,$usermaxbid,$auction_status);
    }
    sqlsrv_free_stmt($getResults);
    sqlsrv_close($conn);

    // Default message if user hasn't bidded on anything
    if($empty){
        echo "You haven't made any bids! Start bidding now!";
    }
}
?>

<?php include_once("footer.php")?>