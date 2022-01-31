<?php session_start();
require_once "pdo.php";

// Demand a session parameter
if ( ! isset($_SESSION['email']) ) {
    die("Not logged in");
  }

  if ( isset($_POST['cancel'] )) {
    header("Location: index.php");
    return;
}


$fail = false;

  if(isset($_POST['add']))
{   $first_name = htmlentities($_POST['first_name']);
    $last_name = htmlentities($_POST['last_name']);    
    $email = htmlentities($_POST['email']);    
    $headline = htmlentities($_POST['headline']);
    $summary = htmlentities($_POST['summary']);
    if ( strlen($first_name) < 1 || strlen($last_name) < 1 || strlen($email) < 1 || strlen($headline) < 1 || strlen($summary) < 1 ){
        $fail = 'All fields are required';        
    }  elseif(strpos($email, '@') === false) {
        $fail = "Email address must contain @";
    }    else    {    






        for($i=1; $i<=9; $i++) {
            if ( ! isset($_POST['years'.$i]) ) continue;
            if ( ! isset($_POST['edu_school'.$i]) ) continue;
        
            $years = htmlentities($_POST['years'.$i]);
            $edu_school = htmlentities($_POST['edu_school'.$i]);
        
            if ( strlen($years) == 0 || strlen($edu_school) == 0 ) {
              $fail = "All fields are required";
              $_SESSION['err'] = $fail;
              header("Location: add.php");
              return;
            }
        
            if ( ! is_numeric($years) ) {
              $fail = "Position year must be numeric";
              $_SESSION['err'] = $fail;
              header("Location: add.php");
              return;
            }
          }
        
        






        
            
        
            for($i=1; $i<=9; $i++) {
              if ( ! isset($_POST['year'.$i]) ) continue;
              if ( ! isset($_POST['desc'.$i]) ) continue;
          
              $year = htmlentities($_POST['year'.$i]);
              $desc = htmlentities($_POST['desc'.$i]);
          
              if ( strlen($year) == 0 || strlen($desc) == 0 ) {
                $fail = "All fields are required";
                $_SESSION['err'] = $fail;
                header("Location: add.php");
                return;
              }
          
              if ( ! is_numeric($year) ) {
                $fail = "Position year must be numeric";
                $_SESSION['err'] = $fail;
                header("Location: add.php");
                return;
              }
            }
            

            $stmt = $pdo->prepare('INSERT INTO users ( name, email, password) VALUES ( :name, :email, :password)');

        $stmt->execute(array(
        ':name' => $first_name." ".$last_name,
        ':email' => $email,
        ':password' => '1a52e17fa899cf40fb04cfc42e6352f1')
        );

        $stmt = $pdo->prepare("SELECT user_id FROM users where email = :xyz");
        $stmt->execute(array(":xyz" => $email));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $_SESSION['user_id'] = $row['user_id'];
            $stmt = $pdo->prepare('INSERT INTO Profile (user_id, first_name, last_name, email, headline, summary) VALUES ( :uid, :fn, :ln, :em, :he, :su)');
            $stmt->execute(array(
            ':uid' => $_SESSION['user_id'],
            ':fn' => $_POST['first_name'],
            ':ln' => $_POST['last_name'],
            ':em' => $_POST['email'],
            ':he' => $_POST['headline'],
            ':su' => $_POST['summary'])
            );



            $profile_id = $pdo->lastInsertId();
            




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




            $_SESSION['success'] = 'Record added'.$_SESSION['user_id'];
            header('Location: index.php');
            return; 
          
            
} 
}

if($fail !== false)
{
    $_SESSION['err'] = $fail;
    header("Location: add.php");
    return;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>c30 net c30net 393fc2d1</title>
    <script src="https://code.jquery.com/jquery-3.2.1.js" integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE=" crossorigin="anonymous"></script>

  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js" integrity="sha256-T0Vest3yCU7pafRw9r+settMBX6JkKN06dqBnpQ8d30=" crossorigin="anonymous"></script>
</head>
<body>

<h1>Adding Profile for <?php echo $_SESSION['email']; ?></h1>
<?php
if ( isset($_SESSION['err']) ) {
    echo('<p style="color: red;">'.htmlentities($_SESSION['err'])."</p>\n");
    unset($_SESSION['err']);
  }
  ?>

        <form method="post">
            <label for="first_name">First Name</label>
            <input type="text" name="first_name" id="first_name"><br />
            <br>
            <label for="last_name">Last Name</label>
            <input type="text" name="last_name" id="last_name"><br />
            <br>
            <label for="email">Email</label>
            <input type="text" name="email" id="email"><br />
            <br>
            <label for="headline">Headline</label>
            <input type="text" name="headline" id="headline"><br />
            <br>
            <label for="summary">Summary</label>
            <textarea id="summary" name="summary" rows="4" cols="50"></textarea>
            <br>
           


            <p>
                Education: <input type="submit" id="addSchool" value="+">
                <div id="school_fields">

                </div>
            </p>


            <p>
                Position: <input type="submit" id="addPos" value="+">
                <div id="position_fields">

                </div>
            </p>



            <input type="submit" value='Add' name='add'>
            <input type="submit" value='Cancel' name='cancel'>
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

            
                    $('.school').autocomplete({ source: "school.php?term=" });
                        })
                    })
                    

        </script>


</body>
</html>

