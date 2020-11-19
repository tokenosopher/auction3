<?php include_once("header.php")?>
<?php require("utilities.php")?>
<?php include_once('auction_functions.php')?>

<div class="container">
<h2 class="my-3">My listings</h2>

<div class="container mt-5">
<?php
  // This page is for showing a user the auction listings they've made.
  // It will be pretty similar to browse.php, except there is no search bar.
  // This can be started after browse.php is working with a database.
  // Feel free to extract out useful functions from browse.php and put them in
  // the shared "utilities.php" where they can be shared by multiple files.
// TODO: Perform a query to pull up their auctions.



// TODO: Pagination part of the query
if (!isset($_GET['page'])) {
    $curr_page = 1;
}
else {
    $curr_page = $_GET['page'];
}

$query = getmylistings();

$getting_array = sqlsrv_query($conn, $query, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
$num_results = sqlsrv_num_rows($getting_array);
$results_per_page = 10;
$max_page = ceil($num_results / $results_per_page);

?>

<ul class="list-group">

    <?php
// TODO: Check user's credentials (cookie/session).
if (isset($_SESSION['logged_in']) and $_SESSION['account_type'] == 'seller') {
    $user_id = $_SESSION['user_id'];
    $seller_id = $_SESSION['seller_id'];


    // TODO: Loop through results and print them out as list items.
    $getResults = sqlsrv_query($conn,$query);
    while ($row = sqlsrv_fetch_array($getResults)) {

        $item_id = $row['itemId'];
        $title = $row['itemTitle'];
        $desc = $row['Description'];
        $end_time = $row['itemEndDate'];
        $price = $row['MaxBid'];
        $num_bids = $row['NoOfBids'];
        $starting_price = $row['AI.itemStartingPrice'];
        $reserve_price = $row['AI.itemReservePrice'];
        $auction_status = getauctionstatus($item_id);

        print_my_listings_li($item_id, $title, $desc, $price, $num_bids, $end_time, $starting_price, $reserve_price, $auction_status);
    }
//    sqlsrv_free_stmt($getResults);
//    sqlsrv_close($conn);
}
?>

    </ul>
    <!-- Pagination for results listings -->
    <nav aria-label="Search results pages" class="mt-5">
        <ul class="pagination justify-content-center">

            <?php
            // Copy any currently-set GET variables to the URL.
            $querystring = "";
            foreach ($_GET as $key => $value) {
                if ($key != "page") {
                    $querystring .= "$key=$value&amp;";
                }
            }

            $high_page_boost = max(3 - $curr_page, 0);
            $low_page_boost = max(2 - ($max_page - $curr_page), 0);
            $low_page = max(1, $curr_page - 2 - $low_page_boost);
            $high_page = min($max_page, $curr_page + 2 + $high_page_boost);

            if ($curr_page != 1) {
                echo('
    <li class="page-item">
      <a class="page-link" href="mylistings.php?' . $querystring . 'page=' . ($curr_page - 1) . '" aria-label="Previous">
        <span aria-hidden="true"><i class="fa fa-arrow-left"></i></span>
        <span class="sr-only">Previous</span>
      </a>
    </li>');
            }

            for ($i = $low_page; $i <= $high_page; $i++) {
                if ($i == $curr_page) {
                    // Highlight the link
                    echo('
                    <li class="page-item active">');
                }
                else {
                    // Non-highlighted link
                    echo('
                    <li class="page-item">');
                }

                // Do this in any case
                echo('
      <a class="page-link" href="mylistings.php?' . $querystring . 'page=' . $i . '">' . $i . '</a>
    </li>');
            }
    if ($num_results != 0) {
        if ($curr_page != $max_page) {
            echo('<li class="page-item">
    <a class="page-link" href="mylistings.php?' . $querystring . 'page=' . ($curr_page + 1) . '" aria-label="Next">
    <span aria-hidden="true"><i class="fa fa-arrow-right"></i></span>
    <span class="sr-only">Next</span>
    </a>
    </li>');}
    }
    else {echo ('<div class="text-center">You have no listings! <a href="create_auction.php.php">Create one!</a></div>');}
            ?>

        </ul>
    </nav>


</div>



<?php include_once("footer.php")?>
