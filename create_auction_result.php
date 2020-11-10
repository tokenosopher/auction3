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

        //echo ($auction["ItemTitle"]) ."<br>";
        //echo ($auction["ItemDescription"]) . "<br>";
        //echo (gettype($auction["ItemStartingPrice"])) . "<br>";
        //echo (gettype($auction["ItemReservePrice"])) . "<br>";
        //echo (($auction["ItemEndDate"])) . "<br>";
        //echo ($auction["Category"]) . "<br>";
        //echo (gettype($auction["Category"])) . "<br>";
        echo ($current_date);
        $current_date<($auction["ItemEndDate"])
           or die("Your auction end date is in the past!");


        session_start();

        //echo ( gettype($_SESSION['user_id']))."<br>";
        //echo ($_SESSION['email'])."<br>";
       // echo (gettype($_SESSION['logged_in']))."<br>";
        //echo ($_SESSION['account_type'])."<br>";

        $query1 = "SELECT sellerId FROM databaseucl.dbo.Sellers
        WHERE (userId = '{$_SESSION['user_id']}')" ;

        $select1 = sqlsrv_query($conn, $query1,);
        $row1 = sqlsrv_fetch_array($select1);
        //echo ($row1[0]);






        // DoneTODO: Extract $_POST variables, check they're OK, and attempt to create
        // an account. Notify user of success/failure and redirect/give navigation
        // options.

        /*$serverName = "databaseucl.database.windows.net";
        $connectionOptions = array(
            "Database" => "databaseucl",
            "Uid" => "narcis",
            "PWD" => "P4ssword"
        );

        $conn = sqlsrv_connect($serverName, $connectionOptions)
        or die('Error connecting to the server.' . sqlsrv_errors()['message']); */

        $query = "INSERT INTO databaseucl.dbo.AuctionItems(sellerId, ItemTitle, ItemDescription,ItemStartingPrice, ItemReservePrice,ItemEndDate,categoryId)
                    VALUES ( '${row1[0]}','${auction["ItemTitle"]}', '${auction["ItemDescription"]}', '${auction["ItemStartingPrice"]}','${auction["ItemReservePrice"]}', '${auction["ItemEndDate"]}', '${auction["Category"]}' )";

        $result = sqlsrv_query($conn, $query)
        or die('Error making saveToDatabase query');
        sqlsrv_close($conn);

        if ($result==true)
            echo('<div class="text-center">Auction successfully created! <a href="mylistings.php">View your new listing.</a></div>');
        // This function takes the form data and adds the new auction to the database.


        /* TODO #1: Connect to MySQL database (perhaps by requiring a file that
                    already does this). */


        /* TODO #2: Extract form data into variables. Because the form was a 'post'
                    form, its data can be accessed via $POST['auctionTitle'],
                    $POST['auctionDetails'], etc. Perform checking on the data to
                    make sure it can be inserted into the database. If there is an
                    issue, give some semi-helpful feedback to user. */


        /* TODO #3: If everything looks good, make the appropriate call to insert
                    data into the database. */




        // If all is successful, let user know.
        ?>

    </div>


<?php include_once("footer.php")?>