<?php include_once("header.php")?>
<?php require("utilities.php")?>
<?php include_once('db_con/db_li.php')?>
<?php include_once('auction_functions.php')?>

<div class="container">

<h2 class="my-3">My listings</h2>

<?php
  // This page is for showing a user the auction listings they've made.
  // It will be pretty similar to browse.php, except there is no search bar.
  // This can be started after browse.php is working with a database.
  // Feel free to extract out useful functions from browse.php and put them in
  // the shared "utilities.php" where they can be shared by multiple files.


  // TODO: Check user's credentials (cookie/session).
    if (isset($_SESSION['logged_in']) and $_SESSION['account_type'] == 'seller') {
        $user_id = $_SESSION['user_id'];
        $seller_id = $_SESSION['seller_id'];
        // TODO: Perform a query to pull up their auctions.

        $getResults = getmylistings();

        // TODO: Loop through results and print them out as list items.

        WHILE ($row = sqlsrv_fetch_array($getResults)) {

            $item_id = $row['itemId'];
            $title = $row['itemTitle'];
            $desc = $row['Description'];
            $end_time = $row['itemEndDate'];
            $price = $row['MaxBid'];
            $num_bids = $row['NoOfBids'];
            $starting_price = $row['itemStartingPrice'];
            $reserve_price = $row['itemReservePrice'];

            $auction_status = getauctionstatus($item_id);;

            print_my_listings_li($item_id, $title, $desc, $price, $num_bids, $end_time,$starting_price,$reserve_price,$auction_status);
        }
        sqlsrv_free_stmt($getResults);
        sqlsrv_close( $conn);

    }
?>
<?php include_once("footer.php")?>