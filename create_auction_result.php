<?php include_once("header.php")?>
<?php include_once 'db_con/db_li.php'?>

    <div class="container my-5">

        <?php
        $auction = array();
        $myvar = $_POST["auctionTitle"];
        $auction["ItemTitle"] = str_replace("'","''",$myvar); //replacing all apostrophes with double quote marks
        $auction["ItemTitle"] !=''
            or die ("You must include a title for your auction!");
        $myvar1 = $_POST["auctionDetails"];
        $auction["ItemDescription"] = str_replace("'","''",$myvar1); // replacing all apostrophes with double quote marks
        $auction["Category"] = intval($_POST["auctionCategory"]); // converting html str input into integer to store in the database
        $auction["ItemStartingPrice"] = floatval($_POST["auctionStartPrice"]); //converting string into float for price

        if ($_POST["auctionReservePrice"]==0){
            $auction["ItemReservePrice"]=floatval($_POST["auctionStartPrice"]);
        }
        else {
                $auction["ItemReservePrice"] = floatval($_POST["auctionReservePrice"]);
        }
        // Making sure that an empty or null reserve price is automatically set to the  Starting Price

        $myvar2 = $_POST["auctionEndDate"];
        $auction["ItemEndDate"]=str_replace("T"," ",$myvar2); // converting html datetime into compatible form with the database


        $current_date = date('Y-m-d H:i:s'); // getting current date
        $current_date<($auction["ItemEndDate"])
           or die("Your auction end date is in the past!"); // Checking that the auction end date is in the future



        $auction['ItemStartingPrice']<($auction["ItemReservePrice"]+0.01)
        or die("Your starting price is higher than your reserve price!"); // Checking that the reserve price is higher than the starting price


        $myseller_id=$_SESSION['seller_id'];


        $query = "INSERT INTO databaseucl.dbo.AuctionItems(sellerId, ItemTitle, ItemDescription,ItemStartingPrice, ItemReservePrice,ItemEndDate,categoryId)
                    VALUES ( '${myseller_id}','${auction["ItemTitle"]}', '${auction["ItemDescription"]}', '${auction["ItemStartingPrice"]}','${auction["ItemReservePrice"]}', '${auction["ItemEndDate"]}', '${auction["Category"]}' )";
            // This query inserts all the values retrieved from the HTML Post into the AuctionItems Table


        $result = sqlsrv_query($conn, $query)
        or die('Error making saveToDatabase query');
        //sqlsrv_close($conn);

        if ($result==true)
            echo('<div class="text-center">Auction successfully created! <a href="mylistings.php">View your new listing.</a></div>');
        // Letting the user know the auction has been successfully created


        ?>

    </div>

<?php include_once("footer.php")?>