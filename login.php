<?php session_start();
require_once "pdo.php";

if ( isset($_POST['cancel'] ) ) {
    header("Location: index.php");
    return;
}

$salt = 'XyZzy12*_';
$stored_hash = '1a52e17fa899cf40fb04cfc42e6352f1';  // Pw is php123

$failure = false; 

if(isset($_POST['submit']))
{

if ( isset($_POST['email']) && isset($_POST['pass']) ) {
    $who = htmlentities($_POST['email']);
    $pass = htmlentities($_POST['pass']);
    if ( strlen($who) <= 1 || strlen($pass) < 1 ) {
        $failure = "User name and password are required";
    } elseif(!strpos($who, '@')){
        $failure = 'Email must have an at-sign (@)';
    } else {

        $check = hash('md5', $salt.$_POST['pass']);
        $stmt = $pdo->prepare('SELECT user_id, name FROM users
        WHERE email = :em AND password = :pw');
        $stmt->execute(array( ':em' => $_POST['email'], ':pw' => $check));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ( $row !== false ) {

            $_SESSION['name'] = $row['name'];
   
            $_SESSION['user_id'] = $row['user_id'];

            $_SESSION['email'] = $_POST['email'];
   
            // Redirect the browser to index.php
   
            header("Location: index.php");
   
            return;







        // $check = hash('md5', $salt.$pass);
        // if ( $check == $stored_hash ) {
        //     // Redirect the browser to view.php
        //     error_log("Login success ".$_POST['email']);
        //     $_SESSION['email'] = $_POST['email'];
        //     header("Location: index.php");
        //     return;

        } else {
            $failure = "Incorrect password";
            error_log("Login fail ".$_POST['email']." $check");
        }
    }
}

if($failure !== false)
{
    $_SESSION['error'] = $failure;
    header("Location: login.php");
    return;
}

}

// Fall through into the View
?>
<!DOCTYPE html>
<html>

<head>
    <title>c30 net Login Page c30 net c30net 393fc2d1</title>
</head>

<body>
    <div>
        <h1>Please Log In</h1>
        <?php


if ( isset($_SESSION['error']) ) {
    echo('<p style="color: red;">'.htmlentities($_SESSION['error'])."</p>\n");
    unset($_SESSION['error']);
  }
?>
        <form method="POST">
            <label for="nam">Email</label>
            <input type="text" name="email" id="nam"><br />
            <br>
            <label for="id_1723">Password</label>
            <input type="text" name="pass" id="id_1723"><br />
            <br>
            <input type="submit" name="submit" value="Log In" onclick="doValidate()">
            <input type="submit" name="cancel" value="Cancel">
        </form>
        <p>
            For a password hint, view source and find a password hint
            in the HTML comments.
            <!-- Hint:

            The password is the three character name of the
            programming language used in this class (all lower case)
            followed by 123. -->
        </p>

        <script>
	function doValidate(){
		console.log('Validateing ...');
		try{

		addr = document.getElementById('nam').value;
    pw = document.getElementById('id_1723').value;
    console.log("Validating addr =" + addr + " pw=" + pw);
    if(addr == null || addr == "" || pw == null || pw == ""){
      alert("Both fields must be filled out");
      return false;
    }

    if(addr.indexOf('@') == -1){
      alert("Invalid email Address");
      return false;
    }
    return true;
  } catch(e){
    return false;
  }
	return false;	
	}

	</script>
    </div>
</body>