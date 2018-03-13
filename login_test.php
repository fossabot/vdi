<?php
// Check if $_SESSION or $_COOKIE already set
if( isset($_SESSION['userid']) ){
  header('Location: home.php');
  exit;
} elseif ( isset($_COOKIE['rememberme'] )) {
  // Decrypt cookie variable value
  $userid = decryptCookie($_COOKIE['rememberme']);

  $sql_query = "select count(*) as cntUser,id from users where id='".$userid."'";
  $result = mysqli_query($con,$sql_query);
  $row = mysqli_fetch_array($result);

  $count = $row['cntUser'];

  if( $count > 0 ){
    $_SESSION['userid'] = $userid;
    header('Location: home.php');
    exit;
  }
}

// Encrypt cookie
function encryptCookie( $value ) {
  $key = 'eY2ZWcKG6xjbdFXTLyBbGf2kRnHTd0uNKPlqj9GGW3iMcJn0YaVV85YxZ87eFVF';
  $newvalue = base64_encode( mcrypt_encrypt( MCRYPT_RIJNDAEL_256, md5( $key ), $value, MCRYPT_MODE_CBC, md5( md5( $key ) ) ) );
  return( $newvalue );
}

// Decrypt cookie
function decryptCookie( $value ) {
  $key = 'eY2ZWcKG6xjbdFXTLyBbGf2kRnHTd0uNKPlqj9GGW3iMcJn0YaVV85YxZ87eFVF';
  $newvalue = rtrim( mcrypt_decrypt( MCRYPT_RIJNDAEL_256, md5( $key ), base64_decode( $value ), MCRYPT_MODE_CBC, md5( md5( $key ) ) ), "\0");
  return( $newvalue );
}

// On submit
if(isset($_POST['but_submit'])){

 $uname = mysqli_real_escape_string($con,$_POST['txt_uname']);
 $password = mysqli_real_escape_string($con,$_POST['txt_pwd']);

 if ($uname != "" && $password != ""){

  $sql_query = "select count(*) as cntUser,id from users where username='".$uname."' and password='".$password."'";
  $result = mysqli_query($con,$sql_query);
  $row = mysqli_fetch_array($result);

  $count = $row['cntUser'];

  if($count > 0){
   $userid = $row['id'];
   if( isset($_POST['rememberme']) ){

    // Set cookie variables
    $days = 30;
    $value = encryptCookie($userid);
    setcookie ("rememberme",$value,time()+ ($days * 24 * 60 * 60 * 1000));
   }

   $_SESSION['userid'] = $userid;
   header('Location: home.php');
   exit;
  }else{
   echo "Invalid username and password";
  }

 }

}
?>
