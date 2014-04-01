<?php
//place this code in the pages, which you need to authenticate
session_start();
if(!session_is_registered(username)){
header("location:index.php");
}
$username = $_SESSION['username'];
?>

<?php include('header.php'); ?>

<?php
$update = $_GET['update'];
$full_name = $_POST['full_name'];
$full_name = strip_tags($full_name);
$location = $_POST['location'];
$location = strip_tags($location);
$gender = $_POST['gender'];

if($update == 1 && !empty($_POST)) // Checks if the form is submitted or not
{
$success_update = mysql_query("UPDATE users SET fullname='$full_name', location='$location', gender='$gender' WHERE username='$username' ");
if($success_update) {
echo '
<div class="alert alert-success">
Account Successfully updated!
</div>
';
}

else {
echo '
<div class="alert">
  <button type="button" class="close" data-dismiss="alert">&times;</button>
Failed to update
</div>
';


}

}

$document_get = mysql_query("SELECT * FROM users WHERE username='$username'");
$match_value = mysql_fetch_array($document_get);
$fullname = $match_value['fullname'];
$location = $match_value['location'];
$gender = $match_value['gender'];

?>
<br/>

 <div style="float:right"> <a class="btn btn-info" href="dashboard.php" > Account </a>  <a class="btn btn-danger logout" href="logout.php" > Logout</a> </div>

 <fieldset>
    <legend>Welcome <?php echo $username; ?>, </legend>

	<br/>
	<br/>
<form action="settings.php?update=1" method="post" name="myForm" onsubmit="return(validate());">
  <fieldset>
    <legend>Settings</legend>

	<label>Full Name *</label>
    <input name="full_name" type="text" placeholder="Type something…" value="<?php echo $fullname; ?>" >
	<br/>
	<label>Location </label>
    <input name="location" type="text" placeholder="Type something…" value="<?php echo $location; ?>">
	<br/>
	<label>Gender </label>
    <select name="gender">
  <option <?php if($gender == Male) echo 'selected'; ?> >Male</option>
  <option <?php if($gender == Female) echo 'selected'; ?> >Female</option>
</select>

	<br/>
    <button type="submit" class="btn">Update</button>
  </fieldset>
</form>
 </fieldset>


 <!--
 Similarly you can also add password change field, I suggest to create separate form for this,
 just make sure your encrypt the password using md5 before you save to database.

 -->


 <script>

 function validate()
{


   if( document.myForm.full_name.value == "" )
   {
     alert( "Please provide your full name!" );
     document.myForm.full_name.focus() ;
     return false;
   }

   return( true );
}


 $('.logout').click(function(){
    return confirm("Are you sure you want to Logout?");
})
</script>
<?php include('footer.php'); ?>
