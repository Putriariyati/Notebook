<?php
session_start();
include('includes/config.php');
if(isset($_POST['signup']))
{
	$name=$_POST['name'];
	$email=$_POST['email'];
	$password=md5($_POST['password']);

	$query = mysqli_query($conn,"select * from register where email = '$email'")or die(mysqli_error());
	$count = mysqli_num_rows($query);

	if ($count > 0){ ?>
	 <script>
	 alert('Data Already Exist');
	</script>
	<?php
      }else{
        mysqli_query($conn,"INSERT INTO register(fullName, email, password) VALUES('$name','$email','$password')         
		") or die(mysqli_error()); 
        $getUserIdQueryContent = 'SELECT user_ID FROM register WHERE email = :email';
        $getUserIdQuery = $dbh->prepare($getUserIdQueryContent);
        $getUserIdQuery->bindValue(':email', $email, PDO::PARAM_STR);
        $getUserIdQuery->execute();
        $user_id = $getUserIdQuery->fetch()['user_ID'];

        $addWelcomeQueryContent = 'INSERT INTO lists2 VALUES (NULL, :user_id, "Welcome", current_timestamp())';
        $addWelcomeQuery = $dbh->prepare($addWelcomeQueryContent);
        $addWelcomeQuery->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $addWelcomeQuery->execute();
        
        $getWelcomeQueryContent = 'SELECT list_id FROM lists2 WHERE user_id = :user_id';
        $getWelcomeQuery = $dbh->prepare($getWelcomeQueryContent);
        $getWelcomeQuery->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $getWelcomeQuery->execute();
        $list_id = $getWelcomeQuery->fetch()['list_id'];

        $welcome = require_once 'welcome.php';
        $addTaskQueryContent = 'INSERT INTO tasks VALUES';

        $values = "";
        foreach ($welcome as $task) {
            $values = $values.'(NULL, '.$list_id.', "'.$task.'"),';
        }
        $values = substr($values, 0, -1);

        $dbh->query($addTaskQueryContent.$values);
    ?>
		<script>alert('Records Successfully  Added');</script>;
		<script>
		window.location = "index.php"; 
		</script>
		<?php   }

}

?>

<!DOCTYPE html>
<html lang="en" class="bg-dark">
<head>
  <meta charset="utf-8" />
  <title>Notebook | Web Application</title>
  <meta name="description" content="app, web app, responsive, admin dashboard, admin, flat, flat ui, ui kit, off screen nav" />
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" /> 
  <link rel="stylesheet" href="css/bootstrap.css" type="text/css" />
  <link rel="stylesheet" href="css/animate.css" type="text/css" />
  <link rel="stylesheet" href="css/font-awesome.min.css" type="text/css" />
  <link rel="stylesheet" href="css/font.css" type="text/css" />
    <link rel="stylesheet" href="css/app.css" type="text/css" />
  <!--[if lt IE 9]>
    <script src="js/ie/html5shiv.js"></script>
    <script src="js/ie/respond.min.js"></script>
    <script src="js/ie/excanvas.js"></script>
  <![endif]-->
</head>
<body>
  <section id="content" class="m-t-lg wrapper-md animated fadeInDown">
    <div class="container aside-xxl">
      <a class="navbar-brand block" href="signup.php">Notebook</a>
      <section class="panel panel-default m-t-lg bg-white">
        <header class="panel-heading text-center">
          <strong>Sign up</strong>
        </header>
        <form name="signup" method="POST">
          <div class="panel-body wrapper-lg">
          	 <div class="form-group">
	            <label class="control-label">Name</label>
	            <input name="name" type="text" placeholder="eg. Your name or company" class="form-control input-lg">
	          </div>
	          <div class="form-group">
	            <label class="control-label">Email</label>
	            <input name="email" type="email" placeholder="test@example.com" class="form-control input-lg">
	          </div>
	          <div class="form-group">
	            <label class="control-label">Password</label>
	            <input name="password" type="password" id="inputPassword" placeholder="Type a password" class="form-control input-lg">
	          </div>
	          <div class="line line-dashed"></div>
	          <button name="signup" type="submit" class="btn btn-primary btn-block">Sign up</button>
	          <div class="line line-dashed"></div>
	          <p class="text-muted text-center"><small>Already have an account?</small></p>
	          <a href="index.php" class="btn btn-default btn-block">Login</a>
          </div>
        </form>
      </section>
    </div>
  </section>
  <!-- footer -->
  <script src="js/jquery.min.js"></script>
  <!-- Bootstrap -->
  <script src="js/bootstrap.js"></script>
  <!-- App -->
  <script src="js/app.js"></script>
  <script src="js/app.plugin.js"></script>
  <script src="js/slimscroll/jquery.slimscroll.min.js"></script>
  
</body>
</html>