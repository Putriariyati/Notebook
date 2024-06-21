<?php include('includes/session.php')?>
<?php include('includes/config.php')?>
<?php

if (!isset($_SESSION['alogin'])) {
    header('Location: index.php');
    exit();
}

if (isset($_GET['list_id'])) {
    $newList = $_GET['list_id'];

    $newListQueryContent = 'SELECT lists2.name FROM lists2 WHERE lists2.user_id = :user AND lists2.list_id = :list';
    $listQuery = $dbh->prepare($newListQueryContent);
    $listQuery->bindValue(':user', $_SESSION['alogin'], PDO::PARAM_INT);
    $listQuery->bindValue(':list', $newList, PDO::PARAM_INT);
    $listQuery->execute();

    if ($listQuery->rowCount()) {
        $_SESSION['current_list'] = $newList;
        $_SESSION['current_list_name'] = $listQuery->fetch()['name'];
    }
    header('Location: new.php');
    exit();
}

$listsQueryContent = "SELECT lists2.name, lists2.list_id, lists2.last_edit, COUNT(tasks.task_id) as taskCount FROM lists2 LEFT OUTER JOIN tasks ON tasks.list_id = lists2.list_id WHERE lists2.user_id = {$_SESSION['alogin']} GROUP BY lists2.list_id ORDER BY lists2.last_edit DESC";
$lists = $dbh->query($listsQueryContent)->fetchAll();

if (!isset($_SESSION['current_list'])) {
    $_SESSION['current_list'] = $lists[0]['list_id'];
    $_SESSION['current_list_name'] = $lists[0]['name'];
}

$tasksQueryContent = "SELECT task_id, content FROM tasks WHERE list_id = {$_SESSION['current_list']}";
$tasks = $dbh->query($tasksQueryContent)->fetchAll();

?>

<!DOCTYPE html>
<html lang="en" class="app">
<head>
  <meta charset="utf-8" />
  <title>Notebook | Web Application</title>
  <meta name="description" content="app, web app, responsive, admin dashboard, admin, flat, flat ui, ui kit, off screen nav" />
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" /> 
  <link rel="stylesheet" href="css/bootstrap.css" type="text/css" />
  <link rel="stylesheet" href="css/animate.css" type="text/css" />
  <link rel="stylesheet" href="css/font-awesome.min.css" type="text/css" />
  <link rel="stylesheet" href="css/font.css" type="text/css" />
  <script src="js2/toggle.js" defer></script>
    <script src="js2/menu.js"defer></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <script src="js2/delete.js" defer></script>
  
  <link rel="stylesheet" href="css/app.css" type="text/css" />
  <!--[if lt IE 9]>
    <script src="js/ie/html5shiv.js"></script>
    <script src="js/ie/respond.min.js"></script>
    <script src="js/ie/excanvas.js"></script>
  <![endif]-->
