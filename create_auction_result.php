<?php include_once("header.php")?>
<?php include_once 'db_con/db_li.php'?>

    <div class="container my-5">

        <?php
        $auction = array();
        $auction["ItemTitle"] = $_POST["auctionTitle"];
        $auction["ItemDescription"] = $_POST["auctionDetails"];
        $auction["Category"] = $_POST["auctionCategory"];
        $auction["ItemStartingPrice"] = floatval($_POST["auctionStartPrice"]);
        $auction["ItemReservePrice"] = floatval($_POST["auctionReservePrice"]);
        $myvar = $_POST["auctionEndDate"];
        $auction["ItemEndDate"]=str_replace("T"," ",$myvar);

        //echo ($auction["ItemTitle"]) ."<br>";
        //echo ($auction["ItemDescription"]) . "<br>";
        //echo (gettype($auction["ItemStartingPrice"])) . "<br>";
        //echo (gettype($auction["ItemReservePrice"])) . "<br>";
        //echo (($auction["ItemEndDate"])) . "<br>";




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

        $query = "INSERT INTO databaseucl.dbo.AuctionItems(ItemTitle, ItemDescription,ItemStartingPrice, ItemReservePrice,ItemEndDate)
                    VALUES ( '${auction["ItemTitle"]}', '${auction["ItemDescription"]}', '${auction["ItemStartingPrice"]}','${auction["ItemReservePrice"]}', '${auction["ItemEndDate"]}' )";

        $result = sqlsrv_query($conn, $query)
        or die('Error making saveToDatabase query');
        sqlsrv_close($conn);

        if ($result==true)
            echo('<div class="text-center">Auction successfully created! <a href="FIXME">View your new listing.</a></div>');
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