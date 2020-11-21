<?php include_once('db_con/db_li.php')?>
<?php include_once('header.php')?>
<?php include_once('auction_functions.php')?>
<?php include_once ('email_functions.php')?>

<?php
// TODO: Extract $_POST variables, check they're OK, and attempt to make a bid.
// Notify user of success/failure and redirect/give navigation options.

    $min_bid_increase = 1;
    $bid_amt = $_POST["bid"];
    $item_id = $_POST["item_id"];
    $buyer_id = $_SESSION["buyer_id"];

    $auction = getauctiondetails($item_id);
    $current_price=0;
    $current_price = $auction['current_price'];
    $end_time = $auction['end_time'];
    $start_price = $auction['starting_price'];
    $reserve_price = $auction['reserve_price'];

    // Calculate time to auction end:
    $now = new DateTime();

    if(isset($buyer_id)){
        if (isset($end_time) and ($now < $end_time)) {
            if($bid_amt >= $start_price){
                //$buyermaxbidvalue = buyermaxbidonauction($item_id);
                if( $bid_amt >= ($current_price + $min_bid_increase) ){
                    add_bid($item_id,$bid_amt);
                    echo "Sucessfully bid £".$bid_amt." on the Auction";
                    if (selfOutbid($item_id)){outbidMail($item_id);}
                    watchlistMail($item_id);
                    if($bid_amt > $current_price){
                        echo "\nCongratulations you are the highest bidder";
                    }
                    else{
                        echo "\nYou are not the highest bidder.";
                    }
                    if($bid_amt < $reserve_price){
                        echo "\nHowever, your bid is less than the Auction's reserve price.\n";
                        echo "You cannot win the auction with your current bid.";
                    }
                }
                else{
                    echo "\nYou need to bid more than the current price. \n";
                    echo "Please enter a value of £".($current_price + $min_bid_increase)." or more";
                }
            }
            else{
                echo "\nSorry, your bid is less than the starting price and cannot be accepted.";
            }
        }
        elseif(isset($end_time) and ($now >= $end_time)){
            echo "\nSorry, the auction is over and you cannot bid anymore";
        }
        else{
            echo "\nInvalid Auction ID, User or Amount...";
        }
    }
    else{ echo "You need to login as a buyer account inorder to make a bid";}
?>

<?php






?>
<br/><br/>
<form action="listing.php" method="GET">
    <input type="hidden" name="item_id" value="<?php echo $item_id?>"/>
    <input type="submit" value="Go back to listing" />
</form>

