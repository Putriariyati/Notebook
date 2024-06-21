<?php include('includes/session.php')?>
<?php include('includes/config.php')?>
<?php

$data = json_decode(file_get_contents("php://input"), true);

if(isset($_SESSION['alogin']) && isset($data['list_id'])) {
    $list = $data['list_id'];
    
    $ownerQueryContent = 'SELECT * FROM lists2 WHERE list_id = :list_id AND user_id = :user_id';
    $ownerQuery = $dbh->prepare($ownerQueryContent);
    $ownerQuery->bindValue('list_id',  $list, PDO::PARAM_INT);
    $ownerQuery->bindValue('user_id', $_SESSION['alogin'], PDO::PARAM_INT);
    $ownerQuery->execute();
    if ($ownerQuery->rowCount()) {
        $listsCountQueryContent = 'SELECT * FROM lists2 WHERE user_id = :user_id';
        $listsCountQuery = $dbh->prepare($listsCountQueryContent);
        $listsCountQuery->bindValue('user_id', $_SESSION['alogin'], PDO::PARAM_INT);
        $listsCountQuery->execute();
        if ($listsCountQuery->rowCount() > 1) {
            $deleteListQueryContent = 'DELETE FROM lists2 WHERE list_id = :list_id';
            $deleteListQuery = $dbh->prepare($deleteListQueryContent);
            $deleteListQuery->bindValue('list_id',  $list, PDO::PARAM_INT);
            $deleteListQuery->execute();
            if ($list == $_SESSION['current_list'])
                unset($_SESSION['current_list']);
    
            echo '1';
        }
        else {
            echo '-1';
        }
        exit();
    }

}

echo '0';