<?php

    //Получаем имя
    if(array_key_exists("username",$_POST)){
        $username = htmlspecialchars(trim($_POST['username']));
        if($username==''){
            die('Имя не должно быть пустым');
        }
    }else{
        $username='';
    }


    //Включаем шаблон
    include_once('templates/main.php');