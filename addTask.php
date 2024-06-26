<?php include('includes/session.php')?>
<?php include('includes/config.php')?>
<?php

if (!isset($_POST['task']) || !isset($_SESSION['current_list'])) {
    header('Location: new.php');
    exit();
}

if ((strlen($_POST['task']) < 1) || (strlen($_POST['task']) > 100)) {
    $_SESSION['e_task'] = 'Task must be 1 to 100 characters long!';
}
else {
    if ($_POST['task'] != filter_var($_POST['task'], FILTER_SANITIZE_FULL_SPECIAL_CHARS)) {
        $_SESSION['e_task'] = 'Task must consist of letters and numbers!';
    }
    else {
        
        
        $taskQueryContent = 'INSERT INTO tasks VALUES (NULL, :list_id, :content)';
        $taskQuery = $dbh->prepare($taskQueryContent);
        $taskQuery->bindValue(':list_id', $_SESSION['current_list'], PDO::PARAM_INT);
        $taskQuery->bindValue('content', $_POST['task'], PDO::PARAM_STR);
        $taskQuery->execute();
        
        $updateTimeQueryContent = 'UPDATE lists2 SET last_edit = current_timestamp() WHERE list_id = :list_id';
        $updateTimeQuery = $dbh->prepare($updateTimeQueryContent);
        $updateTimeQuery->bindValue(':list_id', $_SESSION['current_list'], PDO::PARAM_INT);
        $updateTimeQuery->execute();
    }
}

header('Location: new.php');