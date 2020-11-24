<?php include_once("header.php")?>
<?php require("utilities.php")?>
<?php include_once 'db_con/db_li.php'?>
<?php include_once('auction_functions.php')?>

    <div class="container">

    <h2 class="my-3">My Listings</h2>

    <div class="container mt-5">

    <ul class="list-group">

<?php
// This page is for showing a user the auction listings they've made.
// It will be pretty similar to browse.php, except there is no search bar.
// This can be started after browse.php is working with a database.
// Feel free to extract out useful functions from browse.php and put them in
// the shared "utilities.php" where they can be shared by multiple files.


    // Checks user's credentials (cookie/session).
    if (isset($_SESSION['logged_in']) and $_SESSION['account_type'] == 'seller'){
    $user_id = $_SESSION['user_id'];
    $seller_id = $_SESSION['seller_id'];

    //query to extract all watched items.
    $query = "SELECT AI.itemId, AI.itemTitle, CAST(AI.itemDescription AS VARCHAR(1000)) Description, AI.itemEndDate,
    AI.itemStartingPrice, AI.itemReservePrice, MAX(B.bidValue) MaxBid, COUNT(B.bidValue) NoOfBids
    FROM AuctionItems AI
    LEFT JOIN Bids B ON AI.itemID = B.itemID
    WHERE AI.sellerID = {$seller_id}
    GROUP BY AI.itemId, AI.itemTitle, CAST(AI.itemDescription AS VARCHAR(1000)), AI.itemEndDate, 
    AI.itemStartingPrice, AI.itemReservePrice ORDER BY itemEndDate DESC";



    //Default message displayed if no items are watched
    $numb_of_listings_query = sqlsrv_query($conn, $query, array(), array("Scrollable" => SQLSRV_CURSOR_KEYSET));
    if(sqlsrv_num_rows($numb_of_listings_query) == 0){
        echo '<div class="text-center">You have no listings! <a href="create_auction.php">Create one!</a></div>';}

    $getResults= sqlsrv_query($conn, $query);

    //loops through results and prints them out
    while ($row = sqlsrv_fetch_array($getResults)){
        $item_id = $row['itemId'];
        $title = $row['itemTitle'];
        $desc = $row['Description'];
        $end_time = $row['itemEndDate'];
        $price = $row['MaxBid'];
        $num_bids = $row['NoOfBids'];
        $starting_price = $row['itemStartingPrice'];
        $reserve_price = $row['itemReservePrice'];
        $auction_status = getauctionstatus($item_id);

        print_my_listings_li($item_id, $title, $desc, $price, $num_bids, $end_time, $starting_price, $reserve_price, $auction_status);
    }

    sqlsrv_free_stmt($getResults);
    sqlsrv_close($conn);
}
//    At this stage the session is closed and bellow are messages that the buyer will see if they try to access this page

elseif(isset($_SESSION['logged_in']) and $_SESSION['account_type'] == 'buyer'){
    echo "Only sellers accounts will have My Listings page, you are a buyer account.";
}
else{
    echo "You must login to your seller account in order to see your listings!";
}

?>
<?php include_once("footer.php")?>