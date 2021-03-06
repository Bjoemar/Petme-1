<?php require_once 'template.php'; ?>

<?php function getTitle(){
	echo "PETME | HOME";
} ?>

<?php function getContent() { ?>

	<div class="container py-2">
		<?php 
			$bg = '';
			$image = str_replace("s96-c","s200",$_SESSION['user_picture']);
			if ($_SESSION['user_gender'] == 'male') {
				$bg = "background: url(assets/images/icon/male.svg);";
			} else if ($_SESSION['user_gender'] == 'female') {
				$bg = "background: url(assets/images/icon/female.svg);";
			} else {
				$bg = "background: url(assets/images/icon/unknown.svg);";
			}
		 ?>
		 <?php echo $_SESSION['user_gender'] ?>
		 <div 
		 	class="profile-placeholder" 
		 	style="<?php echo $bg; ?> 
		 		background-size: contain; 
		 		max-width: 200px;
		 		background-repeat: no-repeat;
		 		border:2px solid #e5a62d;
		 		border-radius: 100px;
		 		margin: auto;
		 		height: 200px;">
				<img src="<?php echo $image ?>" class="img-fluid" style="border-radius: 100px;">
		 </div>
		 <div class="text-center py-2">
		 	<h2><?php echo $_SESSION['user_first_name'] ?> <?php echo $_SESSION['user_last_name'] ?></h2>
		 </div>

		 <h3>Pet's you loved!</h3>
		 <hr>
		 <div class="row">
		 	
		
			 <?php 
			 	require 'lib/connection.php';
			 	$user_ID = $_SESSION['OAuthID'];
			 	$sql = "SELECT * FROM userlikedpet WHERE userID = '$user_ID'";
			 	$result = mysqli_query($conn,$sql) or die(mysqli_error($conn));

			 	while ($row = mysqli_fetch_assoc($result)):
			 		$petID = $row['petID'];
			 		$petInfo = "SELECT * FROM likedpet WHERE petID = '$petID'";
			 		$info = mysqli_query($conn,$petInfo) or die(mysqli_error($conn));

			 		$pet = mysqli_fetch_assoc($info);
			 		$pet_unserialized = unserialize($pet['petObject']);
			 		 ?>
			 		<div class="col-md-3 mt-3">
			 			<div class="card" style="width: 100%;">
			 			  	<img class="card-img-top" src="<?php echo $pet_unserialized->animal->primary_photo_cropped->full ?>" alt="Card image cap" style="height: 400px; object-fit: cover;">
			 			  	<div class="card-body">
			 			    	<p class="card-text"><?php echo $pet['petName'] ?> <small class="float-right mt-1"> <?php echo $pet['petLiked'] ?> <i class="icofont-heart-alt" style="color: #e7470c ;"></i></small></p>
			 			  	</div>
			 			</div>
			 		</div>
			 		
			 <?php	endwhile; ?>
		 </div>
	</div>

	<?php require_once 'partials/footer/footer.php' ?>
<?php } ?>

<script type="text/javascript">
    $('.header-menu').find('li').eq(0).addClass('active')
</script>