</head>
<body>
  <section class="vbox">
    <header class="bg-dark dk header navbar navbar-fixed-top-xs">
      <div class="navbar-header aside-md">
        <a class="btn btn-link visible-xs" data-toggle="class:nav-off-screen,open" data-target="#nav,html">
          <i class="fa fa-bars"></i>
        </a>
        <a href="#" class="navbar-brand" data-toggle="fullscreen"><img src="images/logo.png" class="m-r-sm">Notebook</a>
        <a class="btn btn-link visible-xs" data-toggle="dropdown" data-target=".nav-user">
          <i class="fa fa-cog"></i>
        </a>
      </div>
      <ul class="nav navbar-nav navbar-right m-n hidden-xs nav-user">
        <li class="dropdown">
          <?php $query= mysqli_query($conn,"select * from register where user_ID = '$session_id'")or die(mysqli_error());
                $row = mysqli_fetch_array($query);
            ?>

          <a href="#" class="dropdown-toggle" data-toggle="dropdown">
            <span class="thumb-sm avatar pull-left">
              <img src="images/profile.jpg">
            </span>
            <?php echo $row['fullName']; ?> <b class="caret"></b>
          </a>
          <ul class="dropdown-menu animated fadeInRight">
            <span class="arrow top"></span>
            <li class="divider"></li>
            <li>
              <a href="logout.php" data-toggle="ajaxModal" >Logout</a>
            </li>
          </ul>
        </li>
      </ul>      
    </header>
    <section>
      <section class="hbox stretch">
        <!-- .aside -->
        <aside class="bg-dark lter aside-md hidden-print" id="nav">          
          <section class="vbox">
            <section class="w-f scrollable">
              <div class="slim-scroll" data-height="auto" data-disable-fade-out="true" data-distance="0" data-size="5px" data-color="#333333">
                
                <!-- nav -->
                <nav class="nav-primary hidden-xs">
                  <ul class="nav">
                    <li  class="active">
                      <a href="notebook.php" class="active">
                        <i class="fa fa-pencil icon">
                          <b class="bg-info"></b>
                        </i>
                        <span>Notes</span>
                      </a>
                    </li>
                    <li  class="active">
                      <a href="new.php" class="active">
                        <i class="fa fa-pencil icon">
                          <b class="bg-info"></b>
                        </i>
                        <span>Todo List</span>
                      </a>
                    </li>
                  </ul>
                </nav>
                <!-- / nav -->
              </div>
            </section>
            
            <footer class="footer lt hidden-xs b-t b-dark">
              <div id="invite" class="dropup">                
                <section class="dropdown-menu on aside-md m-l-n">
                  <section class="panel bg-white">
                    <header class="panel-heading b-b b-light">
                      <?php $query= mysqli_query($conn,"select * from register where user_ID = '$session_id'")or die(mysqli_error());
                        $row = mysqli_fetch_array($query);
                      ?>
                      <?php echo $row['fullName']; ?> <i class="fa fa-circle text-success"></i>
                    </header>
                  </section>
                </section>
              </div>
              <a href="#nav" data-toggle="class:nav-xs" class="pull-right btn btn-sm btn-dark btn-icon">
                <i class="fa fa-angle-left text"></i>
                <i class="fa fa-angle-right text-active"></i>
              </a>
            </footer>
          </section>
        </aside>
        <!-- /.aside -->
        <section id="content">
          <section class="hbox stretch">
                  <aside class="aside-lg bg-light lter b-r">
                    <div class="wrapper">
                    <div id="mask" class="closed-menu">
                <nav>
                    <div id="hiding-menu">
                        <?php
                        $listsContent = "";
                        foreach ($lists as $list) {
                            if ($list['list_id'] == $_SESSION['current_list']) {
                                $listsContent = '<a href="?list_id=' . $list['list_id'] . '" class="menu-option"><span class="list"><h2 class="m-t-none" id="list'.$list['list_id'].'">' . $list['name'] . '</span><span class="item-count" id="itemCountCurrent">' . $list['taskCount'] . '</span></a><span class="icon-hover" onclick="deleteList('.$list['list_id'].')">     <i class="fa fa-minus"></i></i></span></div>'.$listsContent;
                            }
                            else {
                                $listsContent = $listsContent.'<a href="?list_id=' . $list['list_id'] . '" class="menu-option"><span class="list"><div class="menu-option-outer" id="list'.$list['list_id'].'">' . $list['name'] . '</span><span class="item-count">' . $list['taskCount'] . '</span></a><span class="icon-hover" onclick="deleteList('.$list['list_id'].')">     <i class="fa fa-minus"></i><i class="fi fi-rr-trash normal"></i></span></div><hr>';
                            }
                        }
                        echo $listsContent;
                        ?>

                        <form action="addList.php" method="POST">
                            <div id="add-list">
                                <input type="text" placeholder="New list name" class="no-bg" name="list_name">
                                <button type="submit">
                                    <span class="icon-hover">
                                        <i class="fa fa-plus filled"></i>
                                    </span>
                                </button>
                            </div>
                        </form>
                        <?php
                        if (isset($_SESSION['e_list'])) {
                            echo '<p class="error">' . $_SESSION['e_list'] . '</p>';
                            unset($_SESSION['e_list']);
                        }
                        ?>
                    </div>
                </nav>
            </div>
                    </div>
                </aside>
                <aside class="bg-white">
                  <section class="vbox">
                    <header class="header bg-light bg-gradient">
                      <ul class="nav nav-tabs nav-white">
                        <li class="active"><a href="#activity" data-toggle="tab"><h4 style = "text-transform:uppercase;"><b>To Do List</b></h4></a></li>
                      </ul>
                    </header>
                    <section class="scrollable">
                    <main id="main">
            <div id="add-item-outer">
                <form action="addTask.php" method="POST">
                    <div id="add-item">
                        <button type="submit" class="no-bg">
                            <span class="icon-hover">
                                <i class="fa fa-plus filled"></i>
                            </span>
                        </button>
                        <input type="text" placeholder="Add task" class="no-bg" name="task">
                    </div>
                </form>
                <?php
                if (isset($_SESSION['e_task'])) {
                    echo '<p class="error">' . $_SESSION['e_task'] . '</p>';
                    unset($_SESSION['e_task']);
                }
                ?>
            </div>
            <ul id="items">
                <?php
                foreach ($tasks as $task) {
                    echo '<p  id="task' . $task['task_id'] . '"><span class="icon-hover" onclick="deleteTask(' . $task['task_id'] . ');"><i class="fa fa-minus normal"></i><i class="fi fi-sr-checkbox filled"></i>  </span><span class="task">' .
                        $task['content'] . '</span></p>';
                }
                ?>

            </ul>
        </main>
                        <section class="panel clearfix bg-info lter">
                          </div>
                        </section>
                      </div>
                    </section>
                  </section>              
                </aside>
          </section>
          <a href="#" class="hide nav-off-screen-block" data-toggle="class:nav-off-screen" data-target="#nav"></a>
        </section>
        <aside class="bg-light lter b-l aside-md hide" id="notes">
          <div class="wrapper">Notification</div>
        </aside>
      </section>
    </section>
  </section>
  <script src="js/jquery.min.js"></script>
  <!-- Bootstrap -->
  <script src="js/bootstrap.js"></script>
  <!-- App -->
  <script src="js/app.js"></script>
  <script src="js/app.plugin.js"></script>
  <script src="js/slimscroll/jquery.slimscroll.min.js"></script>
  <script src="js/libs/underscore-min.js"></script>
<script src="js/libs/backbone-min.js"></script>
<script src="js/libs/backbone.localStorage-min.js"></script>  
<script src="js/libs/moment.min.js"></script>
<!-- Notes -->
<script src="js/apps/notes.js"></script>

</body>
</html>