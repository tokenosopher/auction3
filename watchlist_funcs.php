<?php include_once('db_con/db_li.php')?>
<?php
    $user_id = 5;
    $isbuyerstring = "SELECT TOP 1 BuyerID FROM Buyers WHERE Buyers.UserID =".$user_id;
    $isbuyerresults = sqlsrv_query($conn, $isbuyerstring);
    $buyer_id = sqlsrv_fetch_array($isbuyerresults)['BuyerID'];
    sqlsrv_free_stmt($isbuyerresults);

    if (!isset($_POST['functionname']) || !isset($_POST['arguments'])) {
        return;
    }

    // Extract arguments from the POST variables:
    $item_id = $_POST['arguments'];

    if ($_POST['functionname'] == "add_to_watchlist") {
        if ($isbuyerresults){
            $parameters = "(".$buyer_id.",".$item_id.")";
            $insertstring = "INSERT INTO WatchList (BuyerID, ItemID) VALUES ".$parameters;
            sqlsrv_query($conn, $insertstring);
            $res = "success";
        }
        else{
            $res = "failure";
        }
    }
    else if ($_POST['functionname'] == "remove_from_watchlist") {
        if ($isbuyerresults){
            $isitemwatchedstring = "SELECT * FROM WatchList WHERE ItemID =".$item_id." and BuyerID =".$buyer_id;
            $isitemwatched = sqlsrv_query($conn, $isitemwatchedstring);
            if($isitemwatched){
                $deletestring = "DELETE FROM WatchList WHERE ItemID =".$item_id." and BuyerID =".$buyer_id;
                sqlsrv_query($conn, $deletestringstring);
                $res = "success";
            }
        }
        else{
            $res = "failure";
        }
    }

    // Note: Echoing from this PHP function will return the value as a string.
    // If multiple echo's in this file exist, they will concatenate together,
    // so be careful. You can also return JSON objects (in string form) using
    // echo json_encode($res).
    echo $res;

?>
