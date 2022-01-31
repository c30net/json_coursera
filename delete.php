<?php session_start();
//delete page
require_once "pdo.php";


if ( ! isset($_SESSION['email']) ) {
  die("Not logged in");
}


if ( isset($_POST['delete']) && isset($_POST['user_id']) ) {
    $sql = "DELETE FROM users WHERE user_id = :zip";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(':zip' => $_POST['user_id']));


    $_SESSION['success'] = 'Record deleted';
    header( 'Location: index.php' ) ;
    return;
}

// Guardian: Make sure that profile_id is present
if ( ! isset($_GET['profile_id']) ) {
  $_SESSION['error'] = "Missing profile_id";
  header('Location: index.php');
  return;
}

$stmt = $pdo->prepare("SELECT first_name, last_name, profile_id,user_id FROM profile where profile_id = :xyz");
$stmt->execute(array(":xyz" => $_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
    $_SESSION['error'] = 'Bad value for profile_id';
    header( 'Location: index.php' ) ;
    return;
}

?>
<h1>Deleting Profile</h1>

<?php

echo "First Name: ".htmlentities($row['first_name']);

echo '<br>';

echo "Last Name: ".htmlentities($row['last_name']);


?>


  

<form method="post">
<input type="hidden" name="user_id" value="<?= $row['user_id'] ?>">
<input type="submit" value="Delete" name="delete">
<a href="index.php">Cancel</a>
</form>