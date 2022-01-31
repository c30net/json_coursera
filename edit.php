<?php session_start();
require_once "pdo.php";


if ( ! isset($_SESSION['email']) ) {
    die("Not logged in");
  }

if ( isset($_POST['first_name']) && isset($_POST['last_name'])
     && isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary']) && isset($_POST['profile_id']) ) {

    
    if ( strlen($_POST['first_name']) < 1 || strlen($_POST['last_name']) < 1 || strlen($_POST['email']) < 1 ||  strlen($_POST['headline']) < 1 || strlen($_POST['summary']) < 1) {
        $_SESSION['editError'] = 'All fields are required';
        header("Location: edit.php?profile_id=".$_POST['profile_id']);
        return;
    }

    if(strpos($_POST['email'], '@') < 1){
      $_SESSION['editError'] = 'Email address must contain @';    
      header("Location: edit.php?profile_id=".$_POST['profile_id']);
        return;      
    }


    


    

    $sql = "UPDATE profile SET first_name = :first_name, last_name = :last_name, email = :email, headline = :headline, summary = :summary
            WHERE profile_id = :profile_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
      ':first_name' => $_POST['first_name'],
      ':last_name' => $_POST['last_name'],
      ':email' => $_POST['email'],
        ':headline' => $_POST['headline'],
        ':summary' => $_POST['summary'],
        ':profile_id' => $_POST['profile_id']));
    

        $stmt2 = $pdo->prepare("UPDATE users SET  name = :name , email = :email WHERE user_id = :user_id");
        $stmt2->execute(array(
        ':name' => $_POST['first_name']." ".$_POST['last_name'],
        ':email' => $_POST['email'],
        ':user_id' => $_POST['user_id'])
        );




        // Clear out the old position entries
      $stmt = $pdo->prepare('DELETE FROM Position WHERE profile_id=:pid');
      $stmt->execute(array( ':pid' => $_POST['profile_id']));

      $profile_id = $_POST['profile_id'];


            $rank = 1;
            for($i=1; $i<=9; $i++) {
            if ( ! isset($_POST['year'.$i]) ) continue;
            if ( ! isset($_POST['desc'.$i]) ) continue;

            $year = $_POST['year'.$i];
            $desc = $_POST['desc'.$i];
            $stmt = $pdo->prepare('INSERT INTO Position
                (profile_id, rank, year, description)
                VALUES ( :pid, :rank, :year, :desc)');

            $stmt->execute(array(
            ':pid' => $profile_id,
            ':rank' => $rank,
            ':year' => $year,
            ':desc' => $desc)
            );

            $rank++;

            }


            $stmt = $pdo->prepare('DELETE FROM education WHERE profile_id=:pid');
            $stmt->execute(array( ':pid' => $_POST['profile_id']));

            $ranks = 1;
            for($i=1; $i<=9; $i++) {
            if ( ! isset($_POST['years'.$i]) ) continue;
            if ( ! isset($_POST['edu_school'.$i]) ) continue;

            $years = $_POST['years'.$i];
            $edu_school = $_POST['edu_school'.$i];
            $stmts = $pdo->prepare('INSERT INTO education
                (profile_id, rank, year, institution_id)
                VALUES ( :pid, :rank, :year, :inst)');

            $stmt1 = $pdo->prepare('SELECT institution_id FROM Institution WHERE name LIKE :prefix');
            $stmt1->execute(array( ':prefix' => $edu_school));
            while ( $row1 = $stmt1->fetch(PDO::FETCH_ASSOC) ) {
                $inst = $row1['institution_id'];
                }

            $stmts->execute(array(
            ':pid' => $profile_id,
            ':rank' => $ranks,
            ':year' => $years,
            ':inst' => $inst)
            );

            $ranks++;

            }





      $_SESSION['success'] = 'Record updated';
      
      header( 'Location: index.php' ) ;
      return;
}

// Guardian: Make sure that user_id is present
if ( ! isset($_GET['profile_id']) ) {
  $_SESSION['error'] = "Missing profile_id";
  header('Location: index.php');
  return;
}

$stmt = $pdo->prepare("SELECT first_name, last_name, email, headline, summary, profile_id, user_id FROM profile where profile_id = :xyz");
$stmt->execute(array(":xyz" => $_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
    $_SESSION['error'] = 'Bad value for profile_id';
    header( 'Location: index.php' ) ;
    return;
}

// Flash pattern
if ( isset($_SESSION['editError']) ) {
    echo '<p style="color:red">'.$_SESSION['editError']."</p>\n";
    unset($_SESSION['editError']);
}

$first_name = htmlentities($row['first_name']);
$last_name = htmlentities($row['last_name']);
$email = htmlentities($row['email']);
$headline = htmlentities($row['headline']);
$summary = htmlentities($row['summary']);
$user_id = htmlentities($row['user_id']);
$profile_id = htmlentities($_GET['profile_id']);

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>c30 net c30net 393fc2d1</title>
    <script src="jquery.js"></script>
    <script src="jquery-ui.js"></script>
</head>
<body>



<p><h1>Editing Profile for <?= $first_name; ?></h1></p>
<form method="post">
<p>First Name:
<input type="text" name="first_name" value="<?= $first_name ?>"></p>
<p>Last Name:
<input type="text" name="last_name" value="<?= $last_name ?>"></p>
<p>Email:
<input type="text" name="email" value="<?= $email ?>"></p>
<p>HeadLine:
<input type="text" name="headline" value="<?= $headline ?>"></p>
<p>Summary</p>
<textarea name="summary" rows="4" cols="50" ><?= $summary ?></textarea>
<br>

<p>
Education: <input type="submit" id="addSchool" value="+">
                <div id="school_fields">

                </div>
                </p>                

<?php
$stmt4 = $pdo->prepare('SELECT * FROM education WHERE profile_id = :prof ORDER BY rank');
$stmt4 -> execute(array(':prof' => htmlentities($_GET['profile_id'])));
$rows4 = $stmt4->fetchAll();
$countSchool = 1;
foreach($rows4 as $row4){

    $instituion_id = $row4['institution_id'];

        $stmt5 = $pdo->prepare('SELECT * FROM institution WHERE institution_id = :inst');
        $stmt5 -> execute(array(':inst' => $instituion_id));
        $rows5 = $stmt5->fetchAll();


        foreach($rows5 as $row5){
  echo '<div id="school'.strval($countSchool).'"><p>Year: <input type="text" name="years'.strval($countSchool).'" value="'.$row4['year'].'" /><input type="button" value="-" onclick="$(\'#school'.strval($countSchool).'\').remove(); return false;"></p> School: <input type="text" name="edu_school'.strval($countSchool).'" size="80" class="school" value="';
  echo $row5['name'];
  echo '">';
  echo '</div>';

        }
  $countSchool++;
}
?>






<p>Position: <input type="submit" id="addPos" value="+">
                <div id="position_fields">

<?php
$stmt3 = $pdo->prepare('SELECT * FROM Position WHERE profile_id = :prof ORDER BY rank');
$stmt3 -> execute(array(':prof' => htmlentities($_GET['profile_id'])));
$rows3 = $stmt3->fetchAll();
$countPos = 1;
foreach($rows3 as $row3){
  echo '<div id="position'.strval($countPos).'"><p>Year: <input type="text" name="year'.strval($countPos).'" value="'.$row3['year'].'" /><input type="button" value="-" onclick="$(\'#position'.strval($countPos).'\').remove(); return false;"></p><textarea name="desc'.strval($countPos).'" rows="8" cols="80">';
  echo $row3['description'];
  echo '</textarea></div>';
  $countPos++;
}
?>
</div></p>


<input type="hidden" name="profile_id" value="<?= $profile_id ?>">
<input type="hidden" name="user_id" value="<?= $user_id ?>">
<p><input type="submit" value="Save"/>
<a href="index.php">Cancel</a></p>
</form>


<script>
            countPos = 0;
            $(document).ready(function(){
                window.console && console.log('Document Ready Called');
                $('#addPos').click(function(){
                    event.preventDefault();
                    if(countPos >= 9){
                        alert('Maximum of nine position entries exceeded');
                        return;
                    }
                    countPos++;
                    window.console && console.log("Adding position " + countPos);
                    $('#position_fields').append(
                        '<div id="position'+countPos+'">\
                        <p>Year: <input type="text" name="year'+countPos+'" value="" />\
                            <input type="button" value="-"\
                            onclick="$(\'#position'+countPos+'\').remove(); return false;"></p>\
                            <textarea name="desc'+countPos+'" rows="8" cols="80"></textarea>\
                        </div>'
                    );
                })
            })




            countSchool = 0;
            
            $(document).ready(function(){
                console.log('Document Ready Called');
                $('#addSchool').click(function(){
                    event.preventDefault();
                    if(countSchool >= 9){
                        alert('Maximum of nine school entries exceeded');
                        return;
                    }
                    countSchool++;
                    
                    console.log("Adding school " + countSchool);
                    $('#school_fields').append(
                        '<div id="school'+countSchool+'">\
                        <p>Year: <input type="text" name="years'+countSchool+'" value="" />\
                            <input type="button" value="-"\
                            onclick="$(\'#school'+countSchool+'\').remove(); return false;"></p>\
                            School: <input type="text" name="edu_school'+countSchool+'" size="80" class="school" value=""/>\
                        </div>'                       
                    
                        );

                        console.log("Adding school " + countSchool);

            
                    $('.school').autocomplete({ source: "school.php?term=" });
                        })
                    })
        </script>

    
</body>
</html>