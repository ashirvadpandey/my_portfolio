
<?php
//get data from form  

$name = $_POST['name'];
$email= $_POST['email'];
$project= $_POST['project'];
$message= $_POST['message'];
$to = "2002389.cse.cec@cgc.edu.in";
$subject = "Mail From portfolio";
$txt ="Name = ". $name . "\r\n  Email = " . $email ."\r\n project = ". $project . "\r\n Message =" . $message;
$headers = "From: noreply@yoursite.com" . "\r\n" ."CC: somebodyelse@example.com";
//if($email!=NULL){
mail($to,$subject,$txt,$headers);
//}
//redirect
header("Location:thankyou.html");
?>
