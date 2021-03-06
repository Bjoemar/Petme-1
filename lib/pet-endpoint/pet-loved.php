<?php 
	
	require '../connection.php';
	session_start();
	// If client is login nothing will happen if not login
	if (isset($_COOKIE['API_TOKEN'])) {
		if (isset($_SESSION['access_token'])):

			// PET ID GET FROM THE API
			$petID = $_POST['petID'];
			$userID = $_SESSION['OAuthID'];

			// Check if this user is already liked the pet
			$userCheck = "SELECT * FROM userlikedpet WHERE userID = '$userID' AND petID = '$petID'";
			$match = mysqli_query($conn,$userCheck);
			$matchCount = mysqli_num_rows($match);
			if (mysqli_num_rows($match) == 0) {

				// Check if the pet info is already been saved
				$checkPetID = "SELECT * FROM likedPet WHERE petID = '$petID'";
				$result = mysqli_query($conn,$checkPetID) or die(mysqli_error($conn));
				$resCount = mysqli_num_rows($result);

				if ($resCount > 0) {
					// If the pet id exist incremment the liked value

					// Get Result if exist
					$res = mysqli_fetch_assoc($result);

					// Get the last count of LovedCount Table
					$lovedCount = $res['petLiked'] + 0;

					// Increment It by one from the user click
					$lovedCount++;

					$lovedAdd = "UPDATE likedPet SET petLiked = '$lovedCount' WHERE petID = '$petID'";
					mysqli_query($conn,$lovedAdd) or die(mysqli_error($conn));

					// Save User and pet in like table
					userLikedPet($userID,$petID,$conn);

				} else {
					// Fetch Animal Information Using PETID From the API
					$pet = petInfo($petID);

					// Create a common values that not serialize so it will get more easily later
					$petName = $pet->animal->name;

					// Save the pet infor as object using Serialize

					$petObject = serialize($pet);

					// Set the loved count to 1

					$lovedCount = 1;

					$savedPet = "INSERT INTO likedPet(petID,petName,petObject,petLiked) VALUES('$petID','$petName','$petObject','$lovedCount')";

					mysqli_query($conn,$savedPet) or die(mysqli_error($conn));

					// Save User and pet in like table
					userLikedPet($userID,$petID,$conn);
				}

			};

		else:
			echo "401";
		endif;
	} else {
		echo "401";
	}



	// Saving the user and pet in the liked table
	function userLikedPet($userID,$petID,$conn){
		$savedPet = "INSERT INTO userlikedpet(petID,userID) VALUES('$petID','$userID')";
		mysqli_query($conn,$savedPet) or die(mysqli_error($conn));
	}

	// Function for fetching data in the API

	function petInfo($id){
	    $curl = curl_init();
	    $url = 'https://api.petfinder.com/v2/animals/'.$id;
	    $token = $_COOKIE['API_TOKEN'];
	    curl_setopt($curl, CURLOPT_URL, $url);
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($curl, CURLOPT_HTTPHEADER,[
	        'Authorization : Bearer '.$token,
	    ]);
	    $result = curl_exec($curl);
	    curl_close($curl);
	    $animals = json_decode($result);
	    return $animals;
	}

	function createToken(){
		
		$curl = curl_init();
		$url = 'https://api.petfinder.com/v2/oauth2/token';

		$auth_data = array(
			'client_id' => 'nHVlAYbtuuA5fPyERQzUDubsnWA0WZnvq1IIwaGVvKOUnLDqBB',
			'client_secret' => 'cXWIL2kkNo0Z4Z7JUdCbWLayRu3DRRkuq8Jam6uO',
			'grant_type' => 'client_credentials',
		);

		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $auth_data);
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		$result = curl_exec($curl);
		if(!$result){die("Connection Failure");}
		curl_close($curl);
		$token = json_decode($result);
		setcookie("API_TOKEN", $token->access_token, time()+3600);
	}

 ?>