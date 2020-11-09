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
if (isset($_SESSION['logged_in']) and $_SESSION['account_type'] == 'buyer'){
    $user_id = $_SESSION['user_id'];
    $buyer_id = $_SESSION['buyer_id'];

    $query = sprintf("
                SELECT 
                       AI.itemID, 
                       AI.ItemTitle, 
                       CAST(AI.ItemDescription AS VARCHAR(1000)) Description,
                       AI.ItemEndDate,
                       AI.itemStartingPrice,
                       MAX(B.BidValue) MaxBid,
                       COUNT(B.BidValue) NoOfBids
                FROM 
                     AuctionItems AI
                LEFT JOIN Bids B ON AI.itemID = B.itemID
                WHERE 
                      AI.itemID IN (SELECT W.ItemID FROM WatchList W WHERE W.BuyerID = %s)
                GROUP BY 
                    AI.itemID, 
                    AI.ItemTitle, 
                    AI.itemStartingPrice,     
                    CAST(AI.ItemDescription AS VARCHAR(1000)),
                    AI.ItemEndDate;", $buyer_id);

    $getResults= sqlsrv_query($conn, $query);

    WHILE ($row = sqlsrv_fetch_array($getResults)) {

        $item_id = $row['itemID'];
        $title = $row['ItemTitle'];
        $desc = $row['Description'];
        $end_time = $row['ItemEndDate'];
        $price = $row['MaxBid'];
        $num_bids = $row['NoOfBids'];
        $start_price = $row['itemStartingPrice'];

        print_listing_li($item_id, $title, $desc, $price, $num_bids, $end_time, $start_price);
    }

    sqlsrv_free_stmt($getResults);
}
elseif(isset($_SESSION['logged_in']) and $_SESSION['account_type'] == 'seller'){
    echo "Only buyer accounts will have watchlists, you are a seller account.";
}
else{
    echo "You must login to your buyer account in order to see your watchlist";
}
?>
<?php include_once("footer.php")?>