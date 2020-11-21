<?php include_once('db_con/db_li.php')?>
<?php include_once('header.php')?>
<?php include_once('auction_functions.php')?>
<?php include_once ('email_functions.php')?>

<?php
//initialising some variables from POST, SESSION and query results....
    $min_bid_increase = 1;  //You will have to bid this amount over the current winning bid
    $bid_amt = $_POST["bid"];
    $item_id = $_POST["item_id"];
    $buyer_id = $_SESSION["buyer_id"];

    $auction = getauctiondetails($item_id);  //runs query to get auction details
    $current_price=0;
    $current_price = $auction['current_price'];
    $end_time = $auction['end_time'];
    $start_price = $auction['starting_price'];
    $reserve_price = $auction['reserve_price'];

    // Calculate time to auction end:
    $now = new DateTime();

    if(isset($buyer_id)){
        if (!auctionended($item_id)) { //can only bid if auction is active
            if($bid_amt >= $start_price){  //bids have to be greater than the starting price
                if( $bid_amt >= ($current_price + $min_bid_increase) ){ //have to bid the min bid increase over current price
                    add_bid($item_id,$bid_amt);
                    echo "Successfully bid £".$bid_amt." on the Auction.";
                    if (selfOutbid($item_id)){outbidMail($item_id);} //Stops annoying extra emails sent to others when a user keeps outbidding themselves
                    watchlistMail($item_id); //notifies everyone watching the item that a bid has been made.
                    if($bid_amt > $current_price){
                        echo "\n Congratulations you are the highest bidder.";
                    }
                    else{
                        echo "\n You are not the highest bidder.";
                    }
                    if($bid_amt < $reserve_price){
                        echo "\n However, your bid is less than the Auction's reserve price.\n";
                        echo " You cannot win the auction with your current bid.";
                    }
                }
                else{
                    echo "\n You need to bid more than the current price.\n";
                    echo " Please enter a value of £".($current_price + $min_bid_increase)." or more.";
                }
            }
            else{
                echo "\n Sorry, your bid is less than the starting price and cannot be accepted.";
            }
        }
        elseif(isset($end_time) and ($now >= $end_time)){
            echo "\n Sorry, the auction is over and you cannot bid anymore.";
        }
        else{
            echo "\n Invalid Auction ID, User or Amount...";
        }
    }
    else{ echo " You need to login as a buyer account inorder to make a bid.";}
?>

<?php






?>
<!-- Provide a link to easily get back to the listing page to continue bidding! -->
<br/><br/>
<form action="listing.php" method="GET">
    <input type="hidden" name="item_id" value="<?php echo $item_id?>"/>
    <input type="submit" value="Go back to listing" />
</form>

