<?php include_once("header.php")?>
<?php require("utilities.php")?>
<?php include_once 'db_con/db_li.php'?>

<div class="container">

<h2 class="my-3">Top recommendations for you based on your previous bids:</h2>
    <h4 class="my-5"></h4>

<?php
  // This page is for showing a buyer recommended items based on their bid 
  // history. It will be pretty similar to browse.php, except there is no 
  // search bar. This can be started after browse.php is working with a database.
  // Feel free to extract out useful functions from browse.php and put them in
//  // the shared "utilities.php" where they can be shared by multiple files.
// TODO: Check user's credentials (cookie/session).
if (isset($_SESSION['user_id'])) {
    // TODO: Perform a query to pull up auctions they might be interested in.

    //retrieving top 3 categories that the user bid on:
    $retrieve_buyer_cat = "select top 3 Bids.buyerId, count(Bids.itemId) as nr_bids_on_category, AI.categoryId
                        into #retrievebids
                        from Bids
                            inner join AuctionItems AI on AI.itemId = Bids.itemId
                        where buyerId = {$_SESSION['buyer_id']}
                        group by AI.categoryId, Bids.buyerId
                        ORDER BY nr_bids_on_category DESC";
    sqlsrv_query($conn, $retrieve_buyer_cat);
    $query_for_empty_rows = "Select * from #retrievebids";
    $stmt = sqlsrv_query($conn, $query_for_empty_rows);
    if (!sqlsrv_has_rows( $stmt )) {
        echo "<br>We need to get to know you better in order to make recommendations. <br />";
        echo "<br>We will add some once you bid more.</br>";
        sqlsrv_free_stmt($stmt);
        die();
}

//    getting a visual of the retrieve_bids results (not needed for the final query):
//    $retrieve_table_query = "SELECT *
//                                FROM #retrievebids";
//    $getResults= sqlsrv_query($conn, $retrieve_table_query);
//    echo nl2br("Reading data from table" . PHP_EOL);
//    if ($getResults == FALSE)
//        echo ("You need to make a bid to get recommendations");
//    while ($row = sqlsrv_fetch_array($getResults, SQLSRV_FETCH_ASSOC)) {
//        echo nl2br($row['buyerId'] . " " . $row['nr_bids_on_category'] . " " . $row['categoryId']. PHP_EOL);
//        }

    //selecting the top 3 buyers that have bid on the top 3 categories of the user:
    $retrieve_similar_buyers ="select top 3 Bids.buyerId, count(Bids.itemId) as nr_bids_on_category, AI.categoryId
                        into #top_buyers
                        from Bids
                                 inner join AuctionItems AI on AI.itemId = Bids.itemId
                            where categoryId in (select categoryId from #retrievebids)
                          AND buyerId != {$_SESSION['buyer_id']}
                        group by buyerId, categoryId
                        ORDER BY nr_bids_on_category DESC";
    sqlsrv_query($conn, $retrieve_similar_buyers);

    //selecting the top 10 bids (by number of bids) that others have bid on and that the og buyer didn't:
    $retrieve_top_bids = "select top 10 count(Bids.itemId) as num_bids_per_id, Bids.itemId
                            into #top_bids
                            from Bids
                                     inner join AuctionItems AI on AI.itemId = Bids.itemId
                            where Bids.buyerId in (select buyerId from #top_buyers)
                              and Bids.itemId not in (select Bids.itemId from Bids where Bids.buyerId = {$_SESSION['buyer_id']})
                              and AI.itemEndDate > getdate()
                            group by Bids.itemId
                            ORDER BY num_bids_per_id DESC";
    sqlsrv_query($conn, $retrieve_top_bids);

    //printing the results on the page based on the retrieve top bids
    $print_bids = "SELECT 
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
                  AI.itemID IN (SELECT itemId from #top_bids)
            GROUP BY 
                AI.itemID, 
                AI.ItemTitle, 
                AI.itemStartingPrice,     
                CAST(AI.ItemDescription AS VARCHAR(1000)),
                AI.ItemEndDate";
    $getResults4 = sqlsrv_query($conn, $print_bids);

    // TODO: Loop through results and print them out as list items.
        WHILE ($row = sqlsrv_fetch_array($getResults4)) {

            $item_id = $row['itemID'];
            $title = $row['ItemTitle'];
            $desc = $row['Description'];
            $end_time = $row['ItemEndDate'];
            $price = $row['MaxBid'];
            $num_bids = $row['NoOfBids'];
            $start_price = $row['itemStartingPrice'];

            print_listing_li($item_id, $title, $desc, $price, $num_bids, $end_time, $start_price);
        }
        sqlsrv_free_stmt($getResults4);
}
?>