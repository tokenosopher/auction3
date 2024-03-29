<?php include_once("header.php")?>
<?php require("utilities.php")?>
<?php
//This part of the code stores the connection to DB and we store in a separate file
include_once 'db_con/db_li.php'?>
<div class="container">

<h2 class="my-3">Browse listings</h2>

<div id="searchSpecs">
<!-- When this form is submitted, this PHP page is what processes it.
     Search/sort specs are passed to this page through parameters in the URL
     (GET method of passing data to a page). -->
<form method="get" action="browse.php">
  <div class="row">
    <div class="col-md-5 pr-0">
      <div class="form-group">
        <label for="keyword" class="sr-only">Search keyword:</label>
	    <div class="input-group">
          <div class="input-group-prepend">
            <span class="input-group-text bg-transparent pr-0 text-muted">
              <i class="fa fa-search"></i>
            </span>
          </div>
          <input type="text" class="form-control border-left-0" id="keyword" name="keyword" placeholder = "Search for anything" <?php if(isset($_GET['keyword'])) {echo 'value = "'.$_GET['keyword'].'"';}?>>
            <!--We additionally want to add for the search result to stay in the search bar and save latest searches-->
        </div>
      </div>
    </div>
    <div class="col-md-3 pr-0">
      <div class="form-group">
        <label for="cat" class="sr-only">Search within:</label>
          <?php
//          This part of the code retrieves all the categories from DB
          $category_search_query = "SELECT * FROM Category";
          $getResultsCategories = sqlsrv_query($conn,$category_search_query);
          ?>
            <select class="form-control" id="cat" name = "cat">
              <option value="all">All categories</option>
                <!--This part of the code dynamically pulls Categories from DB into HTML Dropdown Menu-->
                <?php
//                This while look prints all the values for category names
                while ($rows = sqlsrv_fetch_array($getResultsCategories,SQLSRV_FETCH_ASSOC))
                {
                    $category_id = $rows['categoryId'];
                    $category_name = $rows['categoryName'];

                    if ($_GET['cat'] == $category_id)
                    {echo "<option selected value = '$category_id'>$category_name</option>";}
                    else
                    {echo "<option value = '$category_id'>$category_name</option>";}

                }
          sqlsrv_free_stmt($getResultsCategories);
          ?>
        </select>
      </div>
    </div>
    <div class="col-md-3 pr-0">
      <div class="form-inline">
        <label class="mx-2" for="order_by">Sort by:</label>
        <select class="form-control" id="order_by" name = "order_by">
            <?php
//              Here I created an associated array, also known as dictionary in python, that essentially displays
            //  the options for sort by and sends the appropriate search variables to GET
                $assoc_arrays_ordering = array("1"=>"Price (low to high)", "2"=>"Price (high to low)", "3"=>"Soonest expiry");
                    foreach ($assoc_arrays_ordering as $order_by => $text)
                    {
                        if ($_GET['order_by'] == $order_by)
                        {echo "<option selected value='$order_by'>$text</option>";}
                            else
                        {echo "<option value='$order_by'>$text</option>";}
                    }
            ?>
        </select>
      </div>
    </div>
    <div class="col-md-1 px-0">
      <button type="submit" class="btn btn-primary">Search</button>
    </div>
  </div>
</form>
</div> <!-- end search specs bar -->

</div>

<?php
  // Retrieve these from the URL

//TODO: I still need to work out how perform a query search for the key word
  if (!isset($_GET['keyword']) or $_GET['keyword'] == "") {
      $keyword = "";
      // TODO: Define behavior if a keyword has not been specified.
  }
  else {
    $keyword = $_GET['keyword'];

//    Now I have to clean both sides of the search from blank spaces and break down
//    the search into an array that will allow me to search for every word in the search
    $keyword = ltrim($keyword);
    $keyword = rtrim($keyword);

//  This part of the code avoids SQL injection through single apostrophe sign
    $keyword = str_replace("'","''",$keyword);}

  if (!isset($_GET['cat']) OR $_GET['cat'] == 'all') {
    // TODO: Define behavior if a category has not been specified.
      $category_search = " ";
  }

  else {
    $category = $_GET['cat'];
    $category_search = "AND AI.categoryId = $category";
  }

  if (!isset($_GET['order_by'])) {
    // TODO: Define behavior if an order_by value has not been specified.
    $ordering = "MaxBid";
  }
  else {
    $ordering = $_GET['order_by'];
//    This step is done to avoid SQL injection, one associative array, finds a key to another associative array
    $assoc_arrays_ordering_queries = array("1"=>"MaxBid", "2"=>"MaxBid DESC", "3"=>"itemEndDate");
    $ordering = $assoc_arrays_ordering_queries[$ordering];

  /*  This part of drop down menu is also done. I have created an Associative Arrays, in Python they are called
  dictionaries, to match the value of html with query input for SQL.
  I am thinking, should we just change the variable in html to match with those required for SQL? */
  }

//This part is done, pagination is working fine.

  if (!isset($_GET['page'])) {
    $curr_page = 1;
  }
  else {
    $curr_page = $_GET['page'];
  }


  /* TODO: Use above values to construct a query. Use this query to 
     retrieve data from the database. (If there is no form data entered,
     decide on appropriate default value/default query to make. */

