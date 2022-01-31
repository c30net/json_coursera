<?php session_start();
require_once "pdo.php";
?>
<!DOCTYPE html>
<html>
<head>
<title>c30 net c30net 393fc2d1</title>
</head>
<body>
<div>
<h1>Welcome to the c30 net's Resume Registry</h1>
<br>
<?php



if ( isset($_SESSION['success']) ) {
    echo('<p style="color: green;">'.htmlentities($_SESSION['success'])."</p>\n");
    unset($_SESSION['success']);
  }

  if ( isset($_SESSION['error']) ) {
    echo('<p style="color: red;">'.htmlentities($_SESSION['error'])."</p>\n");
    unset($_SESSION['error']);
  }

  $stmt = $pdo->query("SELECT name, email, user_id FROM users");
  $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
  
if(count($rows))
{    
    echo('<table border="1">'."\n");
    echo ('<tr><th>Name</th>');
    foreach ( $rows as $row ){
      
      if(isset($_SESSION['email']) && $row['email'] === $_SESSION['email']){
        echo('<th>Headline</th>');
        echo('<th>Action</th>');
        break;
      }
      
    }
    echo('</tr>');

    foreach ( $rows as $row ) {
      if(!isset($_SESSION['email']))
      {            
        $user_id = $row['user_id'];
        $stmt2 = $pdo->query("SELECT profile_id FROM profile WHERE user_id = $user_id");
        $rows2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);
        //foreach ( $rows2 as $row2 )
        //{ 
          //echo $row['user_id'];
         // echo '=============y===========';
          // echo "<tr><td>";
          // echo("<a href="."view.php?profile_id=".$row2['profile_id'].">".htmlentities($row['name'])."</a>");
          // echo("</td>");       
          // echo("</tr>\n");
        //}      
        echo "<tr><td>";
        echo("<a href="."view.php?profile_id="."".">".htmlentities($row['name'])."</a>");
        echo("</td>");       
        echo("</tr>\n");


      }
      
      elseif (isset($_SESSION['email']))
      {
        
        $user_id = $row['user_id'];
        $stmt2 = $pdo->query("SELECT headline , profile_id FROM profile WHERE user_id = $user_id");
        $rows2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);
      
      foreach ( $rows2 as $row2 ) {
        $rowpid = $row2['profile_id'];
      
        
        echo "<tr><td>";
        echo("<a href="."view.php?profile_id=".$rowpid.">".htmlentities($row['name'])."</a>");
        echo("</td><td>");
        echo(htmlentities($row2['headline']));
        echo("</td>");

        
        //foreach ( $rows as $row ){             
          
         // if(isset($_SESSION['email']) && ($row['email'] === $_SESSION['email'])){
            echo("<td>");            
            echo('<a href="edit.php?profile_id='.$rowpid.'">Edit</a> / ');
            echo('<a href="delete.php?profile_id='.$rowpid.'">Delete</a>');
            echo("</td>");
            
  
          //}
        //}
        
        echo("</tr>\n");
      }
         
      }
    }
	
	echo("</table>\n");
} else {
    echo "No rows found";
}

if(!isset($_SESSION['email'])){
  echo '<p><a href="login.php">Please log in</a></p>';
  return;
  }

?>
</div>

<p>
<a href="add.php">Add New Entry</a> |
<a href="logout.php">Logout</a>
</p>

</body>
</html>

