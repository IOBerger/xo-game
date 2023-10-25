function freshCells(data,fresh){
    //Обновление клеток
    for(let i=1;i<=3;i++)
        for(let j=1;j<=3;j++){ 
            $('#cell-'+i+j).html(data['cell-'+i+j]);
        }
}

function gameProcess(formData, fresh){

    //Посылаем запрос
    $.ajax({
        url: 'processing/game_processing.php',
        method: 'post',
        data: JSON.stringify(formData),
        async: false,
        //В случае успеха
        success: function(data) {
            let dataObj = JSON.parse(data);
            if('success' in dataObj && dataObj['success']==1){
                //Меняем значения полей на полученные
                freshCells(dataObj,fresh);
                //Оповещаем о победе или поражении
                if(dataObj['win']>0){
                        clearInterval(fresh);
                        if(dataObj['victory']>0){
                            alert("You won!");
                        }else{
                            alert("Game over!");
                        }
                        window.location.replace('index.php');
                }
            }else{
                //В случае ошибки очищаем интервал и выводим сообщение
                clearInterval(fresh);
                alert(dataObj['error']);
                window.location.replace('index.php');
            }                        
        },
        //В случае ошибки
        error: function(xhr, textStatus) {
            clearInterval(fresh);
            alert([xhr.status, textStatus]);
            window.location.replace('index.php');
        }         


    })

}

//Навешиваем обработчики событий
function step(fresh){
    for(let i=1;i<=3;i++){     
        for(let j=1;j<=3;j++){
            if($("#cell-"+i+j).html()=='...' || $("#cell-"+i+j).html()==''){
                $("#cell-"+i+j).on("click", function() {
                    formData = {
                        i: i, 
                        j: j, 
                        username: $("#username").html(),    
                    };
                    gameProcess(formData,fresh);

                });
            }
        
        }
    }
     
}

$(function(){

    //Если имя пусто
    if($("span#username").html() == null){
        return;
    }

    //Устанавливаем интервал
    let fresh = setInterval(function() {
        let formData = {
                i:0,
                j:0,
                username: $("#username").html(),
            };   
            gameProcess(formData,fresh);
    }, 1*1000);

    //Навешиваем обработчики событий
    step(fresh);
     

});