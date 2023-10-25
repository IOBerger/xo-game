<?php 

    //Функция, проверяющая, есть ли на поле ряд
    function checkWin($data){

        $winRow = 0;

        for($i=0;$i<3;$i++){
            if($data[0][0]==$data[0][1] and $data[0][0]==$data[0][2] and $data[0][0]!=''){
                $winRow = 1;
            }
            if($data[1][0]==$data[1][1] and $data[1][0]==$data[1][2] and $data[1][0]!=''){
                $winRow = 2;
            }
            if($data[2][0]==$data[2][1] and $data[2][0]==$data[2][2] and $data[2][0]!=''){
                $winRow = 3;
            }
            if($data[0][0]==$data[1][0] and $data[0][0]==$data[2][0] and $data[0][0]!=''){
                $winRow = 4;
            }
            if($data[0][1]==$data[1][1] and $data[0][1]==$data[2][1] and $data[0][1]!=''){
                $winRow = 5;
            }
            if($data[0][2]==$data[1][2] and $data[0][2]==$data[2][2] and $data[0][2]!=''){
                $winRow = 6;
            }
            if($data[0][0]==$data[1][1] and $data[0][0]==$data[2][2] and $data[0][0]!=''){
                $winRow = 7;
            }
            if($data[0][2]==$data[1][1] and $data[0][2]==$data[2][0] and $data[0][2]!=''){
                $winRow = 8;
            }                     
        }

        return $winRow;

    }

    //Включение файла с данными БД
    include_once('../config.php');

    //Время, через которое игра завершается автоматически, в секундах
    $timeOld = 5*60;

    //Проверки данных
    if(empty($_POST)){
        die('{"success":0}');
    }
    $inputData = file_get_contents('php://input');
    $inputDataDecoded = !is_null(json_decode($inputData,true)) ? json_decode($inputData,true) : array();
    if(empty($inputDataDecoded) or !array_key_exists('username',$inputDataDecoded)
        or !array_key_exists('i',$inputDataDecoded) or !array_key_exists('j',$inputDataDecoded)
    ){
        die('{"success":0,"error":"Неправильные данные"}');
    }

    //Достаём данные из массива
    $username = $inputDataDecoded['username'];
    $step = [ 'x' => $inputDataDecoded['i'], 'y' => $inputDataDecoded['j'] ];

    //Подключаемся к БД
    $conn = new mysqli($dbhost, $dbusername, $dbpassword, $dbdatabase);
    if($conn->connect_error){
        die('{"success":0,"error":"Ошибка базы данных"}');
    }       

    //Достаём из базы последнюю игру
    $sql = "SELECT * FROM game ORDER BY id DESC LIMIT 0,1";
    $resGame = $conn->query($sql);
    $game = $resGame->fetch_assoc();
    
    //Если в базе ничего нет или последняя игра закончена
    if(!$game or !is_null($game['win']) and $game['win']!=0 or microtime(true)-$game['last_update']>$timeOld){

        //Проверяем, уведомлен ли проигравший о конце игры
        if(
            ($game['next_step_user']==1 and $username == $game['username1'] or
            $game['next_step_user']==2 and $username == $game['username2'])
        ){
            //Меняем следующего, кому ходить, на победителя, это значит, проигравшая сторона уведомлена о конце игры
            $stmt = $conn->prepare("UPDATE game SET next_step_user = ? WHERE id = ?");
            $next = !is_null($game['win']) ? $game['win'] : 3-$game['next_step_user'];
            //Подготовленный запрос, автоматически проставит слэши перед кавычками
            $stmt->bind_param("ii", $next,$game['id']);
            $result=$stmt->execute();
            if(!$result){
                die('{"success":0,"error":"Ошибка базы данных"}');
            }    
            //Отправляем позиции клеток и информацию о проигрыше
            die("{
                    \"success\": 1,
                    \"cell-11\": \"{$game["cell11"]}\",
                    \"cell-12\": \"{$game["cell12"]}\",
                    \"cell-13\": \"{$game["cell13"]}\",
                    \"cell-21\": \"{$game["cell21"]}\",
                    \"cell-22\": \"{$game["cell22"]}\",
                    \"cell-23\": \"{$game["cell23"]}\",
                    \"cell-31\": \"{$game["cell31"]}\",
                    \"cell-32\": \"{$game["cell32"]}\",
                    \"cell-33\": \"{$game["cell33"]}\",
                    \"win\": \"{$game["win"]}\",
                    \"victory\": 0
                }");

        }

        //Создаём новую игру
        $stmt = $conn->prepare("INSERT INTO game(username1,last_update) VALUES (?,?)");
        //Подготовленный запрос, автоматически проставит слэши перед кавычками
        $stmt->bind_param("si", $username,microtime(true));
        $result=$stmt->execute();
        if(!$result){
            die('{"success":0,"error":"Ошибка базы данных"}');
        }
        //Достаём новую игру из БД
        $sql = "SELECT * FROM game ORDER BY id DESC LIMIT 0,1";
        $result = $conn->query($sql);
        $game=$result->fetch_assoc();
    }

    //Определяем символ, которым ходим
    if($username == $game['username1']){
        $symbol = 'x';
    }else{
        $symbol = 'o';
    }

    //Если прошло определённое время с последнего действия, игра устаревает
    if(microtime(true)-$game['last_update']>$timeOld and (is_null($game['win']) or $game['win'] == 0) and 
        ($game['username1']==$username or $game['username2']==$username)
    ){
        
        $stmt = $conn->prepare("UPDATE game SET win = 0 WHERE id = ?");
        //Подготовленный запрос, автоматически проставит слэши перед кавычками
        $stmt->bind_param("i", $game['id']);
        $result=$stmt->execute();
        if(!$result){
            die('{"success":0,"error":"Ошибка базы данных"}');
        }
        die('{"success":0,"error":"Долго не было действий в игре"}');
        
    }

    //Если уже есть один пользователь в игре
    if($game['username1']!=null){
        if($game['username1']!=$username){
            
            //Если есть и второй пользователь, то игра занята
            if(!is_null($game['username1']) and $game['username1']!=$username and !is_null($game['username2']) and $game['username2']!=$username 
                and microtime(true)-$game['last_update']<=$timeOld
            ){
                die('{"success":0,"error":"Игра занята, уже есть два человека"}');
            }

            //Заносим имя второго пользователя
            $stmt = $conn->prepare("UPDATE game SET username2 = ?, last_update = ? WHERE id = ?");
            $stmt->bind_param("sii", $username, microtime(true), $game['id']);
            $result=$stmt->execute();
            if(!$result){
                die('{"success":0,"error":"Ошибка базы данных"}');
            }

        }
    }

    //При победе записать последнее время
    if(microtime(true)-$game['last_update']>$timeOld and $game['win']>0 and $step[0]!=0){
        $stmt = $conn->prepare("UPDATE game SET last_update = ? WHERE id = ?");
        $stmt->bind_param("si", microtime(true), $game['id']);
        $result=$stmt->execute();
        if(!$result){
            die('{"success":0,"error":"Ошибка базы данных"}');
        }
    }
    
    //Достаём данные клеток из БД
    $data = [
        [$game['cell11'],$game['cell12'],$game['cell13']],
        [$game['cell21'],$game['cell22'],$game['cell23']],
        [$game['cell31'],$game['cell32'],$game['cell33']]
    ];

    //Переменная, обозначающая ряд, который перечёркнут
    $win = 0;

    //Делвем шаг и заносим в БД
    if($step['x']>0 and $step['y']>0 and ($game['next_step_user']==1 and $username == $game['username1'] or
        $game['next_step_user']==2 and $username == $game['username2'])){

        if(empty($data[$step['x']-1][$step['y']-1])){
            $data[$step['x']-1][$step['y']-1]=$symbol;
            $win = checkWin($data);
        }
 
        $nextStepUser = 3-$game['next_step_user'];

        //Заносим данные в БД
        $stmt = $conn->prepare("UPDATE game SET cell11 = ?,cell12 = ?,cell13 = ?, 
            cell21 = ?,cell22 = ?,cell23 = ?,cell31 = ?,cell32 = ?,cell33 = ?,win = ?, last_update = ?, next_step_user = ? WHERE id = ?");
        $stmt->bind_param("sssssssssiiii", 
            $data[0][0],$data[0][1],$data[0][2],
            $data[1][0],$data[1][1],$data[1][2],
            $data[2][0],$data[2][1],$data[2][2],
            $winUser, microtime(true), $nextStepUser, $game['id']
        );
        $result=$stmt->execute();
        if(!$result){
            die('{"success":0,"error":"Ошибка базы данных"}');
        }    
    }

    //Проверяем, есть ли выигрыш, и если да, заносим это в БД
    $winUser = 0;
    $victory = 0;
    $win = checkWin($data);
    if($win>0 and (is_null($game['win']) or $game['win']<1)){
        if($symbol == 'x'){
            $winUser = 1;
        }
        if($symbol == 'o'){
            $winUser = 2;
        }
        $stmt = $conn->prepare("UPDATE game SET win = ? WHERE id = ?");
        $stmt->bind_param("ii", 
            $winUser, $game['id']
        );
        $result=$stmt->execute();
        if(!$result){
            die('{"success":0,"error":"Ошибка базы данных"}');
        }    
        $victory = 1;
    }
    
    //Отправляем данные
    echo "{
        \"success\": 1,
        \"cell-11\": \"{$data[0][0]}\",
        \"cell-12\": \"{$data[0][1]}\",
        \"cell-13\": \"{$data[0][2]}\",
        \"cell-21\": \"{$data[1][0]}\",
        \"cell-22\": \"{$data[1][1]}\",
        \"cell-23\": \"{$data[1][2]}\",
        \"cell-31\": \"{$data[2][0]}\",
        \"cell-32\": \"{$data[2][1]}\",
        \"cell-33\": \"{$data[2][2]}\",
        \"win\": $win,
        \"victory\": $victory
    }";
    
    