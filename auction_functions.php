<?php include_once('db_con/db_li.php')?>

<?php
    function is_session_started(){
        if ( php_sapi_name() !== 'cli' ) {
            if ( version_compare(phpversion(), '5.4.0', '>=') ) {
                return session_status() === PHP_SESSION_ACTIVE ? TRUE : FALSE;
            } else {
                return session_id() === '' ? FALSE : TRUE;
            }
        }
        return FALSE;
    }

    if ( is_session_started() === FALSE ) session_start();

    function validauction($item_id){
        $checkstring = "SELECT itemId FROM AuctionItems WHERE itemId =".$item_id;
        global $conn;
        $results = sqlsrv_query($conn, $checkstring);
        $foundId = sqlsrv_fetch_array($results)['itemId'];
        sqlsrv_free_stmt($results);
        if(isset($foundId)){
            return true;
        }
        else{
            return false;
        }
    }

    function getselleremail($seller_id){
        $checkstring ="
                        SELECT U.EmailAddress 
                        FROM Sellers S
                        LEFT JOIN Users2 U on U.UserID = S.userId 
                        WHERE S.sellerId =".$seller_id;
        global $conn;
        $results = sqlsrv_query($conn, $checkstring);
        $email = sqlsrv_fetch_array($results)['EmailAddress'];
        sqlsrv_free_stmt($results);
        return $email;
    }

    function getmylistings(){
        $seller_id = $_SESSION['seller_id'];
        $query =
            "SELECT
             AI.itemId,
             AI.itemTitle,
             CAST(AI.itemDescription AS VARCHAR(1000)) Description,
             AI.itemEndDate,
             AI.itemStartingPrice,
             AI.itemReservePrice,
             MAX(B.bidValue) MaxBid,
             COUNT(B.bidValue) NoOfBids
             FROM
             AuctionItems AI
             LEFT JOIN Bids B ON
             AI.itemID = B.itemID
             WHERE
             AI.sellerID = {$seller_id}
             GROUP BY
             AI.itemId,
             AI.itemTitle,
             CAST(AI.itemDescription AS VARCHAR(1000)),
             AI.itemStartingPrice,
             AI.itemReservePrice,
             AI.itemEndDate; ";
        return $query;
    }

    function getmybids(){
        $buyer_id = $_SESSION['buyer_id'];
        $query = sprintf(
                            "SELECT
                                AI.itemId,
                                AI.itemTitle,
                                CAST(AI.itemDescription AS VARCHAR(1000)) Description,
                                AI.itemEndDate,
                                MAX(B.bidValue) MaxBid,
                                COUNT(B.bidValue) NoOfBids,
                                A.UserMaxBid
                             FROM
                                AuctionItems AI
                             FULL JOIN Bids B ON
                                AI.itemID = B.itemID
                             LEFT JOIN (SELECT buyerId, itemId, MAX(bidValue) UserMaxBid FROM Bids WHERE buyerId = %s GROUP BY buyerId,itemID) A ON
                                AI.itemID = A.itemID
                             WHERE
                                A.UserMaxBid is not null 
                             GROUP BY
                                AI.itemID,
                                AI.itemTitle,
                                A.UserMaxBid,
                                CAST(AI.itemDescription AS VARCHAR(1000)),
                                AI.itemEndDate;",$buyer_id);
        global $conn;
        $getResults= sqlsrv_query($conn, $query);
        return $getResults;
    }

    function iswatchingauction($buyer_id,$item_id){

        $checkstring = sprintf("SELECT * FROM WatchList WHERE buyerId = %s and itemID = %s", $buyer_id, $item_id);
        global $conn;
        $results = sqlsrv_query($conn,$checkstring);
        $foundId = sqlsrv_fetch_array($results)['itemId'];
        sqlsrv_free_stmt($results);
        if(isset($foundId)){
            return true;
        }
        else{
            return false;
        }
    }

    function auctionended($item_id){
        $checkstring = "SELECT AI.ItemEndDate FROM AuctionItems AI WHERE AI.itemID = ".$item_id;
        global $conn;
        $results = sqlsrv_query($conn,$checkstring);
        $enddate = sqlsrv_fetch_array($results)['ItemEndDate'];
        sqlsrv_free_stmt($results);
        $now = new DateTime();
        if($now>=$enddate){
            return true;
        }
        else{
            return false;
        }
    }

    function buyermaxbidonauction($item_id){
        $buyer_id = $_SESSION["buyer_id"];
        $buyermaxbidstring = sprintf(
                "SELECT 
                    buyerId,
                    itemId,
                    MAX(bidValue) UserMaxBid 
                 FROM 
                    Bids 
                 WHERE 
                    buyerID = %s and 
                    itemId = %s 
                 GROUP BY 
                    buyerId,
                    itemID", $buyer_id, $item_id);
        global $conn;
        $buyermaxbidonitem = sqlsrv_query($conn, $buyermaxbidstring);
        $buyermaxbidvalue = 0;
        WHILE ($row = sqlsrv_fetch_array($buyermaxbidonitem)) {
            $buyermaxbidvalue = $row['UserMaxBid'];
            break;
        }
        sqlsrv_free_stmt($buyermaxbidonitem);
        return $buyermaxbidvalue;
    }

    function add_bid($item_id,$bid_amt){
        $buyer_id = $_SESSION["buyer_id"];
        $insertstring = sprintf(
                    "INSERT INTO 
                        Bids (buyerId, itemId, bidValue) 
                     VALUES 
                        (%s,%s,%s)", $buyer_id, $item_id, $bid_amt);
        global $conn;
        sqlsrv_query($conn,$insertstring);
    }

    function getcurrentwinninguser($item_id){
        $winninguserstring = sprintf(
            "SELECT TOP 1 
                B.buyerId,
                U.EmailAddress,
                Bd.bidValue 
             FROM 
                  BIDS Bd
             LEFT JOIN Buyers B ON Bd.buyerId = B.buyerId
             LEFT JOIN Users2 U ON B.userId = U.UserID
             WHERE 
                   itemId = %s 
             ORDER BY
                bidValue desc,
                bidDateTime asc"
            , $item_id);
        global $conn;
        $winninguserresults = sqlsrv_query($conn,$winninguserstring);
        WHILE ($row = sqlsrv_fetch_array($winninguserresults)) {
            $winningbid = [
                "BuyerId" => $row['buyerId'],
                "EmailAddress" => $row['EmailAddress'],
                "WinningBidAmt" => $row['bidValue']
            ];
            break;
        }
        sqlsrv_free_stmt($winninguserresults);
        return $winningbid;
    }

    //this obfuscate email function was found here:
    //https://stackoverflow.com/questions/20545301/partially-hide-email-address-in-php
    //user kks21199 12-12-2013 13:57
    function obfuscateemail($email){
        $em   = explode("@",$email);
        $name = implode('@', array_slice($em, 0, count($em)-1));
        $len  = floor(strlen($name)/2);
        return substr($name,0, $len) . str_repeat('*', $len) . "@" . end($em);
    }

    function printbidsforauction($item_id){
        $querystring = sprintf("
                        SELECT
                            Bids.bidValue,
                            Bids.bidDateTime,
                            U.EmailAddress
                        FROM 
                             Bids 
                        LEFT JOIN Buyers B on Bids.buyerId = B.buyerId
                        LEFT JOIN Users2 U on B.userId = U.UserID
                        WHERE Bids.itemId =%s
                        ORDER BY
                            Bids.bidValue desc,
                            Bids.bidDateTime asc
                        ", $item_id);
        global $conn;
        $bidsforauction = sqlsrv_query($conn, $querystring);
        echo(
            '<li class="list-group-item d-flex justify-content-between">
                        <div class="text-center text-nowrap"><h6>User</h6></div>
                        <div class="text-center text-nowrap"><h6>Bid Amount</h6></div>
                        <div class="text-center text-nowrap"><h6>Date and Time</h6></div>
             </li>'
        );
        WHILE ($row = sqlsrv_fetch_array($bidsforauction)) {
            $Email_Address = obfuscateemail($row['EmailAddress']);
            $Bid_value = $row["bidValue"];
            $Bid_Datetime = date_format($row["bidDateTime"],"Y-M-d H:i:s u e");

            echo(
                    '<li class="list-group-item d-flex justify-content-between">
                        <div class="p-2 mr-5">'.$Email_Address.'</div>
                        <div class="text-center text-nowrap">£'.$Bid_value.'</div>
                        <div class="text-center text-nowrap">'.$Bid_Datetime.'</div>
                    </li>'
            );
        }
        sqlsrv_free_stmt($bidsforauction);
    }

    function getauctionstatus($item_id){
        $auctionended = auctionended($item_id);
        $auction = getauctiondetails($item_id);
        $reserve_price = $auction['reserve_price'];
        $maxbid = $auction['current_price'];
        $num_bids = $auction['num_bids'];
        $auction_seller = $auction['seller_id'];
        if($num_bids > 0){
            $winning_bid = getcurrentwinninguser($item_id);
            $winnerid = $winning_bid['BuyerId'];
            $winnersemail = $winning_bid['EmailAddress'];
        }

        if(isset($_SESSION['buyer_id'])){ $buyer_id = $_SESSION['buyer_id'];}
        $status = '';

        if($auctionended){
            $status = $status."This auction has ended.";
            if($num_bids > 0){
                if($maxbid >= $reserve_price){
                    if(isset($buyer_id) and $buyer_id == $winnerid){
                        $status = $status." You won this auction.";
                    }
                    elseif($_SESSION('seller_id') == $auction_seller){
                        $winner_string = sprintf(" The winner was %s.",$winnersemail);
                        $status = $status.$winner_string;
                    }
                    else{
                        $winner_string = sprintf(" The winner was %s.",obfuscateemail($winnersemail));
                        $status = $status.$winner_string;
                    }
                }
                else{
                    $status = $status."The maximum bid failed to meet the reserve price, there is no winner.";
                }
            }
            else{
                $status = $status." No bids have been made on this auction, there is no winner.";
            }
        }
        else{
            $status = $status."This auction is currently live.";
            if($num_bids>0){
                if($maxbid >= $reserve_price){
                    if(isset($buyer_id)){
                        if($buyer_id == $winnerid){
                            $status = $status." You are currently winning this auction!";
                        }
                        else{
                            $status = $status." You are currently not winning this auction.";
                            $winner_string = sprintf(" The leader is currently %s, bid higher to get into the lead.",obfuscateemail($winnersemail));
                            $status = $status.$winner_string;
                        }
                    }
                    else{
                        $winner_string = sprintf(" The leader is currently %s.",obfuscateemail($winnersemail));
                        $status = $status.$winner_string;
                    }
                }
                else{
                    $status = $status." The current highest bid is lower than the reserve price and cannot win.";
                }
            }else{
                $status = $status." There are currently no bids on this auction.";
            }

        }
        return $status;
    }

    //This function returns an associative array. Access results using $varname["attribute"], see below for attr names
    function getauctiondetails($item_id){

        $isauctionstring = sprintf(
            "SELECT 
                AI.itemID, 
                AI.ItemTitle, 
                CAST(AI.ItemDescription AS VARCHAR(1000)) Description,
                AI.ItemEndDate, 
                MAX(B.BidValue) MaxBid,
                COUNT(B.BidValue) NoOfBids, 
                AI.itemStartingPrice,
                AI.itemReservePrice,
                AI.sellerId
             FROM 
                AuctionItems AI
             LEFT JOIN 
                Bids B ON AI.itemID = B.itemID
             WHERE 
                AI.itemID = %s
             GROUP BY 
                AI.itemID, 
                AI.sellerId,      
                AI.ItemTitle, 
                CAST(AI.ItemDescription AS VARCHAR(1000)), 
                AI.ItemEndDate,
                AI.itemReservePrice, 
                AI.itemStartingPrice;", $item_id);
        global $conn;
        $listingResults= sqlsrv_query($conn, $isauctionstring);
        WHILE ($row = sqlsrv_fetch_array($listingResults)) {
            $auction = [
            "title" => $row['ItemTitle'],
            "description" => $row['Description'],
            "current_price" => $row['MaxBid'],
            "num_bids" => $row['NoOfBids'],
            "end_time" => $row['ItemEndDate'],
            "starting_price" => $row['itemStartingPrice'],
            "reserve_price" => $row['itemReservePrice'],
            "seller_id" => $row['sellerId']
            ];
            break;
        }
        sqlsrv_free_stmt($listingResults);
        return $auction;
    }
?>
