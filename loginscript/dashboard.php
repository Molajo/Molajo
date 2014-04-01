<?php
//place this code on top of all the pages which you need to authenticate

//--- Authenticate code begins here ---
session_start();
//checks if the login session is true
if(!session_is_registered(username)){
header("location:index.php");
}
$username = $_SESSION['username'];

// --- Authenticate code ends here ---
?>

<?php include('header.php'); ?>

<?php
$document_get = mysql_query("SELECT * FROM users WHERE username='$username'");
$match_value = mysql_fetch_array($document_get);
$fullname = $match_value['fullname'];
$location = $match_value['location'];
$gender = $match_value['gender'];
?>
<br/>

 <div style="float:right"> <a class="btn btn-info" href="settings.php" > Settings </a>  <a class="btn btn-danger logout" href="logout.php" > Logout</a> </div>

 <fieldset>
    <legend>Welcome <?php echo $username; ?>, </legend>

	<br/>
	<br/>
<table class="table table-hover" style="border:0;width:50%">
<tr> <td> <b> Full Name:  </b> </td> <td> <?php echo $fullname; ?></td></tr>
<tr><td>  <b> Location:  </b> </td> <td> <?php echo $location; ?></td></tr>
<tr><td>  <b> Gender:  </b> </td> <td> <?php echo $gender; ?></td></tr>

</tr>

</table>
 </fieldset>

 <script>
 $('.logout').click(function(){
    return confirm("Are you sure you want to Logout?");
})
</script>
<?php include('footer.php'); ?>
