<?php include_once("header.php")?>
<?php require("utilities.php")?>
<?php include_once('db_con/db_li.php')?>
<?php include_once('auction_functions.php')?>


<?php
  // Get info from the URL:
  $item_id = $_GET['item_id'];
  //check whether itemid exists in database
  $isvalidauction = validauction($item_id);
  if($isvalidauction){

      $auctiondetails = getauctiondetails($item_id);
      $title = $auctiondetails['title'];
      $description = $auctiondetails['description'];
      $current_price = $auctiondetails['current_price'];
      $num_bids = $auctiondetails['num_bids'];
      $end_time = $auctiondetails['end_time'];
      $starting_price = $auctiondetails['starting_price'];
      $seller = $auctiondetails['seller_id'];
      $selleremail = getselleremail($seller);

      // Calculate time to auction end:
      $now = new DateTime();

      if ($now < $end_time) {
          $time_to_end = date_diff($now, $end_time);
          $time_remaining = ' (in ' . display_time_remaining($time_to_end) . ')';
      }

      $auctionstatus = getauctionstatus($item_id);
  }
  else{
      echo "This auction does not exist!";
  }
  // TODO: Note: Auctions that have ended may pull a different set of data,
  //       like whether the auction ended in a sale or was cancelled due
  //       to lack of high-enough bids. Or maybe not.
  if (isset($_SESSION['logged_in']) and $_SESSION['account_type'] == 'buyer'){
        $user_id = $_SESSION['user_id'];
        $buyer_id = $_SESSION['buyer_id'];
        $has_session = true;
        $watching = iswatchingauction($buyer_id,$item_id);
        $usermaxbid = buyermaxbidonauction($item_id);
    }
  else{
      $has_session = false;
      $watching = false;
  }
?>


<div class="container">
    <?php if ($isvalidauction):?>
<div class="row"> <!-- Row #1 with auction title + watch button -->
  <div class="col-sm-8"> <!-- Left col -->
    <h2 class="my-3"><?php echo($title); ?></h2>
  </div>
  <div class="col-sm-4 align-self-center"> <!-- Right col -->
<?php
  /* The following watchlist functionality uses JavaScript, but could
     just as easily use PHP as in other places in the code */
  if ($now < $end_time):
?>
    <div id="watch_nowatch" <?php if ($has_session && $watching) echo('style="display: none"');?> >
        <?php if(isset($buyer_id)):?>
            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="addToWatchlist()">+ Add to watchlist</button>
        <?php endif /* Shows no button otherwise */ ?>
    </div>
    <div id="watch_watching" <?php if (!$has_session || !$watching) echo('style="display: none"');?> >
      <button type="button" class="btn btn-success btn-sm" disabled>Watching</button>
      <button type="button" class="btn btn-danger btn-sm" onclick="removeFromWatchlist()">Remove watch</button>
    </div>
<?php endif /* Print nothing otherwise */ ?>
  </div>
</div>

<div class="row"> <!-- Row #2 with auction description + bidding info -->
  <div class="col-sm-8"> <!-- Left col with item info -->

    <div class="itemDescription">
          Seller: <?php echo($selleremail); ?>
    </div><br/>
    <div class="itemDescription">
        <h5>Item Description</h5>
        <?php echo($description); ?>
    </div>
    <br/>
    <div class="itemDescription">
        <h5>Auction Status</h5>
        <?php echo($auctionstatus); ?>
    </div>
    <br/><br/><br/>
    <div>
        <h5>Bidding History</h5>
        <?php printbidsforauction($item_id);?>
    </div>


  </div>

  <div class="col-sm-4"> <!-- Right col with bidding info -->

    <p>
<?php if ($now > $end_time): ?>
     This auction ended <?php echo(date_format($end_time, 'j M H:i')) ?>
     <!-- TODO: Print the result of the auction here? -->
<?php else: ?>
     Auction ends <?php echo(date_format($end_time, 'j M H:i') . $time_remaining) ?></p>
    <?php if(isset($usermaxbid) and $usermaxbid > 0):?>
        <p class="lead">Your highest bid: £<?php echo(number_format($usermaxbid, 2)) ?></p>
    <?php endif ?>
      <p class="lead">Current highest bid: £<?php echo(number_format($current_price, 2)) ?></p>
    <p class="lead">Starting Price: £<?php echo(number_format($starting_price, 2)) ?></p>

    <!-- Bidding form -->
    <?php if(isset($buyer_id)): ?>
        <form method="POST" action="place_bid.php">
          <input type="hidden" name="item_id" value="<?php echo $item_id;?>">
          <div class="input-group">
            <div class="input-group-prepend">
              <span class="input-group-text">£</span>
            </div>
            <input type="number" class="form-control" id="bid" name="bid">
          </div>
          <button type="submit" class="btn btn-primary form-control">Place bid</button>
        </form>
    <?php endif ?>
<?php endif ?>


  </div> <!-- End of right col with bidding info -->

</div> <!-- End of row #2 -->
    <?php endif /* Print nothing otherwise */ ?>



<?php include_once("footer.php")?>


<script> 
// JavaScript functions: addToWatchlist and removeFromWatchlist. Add bid

function addToWatchlist(button) {
  console.log("These print statements are helpful for debugging btw");

  // This performs an asynchronous call to a PHP function using POST method.
  // Sends item ID as an argument to that function.
  $.ajax('watchlist_funcs.php', {
    type: "POST",
    data: {functionname: 'add_to_watchlist', arguments: [<?php echo($item_id);?>,<?php echo($buyer_id);?>]},

    success: 
      function (obj, textstatus) {
        // Callback function for when call is successful and returns obj
        console.log("Success");
        var objT = obj.trim();
        console.log(objT);
 
        if (objT == "success") {
          $("#watch_nowatch").hide();
          $("#watch_watching").show();
        }
        else {
          var mydiv = document.getElementById("watch_nowatch");
          mydiv.appendChild(document.createElement("br"));
          mydiv.appendChild(document.createTextNode("Add to watch failed. Try again later."));
        }
      },

    error:
      function (obj, textstatus) {
        console.log("Error");
      }
  }); // End of AJAX call

} // End of addToWatchlist func

function removeFromWatchlist(button) {
  // This performs an asynchronous call to a PHP function using POST method.
  // Sends item ID as an argument to that function.
  $.ajax('watchlist_funcs.php', {
    type: "POST",
    data: {functionname: 'remove_from_watchlist', arguments: [<?php echo($item_id);?>,<?php echo($buyer_id);?>]},

    success: 
      function (obj, textstatus) {
        // Callback function for when call is successful and returns obj
        console.log("Success");
        console.log(obj);
        var objT = obj.trim();
 
        if (objT == "success") {
          $("#watch_watching").hide();
          $("#watch_nowatch").show();
        }
        else {
          var mydiv = document.getElementById("watch_watching");
          mydiv.appendChild(document.createElement("br"));
          mydiv.appendChild(document.createTextNode("Watch removal failed. Try again later."));
        }
      },

    error:
      function (obj, textstatus) {
        console.log("Error");
      }
  }); // End of AJAX call
} // End of removeFromWatchlist func
</script>