 <?php
 //Configuration fof our PHP server
 set_time_limit(0);
 ini_set('default_socket_timeout', 300);
 session_start();

//Make constants using define
 define('clientID', '4abc400ab6094efab9bef26bc3ee0b85');
 define('clientSecret', 'a04d624deca74e47a6dfa3df30807dd4');
 define('redirectURI', 'http://localhost/jayyapi/index.php');
 define('ImageDirectory','pics/');

//fucntion that is going to connect to instagram
 function connectToInstagram($url){
 	$ch = curl_init();

 	curl_setopt_array($ch, array(
 		CURLOPT_URL => $url,
 		CURLOPT_RETURNTRANSFER => true,
 		CURLOPT_SSL_VERIFYPEER => false,
 		CURLOPT_SSL_VERIFYHOST => 2,
 		));
 	$result = curl_exec($ch);
 	curl_close($ch);
 	return $result;
 }
//function to get useerID cause userName doesn't allow us to get pictures
 function getUserID($userName){
 	$url = 'https://api.instagram.com/v1/users/search?q=\"j.cardo\"&client_id='.clientID;
 	$instagramInfo = connectToInstagram($url);
 	$results = json_decode($instagramInfo, true);

 	//print_r($results);
 	return $results['data'][0]['id'];
 }

function printImages($userID)
{
	$url = 'https://api.instagram.com/v1/users/' . $userID . '/media/recent?client_id='.clientID . '&count=5';
	$instagramInfo = connectToInstagram($url);
	$results = json_decode($instagramInfo, true);

	//Parse through thet information one by one
	foreach($results['data'] as $items)
	 {
	 	$image_url = $items['images']['low_resolution']['url']; //go through all of the results and give back the url of those pictures because we want to save it in the php server.
	 	echo '<img src=" '. $image_url .' "/><br/>';
	 }
}

function savePictures($image_url){
echo $image_url.'<br>';
$filename = basename($image_url);
echo $filename . '<br>';

$destination = ImageDirectory . $filename;
file_put_contents($destination, file_get_contents($image_url));
}

if (isset($_GET['code'])){
	$code = $_GET['code'];
	$url = 'https://api.instagram.com/oauth/access_token';
	$access_token_settings = array('client_id' => clientID,
		'client_secret' => clientSecret,
		'grant_type'=>'authorization_code',
		'redirect_uri'=> redirectURI,
		'code'=> $code
		);
	//cURL is what we use in PHP, it's a library calls to other api's
	$curl = curl_init($url); 
	curl_setopt($curl, CURLOPT_POST, true);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $access_token_settings);  //setting the POSTFIELDS to the array set up that we have created.
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);


$result = curl_exec($curl);
curl_close($curl);	

$results = json_decode($result, true);

//print_r($results);

$userName = $results['user']['username'];


$userID = getUserID($userName);


printImages($userID);
}
else {
 ?>

 <!DOCTYPE html>
 <html>
 <head>
 <meta charset="utf-8">
 <meta name="description" content="">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 	<title>Instagram Project</title>
 	<link rel="stylesheet" type="text/css" href="css/style.css">
 	<link rel="author" href="humans.txt">
 </head>
 <body>
 <style>
body {
    background-color: gray;
      background-image: url("https://www.google.com/url?sa=i&rct=j&q=&esrc=s&source=images&cd=&cad=rja&uact=8&ved=0CAcQjRw&url=http%3A%2F%2Fmoshlab.com%2Fvfl-wolfsburg-logo-germany-football-club-wallpaper-sport-hd-picture-33309992093%2F&ei=QQ1WVbCdH5K2yATY34HwBg&psig=AFQjCNHqGWUz0ulJchSTI8qbL5zFsfREmw&ust=1431789226490349");
  background-position: 50% 50%;
  background-repeat: no-repeat;
}
</style>
 <!-- Creating a login for people to go and give approval for ourr web app to access their Instagram Account 
 After getting approval we are now going to have the information so that we can play with it.
 -->
 <a href="https:api.instagram.com/oauth/authorize/?client_id=<?php echo clientID; ?>&redirect_uri=<?php echo redirectURI ?>&response_type=code">Login</a>
 <script src="js/main.js"></script>
 </body>
 </html>
 <?php

}
?>