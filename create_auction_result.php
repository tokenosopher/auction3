<?php include_once("header.php")?>
<?php include_once 'db_con/db_li.php'?>

    <div class="container my-5">

        <?php
        $auction = array();
        $myvar = $_POST["auctionTitle"];
        $auction["ItemTitle"] = str_replace("'","''",$myvar); //replacing all apostrophes with double quote marks
        $myvar1 = $_POST["auctionDetails"];
        $auction["ItemDescription"] = str_replace("'","''",$myvar1); // replacing all apostrophes with double quote marks
        $auction["Category"] = intval($_POST["auctionCategory"]); // converting html str input into integer to store in the database
        $auction["ItemStartingPrice"] = floatval($_POST["auctionStartPrice"]); //converting string into float for price
        $auction["ItemReservePrice"] = floatval($_POST["auctionReservePrice"]); // converting string into float for price
        $myvar2 = $_POST["auctionEndDate"];
        $auction["ItemEndDate"]=str_replace("T"," ",$myvar2); // converting html datetime into compatible form with the database


        $current_date = date('Y-m-d H:i:s');
        $current_date<($auction["ItemEndDate"])
           or die("Your auction end date is in the past!");

        $auction['ItemStartingPrice']<($auction["ItemReservePrice"])
        or die("Your starting price is higher than your reserve price!");


        $myseller_id=$_SESSION['seller_id'];


        $query = "INSERT INTO databaseucl.dbo.AuctionItems(sellerId, ItemTitle, ItemDescription,ItemStartingPrice, ItemReservePrice,ItemEndDate,categoryId)
                    VALUES ( '${myseller_id}','${auction["ItemTitle"]}', '${auction["ItemDescription"]}', '${auction["ItemStartingPrice"]}','${auction["ItemReservePrice"]}', '${auction["ItemEndDate"]}', '${auction["Category"]}' )";



        $result = sqlsrv_query($conn, $query)
        or die('Error making saveToDatabase query');
        //sqlsrv_close($conn);

        if ($result==true)
            echo('<div class="text-center">Auction successfully created! <a href="mylistings.php">View your new listing.</a></div>');
        // This function takes the form data and adds the new auction to the database.


        ?>

    </div>


<?php include_once("footer.php")?>