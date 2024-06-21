<?php include('includes/session.php')?>
<?php include('includes/config.php')?>
<?php

if (!isset($_POST['list_name'])) {
    header('Location: new.php');
    exit();
}

if ((strlen($_POST['list_name']) < 1) || (strlen($_POST['list_name']) > 50)) {
    $_SESSION['e_list'] = 'List name must be 1 to 50 characters long!';
}
else {
    if ($_POST['list_name'] != filter_var($_POST['list_name'], FILTER_SANITIZE_FULL_SPECIAL_CHARS)) {
        $_SESSION['e_list'] = 'List name must consist of letters and numbers!';
    }
    else {
        
        $addListQueryContent = 'INSERT INTO lists2 VALUES (NULL, :user_id, :name, current_timestamp())';
        $addListQuery = $dbh->prepare($addListQueryContent);
        $addListQuery->bindValue(':user_id', $_SESSION['alogin']);
        $addListQuery->bindValue(':name', $_POST['list_name']);
        $addListQuery->execute();
        unset($_SESSION['current_list']);
    }
}

header('Location: new.php');