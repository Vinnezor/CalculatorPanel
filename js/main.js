"use strict";
$(document).ready( function(){
    var limit = 5; //лимит количества создания полей для заполнения размеров окон
    var window_size_obj = {"wind_size": {}}; //объект для записи значений размера окна
    var corner_count_obj = {"corner_count" : {}}; // объект для записи значений углов
    var outputArray; //массив для хранения полей куда результат записывать
    var limit_window; // лимит окон передается серверу, чтобы не обрабатывались данные с удаленных окон
    var total_cost_obj = { // для запомнимания общей стоимости
        "result_cost_panels" : 0,
        "result_protective_covering" : 0,
        "result_cost_window" : 0,
        "result_cost_corners" : 0
    };

    window_size_obj.wind_size = {1:{}, "limit_window" : 1};


    //расчет панелей
    $("#count_panels").on("keyup", function() {
        var data = {"count_panels" :  $(this).val()};
        outputArray = ["count_panels","cost_panels", "weight_pc", "protective_covering"]; //куда вписывать результат
        requestAjax(data,outputArray);
    });

    //расчет метража окон
    $(".window_size").on('keyup',  "input[type!='submit']",  function() {
        var str = this.id;
        var char_number = str.charAt(str.length-1);
        outputArray = ["footage", "cost_window"];
        window_size_obj.wind_size[char_number][str] = $(this).val();
        requestAjax(window_size_obj, outputArray);
    });

    //расчет углов дома
    $(".corners_count").on("keyup", "input[type!='submit']", function() {
        var str = this.id;
        corner_count_obj["corner_count"][str] = $(this).val();
        outputArray = ["footage_corners", "cost_corners"]; //куда вписывать результат
        requestAjax(corner_count_obj,outputArray);
    });

    //добавление окон
    $("#add_window_size").on("click",function () {
        limit_window = addWindowSize();
        window_size_obj["wind_size"]["limit_window"] = limit_window;
        window_size_obj["wind_size"][limit_window] = {};
    });

    //удаление окон
    $("#delete_window_size").on("click",function () {
       limit_window = deleteWindowSize();
       window_size_obj["wind_size"]["limit_window"] = limit_window;
       window_size_obj["wind_size"][limit_window] = {};
    });

    //отправка аякс запроса
    function requestAjax(data, outputArray) {
        var url = "controller.php";
        $.ajax({
            type: "POST",
            url: url,
            cache: false,
            data: data,
            dataType: "json",
            success: function(data){
                //console.log(data);
                for(var i = 0; i < outputArray.length; i++ ) {
                    var name = "result_" + outputArray[i];
                    if(name in total_cost_obj){
                        total_cost_obj[name] = data[outputArray[i]];
                        if(total_cost_obj[name] !== undefined)
                        total_cost();

                    };
                    $("#"+name).empty().append(data[outputArray[i]]);
                }
            }
        });
    }
    //добавление размеров окна
    function addWindowSize() {
        var next = $('.window_size > p:last').attr("name");
        next++;
        if(next <= limit) {
            $(".window_size").append(
                " <p name="+next+">\n" +
                "        Ширина <input type=\"text\" size=\"2\" id = \"width_window"+next+"\" onkeydown=\"validate(this)\"> см,\n" +
                "        Высота <input type=\"text\" size=\"2\" id = \"height_window"+next+"\" onkeydown=\"validate(this)\"> см,\n" +
                "        Количество <input type=\"text\" size=\"2\" id = \"number_window"+next+"\" onkeydown=\"validate(this)\"> шт\n" +
                " </p>"
            )
        }
        return next;
    }
    //удаление размеров окна
    function deleteWindowSize() {
        var last = $('.window_size > p:last').attr("name");
        if(last > 1) $("p[name="+last+"]").remove();
        last--;
        return last;
    }

    function total_cost() {
        var total_sum = 0; // общая суммма
        for (var cost in total_cost_obj) {
            total_sum += total_cost_obj[cost];
        }
        console.log(total_cost_obj);
        $("#total_cost").empty().append(total_sum);
    }


});
function validate(inp) {
    inp.value = inp.value.replace(/[^\d.]*/g, '')
        .replace(/([.])[.]+/g, '$1')
        .replace(/^[^\d]*(\d+([.]\d{0,5})?).*$/g, '$1');
}