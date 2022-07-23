<?php

session_start();

// initializing variables
$email    = "";
$errors = array(); 

// connect to the database
$db = mysqli_connect('localhost', 'root', '', 'cse482_project');

// REGISTER USER
if (isset($_POST['sign_up'])) {
 //Generating Donor_Id  
    $n=6;
function getDonorId($n) {
    $characters = '0123456789';
    $randomString = '';
  
    for ($i = 0; $i < $n; $i++) {
        $index = rand(0, strlen($characters) - 1);
        $randomString .= $characters[$index];
    }
  
    return $randomString;
}
  $donor_id = getDonorId($n); 
  // receive all input values from the form
  $fullName = mysqli_real_escape_string($db, $_POST['name']);
  $email = mysqli_real_escape_string($db, $_POST['email']);
  $contactNo = mysqli_real_escape_string($db, $_POST['contact']); 
  $address = mysqli_real_escape_string($db, $_POST['address']);
  $password = mysqli_real_escape_string($db, $_POST['password']);
  $repeat_password = mysqli_real_escape_string($db, $_POST['psw-repeat']);
  $encrypted_password = password_hash($password, PASSWORD_DEFAULT); 
   

  // first check the database to make sure 
  // a user does not already exist with the same username and/or email
  $user_check_query = "SELECT * FROM `donor_list` WHERE `Donor_Id` = '$donor_id' OR `Email` ='$email' OR `Contact_No` ='$contactNo' LIMIT 1";
  $result = mysqli_query($db, $user_check_query);
  $user = mysqli_fetch_assoc($result);
  
  if ($user) { // if user exists
    if ($user['Contact_No'] === $contactNo) {
      array_push($errors, "Contact No. already exists");?>
      <html><body><h1>Contact No. already exists</h1></body>
      <a href="donor_signup.html"><-Back</a> 
      </html><?php
    }

    if ($user['Email'] === $email) {
      array_push($errors, "Email already exists");?>
      <html><body><h1>Email already exists</h1></body>
      <a href="donor_signup.html"><-Back</a> 
      </html><?php
    }  

    if ($user['Donor_Id'] === $donor_id){
      getDonorId(6);
    }

  }

  if ($password !== $repeat_password){ 
       array_push($errors, "Password is re-written correctly");
       ?>
       <html><body><h1>Password is not re-written correctly</h1></body>
       <a href="donor_signup.html"><-Back</a> 
       </html>
       <?php 
    }  

  // Finally, register user if there are no errors in the form
  if (count($errors) == 0) {
  
      $insert = "INSERT INTO `donor_list`(`Donor_Id`, `Full_Name`, `Contact_No`, `Address`, `Email`, `Password`) 
      VALUES (?, ?, ?, ?, ?, ?)";
      $stmnt = $db->prepare($insert);
      $stmnt->bind_param("isisss", $donor_id, $fullName, $contactNo, $address, $email, $encrypted_password); 
      $stmnt->execute(); 
      $stmnt->close();
  	
    ?>
    <html><body><h1>Registration Completed Successfully</h1></body>
    <br><a href="login.html">You can login now.</a></html>
        <?php
        
    }
}

// LOGIN USER 
if (isset($_POST['login'])) {
  $email = mysqli_real_escape_string($db, $_POST['email']);
  $password = mysqli_real_escape_string($db, $_POST['password']);
  
  if (empty($email)) {
    array_push($errors, "Email is required");
  }
  if (empty($password)) {
    array_push($errors, "Password is required");
  }

  if (count($errors) == 0) {
    
    $query = "SELECT * FROM `donor_list` WHERE `Email` = '$email'"; 
    $results = mysqli_query($db, $query);
    $user = mysqli_fetch_assoc($results);
    $encrypted_password = $user['Password'];
    $verify = password_verify($password, $encrypted_password);

    if ((mysqli_num_rows($results) == 1) && ($verify)) { 
        $_SESSION['id'] = $user['Donor_Id'];

         ?>
    <html><script>alert("Login Successful");</script></html>;  
    <?php 

        header('location:user_profile.php');
            
    }else {
      array_push($errors, "Wrong username/password combination");
      ?>

      <html><body><h1>Wrong username/password combination</h1></body>
      <a href="login.html"><-Back</a> 
      </html>
      <?php 
    }
  }
}


//LOGOUT from Account
if (isset($_POST['logout'])) {
  $_SESSION['id'] = $email;
  header('location:logout.php');
}

    ?>
       