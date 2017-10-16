<?php
// статичный класс для вывода в js
class Output
{
    public static function out ($outArray){
        print_r(json_encode($outArray));
    }
}