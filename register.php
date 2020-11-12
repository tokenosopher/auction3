 <?php include_once("header.php")?>

<div class="container">
<h2 class="my-3">Register new account</h2>

<!-- Create auction form -->
<form method="POST" action="process_registration.php">
  <div class="form-group row">
    <label for="accountType" class="col-sm-2 col-form-label text-right">Registering as a:</label>
	<div class="col-sm-10">
	  <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="accountType" id="accountBuyer" value="buyer" checked>
        <label class="form-check-label" for="accountBuyer">Buyer</label>
      </div>
      <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="accountType" id="accountSeller" value="seller">
        <label class="form-check-label" for="accountSeller">Seller</label>
      </div>
      <small id="accountTypeHelp" class="form-text-inline text-muted"><span class="text-danger">* Required.</span></small>
	</div>
  </div>
  <div class="form-group row">
        <label for="first_name" class="col-sm-2 col-form-label text-right">First Name</label>
    <div class="col-sm-10">
      <input type="text" name = "first_name" pattern="[A-Za-z]{1,32}" class="form-control" id="first_name" placeholder="First name">
      <small id="emailHelp" class="form-text text-muted"><span class="text-muted">* Optional.</span></small>
    </div>
  </div>
  <div class="form-group row">
        <label for="first_name" class="col-sm-2 col-form-label text-right">Family Name</label>
        <div class="col-sm-10">
            <input type="text" name = "family_name" pattern="[A-Za-z]{1,32}" title = "Only letters are allowed" class="form-control" id="family_name" placeholder="Family name">
            <small id="emailHelp" class="form-text text-muted"><span class="text-muted">* Optional.</span></small>
        </div>
    </div>
  <div class="form-group row">
    <label for="email" class="col-sm-2 col-form-label text-right">Email</label>
	<div class="col-sm-10">
      <input type="email" name = "email" class="form-control" id="email" placeholder="Email">
      <small id="emailHelp" class="form-text text-muted"><span class="text-danger">* Required.</span></small>
	</div>
  </div>
  <div class="form-group row">
    <label for="password" class="col-sm-2  col-form-label text-right">Password</label>
    <div class="col-sm-10">
      <input type="password" name = "Password" pattern=".{6,}" required title="6 characters minimum"    class="form-control" id="password" placeholder="Password">
      <small id="passwordHelp" class="form-text text-muted"><span class="text-danger">* Required.</span></small>
    </div>
  </div>
  <div class="form-group row">
    <label for="passwordConfirmation" class="col-sm-2 col-form-label text-right">Repeat password</label>
    <div class="col-sm-10">
      <input type="password" name ="passwordConfirmation" class="form-control" id="passwordConfirmation" placeholder="Enter password once more">
      <small id="passwordConfirmationHelp" class="form-text text-muted"><span class="text-danger">* Required.</span></small>
    </div>
  </div>
  <div class="form-group row">
    <button type="submit" class="btn btn-primary form-control">Register</button>
  </div>
</form>

<div class="text-center">Already have an account? <a href="" data-toggle="modal" data-target="#loginModal">Login</a>

</div>

<?php include_once("footer.php")?>