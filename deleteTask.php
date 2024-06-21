<?php include('includes/session.php')?>
<?php include('includes/config.php')?>
<?php

$data = json_decode(file_get_contents("php://input"), true);

if(isset($_SESSION['alogin']) && isset($data['task_id'])) {
    $task = $data['task_id'];
    
    $ownerQueryContent = 'SELECT * FROM tasks WHERE task_id = :task_id AND list_id = :list_id';
    $ownerQuery = $dbh->prepare($ownerQueryContent);
    $ownerQuery->bindValue('task_id',  $task, PDO::PARAM_INT);
    $ownerQuery->bindValue('list_id', $_SESSION['current_list'], PDO::PARAM_INT);
    $ownerQuery->execute();
    if ($ownerQuery->rowCount()) {
        $deleteTaskQueryContent = 'DELETE FROM tasks WHERE task_id = :task_id';
        $deleteTaskQuery = $dbh->prepare($deleteTaskQueryContent);
        $deleteTaskQuery->bindValue('task_id',  $task, PDO::PARAM_INT);
        $deleteTaskQuery->execute();

        $updateTimeQueryContent = 'UPDATE lists2 SET last_edit = current_timestamp() WHERE list_id = :list_id';
        $updateTimeQuery = $dbh->prepare($updateTimeQueryContent);
        $updateTimeQuery->bindValue(':list_id', $_SESSION['current_list'], PDO::PARAM_INT);
        $updateTimeQuery->execute();

        echo '1';
        exit();
    }

}

echo '0';