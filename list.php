<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>XO</title>
    <link rel="stylesheet" href="styles/style.css?time=<?= microtime(true) ?>">
</head>
<body>

<h1>Список игроков</h1>
<nav>   
    <a href="index.php">К игре</a>
</nav>
<?php

    include_once('config.php');

    $conn = new mysqli($dbhost, $dbusername, $dbpassword, $dbdatabase);
    if($conn->connect_error){
        die('{"success":0,"error":"Ошибка базы данных"}');
    }       
    //Достаём из базы игроков
    $sql = "SELECT a.username, SUM(a.vic) as vic, SUM(a.fail) as fail FROM (( SELECT username1 as username, COUNT(*) as vic, 0 as fail FROM game WHERE win = 1 GROUP BY username1 UNION ALL SELECT username2 as username, COUNT(*) as vic, 0 as fail FROM game WHERE win = 2 GROUP BY username2 ) UNION ALL ( SELECT username1 as username, 0 as vic, COUNT(*) as fail FROM game WHERE win = 2 GROUP BY username1 UNION ALL SELECT username2 as username, 0 as vic, COUNT(*) as fail FROM game WHERE win = 1 GROUP BY username2 )) as a GROUP BY a.username;";
    $res = $conn->query($sql); 
    //Выводим на экран  
    while($list = $res->fetch_assoc()):
        echo $list['username'].', Побед: '.$list['vic'].', Поражений: '.$list['fail'].'<br>';
    endwhile;
?>

</body>
</html>