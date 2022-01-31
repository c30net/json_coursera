<?php session_start();
require_once "pdo.php";

// if ( !isset($_SESSION['email']) ) {
//     $_SESSION['error'] = 'Could not load profile';
//     header( 'Location: index.php' ) ;
//     return;
//   }


  $_GET['profile_id'];



  $stmt = $pdo->prepare("SELECT first_name, last_name, email, headline, summary FROM profile where profile_id = :xyz");
  $stmt->execute(array(":xyz" => $_GET['profile_id']));
  $row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
    $_SESSION['error'] = 'Bad value for autos_id';
    header( 'Location: index.php' ) ;
    return;
}

$first_name = htmlentities($row['first_name']);
$last_name = htmlentities($row['last_name']);
$email = htmlentities($row['email']);
$headline = htmlentities($row['headline']);
$summary = htmlentities($row['summary']);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>c30 net c30net 393fc2d1</title>
</head>
<body>


<h1>Profile information</h1>

<p>First Name: <?php echo $first_name; ?></p>
<p>Last Name: <?php echo $last_name; ?></p>
<p>Email: <?php echo $email; ?></p>
<p>Headline: <?php echo $headline; ?></p>
<p>Summary: <?php echo $summary; ?></p>








<p>Education</p>
<ul>
<?php
$stmt4 = $pdo->prepare('SELECT * FROM education WHERE profile_id = :prof ORDER BY rank');
$stmt4 -> execute(array(':prof' => htmlentities($_GET['profile_id'])));
$rows4 = $stmt4->fetchAll();
foreach($rows4 as $row4){


        $instituion_id = $row4['institution_id'];

        $stmt5 = $pdo->prepare('SELECT * FROM institution WHERE institution_id = :inst');
        $stmt5 -> execute(array(':inst' => $instituion_id));
        $rows5 = $stmt5->fetchAll();
        foreach($rows5 as $row5){
            echo '<li>';
            echo $row4['year'];
            echo ' : ';
            echo $row5['name'];
            echo '</li>';
        }
}
?>
</ul>




<p>Positions</p>
<ul>

<?php



$stmt3 = $pdo->prepare('SELECT * FROM Position WHERE profile_id = :prof ORDER BY rank');
$stmt3 -> execute(array(':prof' => htmlentities($_GET['profile_id'])));
$rows3 = $stmt3->fetchAll();

foreach($rows3 as $row3){
    echo '<li>';
    echo $row3['year'];
    echo ' : ';
    echo $row3['description'];
    echo '</li>';
}
?>
</ul>


<a href="index.php">Done</a>
    
</body>
</html>