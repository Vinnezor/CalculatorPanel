<?php

require "classes/calculate.php"; // модель для расчета
require "classes/output.php"; // Модель для вывода
require "settings/settings.php"; //настройки констант

$result = array();
$calculate = new CalculateClass($settings);

//расчет размеров окна
if(isset($_POST["wind_size"])) {

    $result = $calculate->getSizeWindow($_POST["wind_size"]);
    Output::out($result);
}
//расчет стоимости углов
if(isset($_POST["corner_count"])) {
    if (count($_POST["corner_count"]) == $settings["corner_term"]) {
        $count_corners = $_POST["corner_count"]["count_corners"];
        $height_corners = $_POST["corner_count"]["height_corners"];
        $result = $calculate->getCornersCount($count_corners,$height_corners);
        Output::out($result);
    }
}

//расчет стоимости панелей\
if(isset($_POST["count_panels"])) {
    $result = $calculate -> getCountOfPanels($_POST["count_panels"]);
    Output::out($result);
  }