// This function runs a query for retrieving the number of currently active auctions to work out
// the number of pages for pagination
function number_of_listings($conn,$keyword,$category_search)
    {
    $active_auctions_query = "SELECT * FROM AuctionItems AI WHERE AI.itemEndDate > GETDATE() {$keyword} {$category_search}";

    $getResults = sqlsrv_query($conn, $active_auctions_query,array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
    /* reference 1:  https://www.php.net/manual/en/function.sqlsrv-query.php
       reference 2:  https://www.php.net/manual/en/function.sqlsrv-num-rows.php
     reference 3:  https://docs.microsoft.com/en-us/sql/connect/php/cursor-types-sqlsrv-driver?view=sql-server-ver15*/
        $numb_of_rows = sqlsrv_num_rows($getResults);
        sqlsrv_free_stmt($getResults);
        return $numb_of_rows;

    }

//    Querying of the the search word happens in 3 stages, we fist find the exact match to user's input
    $resulting_keyword = "AND (AI.itemDescription like '%" . $keyword . "%' or AI.itemTitle like '%" . $keyword . "%')";
    $num_results = number_of_listings($conn, $resulting_keyword, $category_search);

//  Then we need to check if there were any results displayed, if the results are empty, we break up the sting of words
// and search whether any of the listings contain both words, but not next to each other
    if($num_results ==0){$keywords_item_description_and = "AI.itemDescription like '%".implode("%' AND AI.itemDescription like '%",explode(" ",$keyword))."%'";
                         $keywords_item_title_and = "AI.itemTitle like '%".implode("%' AND AI.itemTitle like '%",explode(" ",$keyword))."%'";
                         $search_keywords = "AND ({$keywords_item_description_and} OR {$keywords_item_title_and})";
                         $num_results = number_of_listings($conn,$search_keywords,$category_search);
                         $resulting_keyword = $search_keywords;}

//  Then, in the worst case scenario, if AND didn't work, we will display listings that have either of the words either in the description, or the title
    if($num_results ==0){$keywords_item_description_or = "AI.itemDescription like '%".implode("%' OR AI.itemDescription like '%",explode(" ",$keyword))."%'";
                         $keywords_item_title_or = "AI.itemTitle like '%".implode("%' OR AI.itemTitle like '%",explode(" ",$keyword))."%'";
                         $keyword_or = "AND ({$keywords_item_description_or} OR {$keywords_item_title_or})";
                         $num_results = number_of_listings($conn,$keyword_or,$category_search);
                         $resulting_keyword = $keyword_or;}

$results_per_page = 10;
$max_page = ceil($num_results / $results_per_page);
?>

<div class="container mt-5">

<!-- TODO: If result set is empty, print an informative message. Otherwise... -->
<!--Done! Outputs "No auctions were found for your search request, please alter your search!"-->

<?php if ($num_results == 0) {echo '<H5> No results for your search were found.<br> Try checking your spelling or alter your search criteria.</H5>';}?>

<ul class="list-group">

<!-- TODO: Use a while loop to print a list item for each auction listing retrieved from the query -->

<?php
//$results_for_current_page shows the offset for the current page, for the first page it will be 0, second 10, third 20 and so on
  $results_for_current_page = ($curr_page-1)*$results_per_page;

//The query below is dynamic. This means that it fetches the page value then adds an offset and lists the next 10 active auctions
  $query = "SELECT AI.itemId, AI.itemTitle, CAST(AI.itemDescription AS VARCHAR(100)) Description, AI.itemEndDate, MAX(B.bidValue) MaxBid,
    COUNT(B.bidValue) NoOfBids, AI.categoryId, AI.itemStartingPrice
    FROM AuctionItems AI
    LEFT JOIN Bids B ON AI.itemID = B.itemID
    WHERE (AI.itemEndDate > GETDATE()) {$resulting_keyword} {$category_search} 
    GROUP BY AI.itemId, AI.itemTitle, CAST(AI.itemDescription AS VARCHAR(100)), AI.itemEndDate, AI.categoryId, 
      AI.itemStartingPrice ORDER BY {$ordering} OFFSET {$results_for_current_page} ROWS FETCH NEXT {$results_per_page} ROWS ONLY";

$getResults = sqlsrv_query($conn, $query);

//This while look outputs the listings in such way that they are formatted as "Boxes" of auctions that contain all the relevant info
while ($row = sqlsrv_fetch_array($getResults)) {
    print_listing_li($row['itemId'], $row['itemTitle'], $row['Description'], $row['MaxBid'], $row['NoOfBids'], $row['itemEndDate'],$row['itemStartingPrice']);}
    sqlsrv_free_stmt($getResults);
    sqlsrv_close($conn);
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
      <a class="page-link" href="browse.php?' . $querystring . 'page=' . ($curr_page - 1) . '" aria-label="Previous">
        <span aria-hidden="true"><i class="fa fa-arrow-left"></i></span>
        <span class="sr-only">Previous</span>
      </a>
    </li>');
  }
    
  for ($i = $low_page; $i <= $high_page; $i++) {
    if ($i == $curr_page) {
      // Highlight the link
      echo('<li class="page-item active">');
    }
    else {
      // Non-highlighted link
      echo('<li class="page-item">');
    }

    // Do this in any case
    echo('
      <a class="page-link" href="browse.php?' . $querystring . 'page=' . $i . '">' . $i . '</a>
    </li>');
  }

  if ($num_results != 0){
  if ($curr_page != $max_page){
    echo('
    <li class="page-item">
      <a class="page-link" href="browse.php?' . $querystring . 'page=' . ($curr_page + 1) . '" aria-label="Next">
        <span aria-hidden="true"><i class="fa fa-arrow-right"></i></span>
        <span class="sr-only">Next</span>
      </a>
    </li>');
  }}
  else {echo "";}
?>

  </ul>
</nav>


</div>

<?php include_once("footer.php")?>