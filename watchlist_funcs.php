<?php include_once('db_con/db_li.php')?>

<?php

    if (!isset($_POST['functionname']) || !isset($_POST['arguments'])) {
        return;
    }

    // Extract arguments from the POST variables:
    $item_id = $_POST['arguments'][0];
    $buyer_id = $_POST['arguments'][1];

    if ($_POST['functionname'] == "add_to_watchlist") {
        if ($buyer_id){
            $parameters = "(".$buyer_id.",".$item_id.")";
            $insertstring = "INSERT INTO WatchList (BuyerID, ItemID) VALUES ".$parameters;
            sqlsrv_query($conn, $insertstring);
            $res = "success";
        }
        else{
            $res = "failure";
        }
    }
    elseif ($_POST['functionname'] == "remove_from_watchlist") {
        if ($buyer_id){
            $conditionstring = " WHERE ItemID = ".$item_id." AND BuyerID = ".$buyer_id;
            $deletestring = "DELETE FROM WatchList".$conditionstring;
            sqlsrv_query($conn, $deletestring);
            $res = "success";
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
