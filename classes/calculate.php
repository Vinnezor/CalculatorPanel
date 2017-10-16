<?php


class CalculateClass
{
    protected $result_array = array();
    protected $coefficient;
    protected $markup;
    protected $expense_bucket;
    protected $volume_bucket;
    protected $cost_bucket;
    protected $panel_cost;
    protected $cost_framing_window;
    protected $cost_corner;
    protected $add_percent_panels;
    protected $count_inp;
    protected $coefficient_multiply;
    //получаем массив настроек
    public function __construct($settings){
        $this->coefficient = $settings["coefficient"];
        $this->markup = $settings["markup"];
        $this->expense_bucket = $settings["expense_bucket"];
        $this->volume_bucket = $settings["volume_bucket"];
        $this->cost_bucket = $settings["cost_bucket"];
        $this->panel_cost = $settings["panel_cost"];
        $this->cost_framing_window = $settings["cost_framing_window"];
        $this->cost_corner = $settings["cost_corner"];
        $this->add_percent_panels = $settings["add_percent_panels"];
        $this->count_inp = $settings["count_inp"];
        $this->coefficient_multiply = $settings["coefficient_multiply"];
    }

    // расчет панелей формула
   public function getCountOfPanels($area) {
       $count_panels = $this->rounding($area * $this->coefficient);
       $cost_panels = $count_panels * $this->panel_cost;
       $proc = $this->getToProcent($count_panels,$this->add_percent_panels);
       $count_panels = $this->rounding($proc + $count_panels);
       $weight_pc = $this->getWeight($area);
       $this->result_array["count_panels"] = $count_panels;
       $this->result_array["cost_panels"] = $cost_panels;
       $this->result_array["weight_pc"] = $weight_pc;
       $this->result_array["protective_covering"] = $this->getProtectiveCoating($weight_pc);
       return $this->result_array;
    }

    //размеры окон
    public function getSizeWindow($wind_size) {
        $limit_window = $wind_size["limit_window"];
        $middle_result = array();
        unset($wind_size["limit_window"]);
        for ($i = 1; $i <= $limit_window; $i++) {
            if (isset($wind_size[$i]) && count($wind_size[$i]) == $this->count_inp) {
                $middle_result[] = $this->getCmToM($wind_size[$i]["width_window" . $i]) *
                                   $this->getCmToM($wind_size[$i]["height_window" . $i]) *
                                   $this->coefficient_multiply *
                                   $wind_size[$i]["number_window" . $i];
            }
        }
        //обрабатываем результаты
        if(count($middle_result) != 0) {
            $result_footage = 0;
            for ($i = 0; $i < count($middle_result) + 1; $i++) {
                if(isset($middle_result[$i])){
                    $result_footage = $result_footage + $middle_result[$i];
                }
            }
            $proc =  $this->getToProcent($result_footage,$this->markup); // вычисляем проценты
            $result_footage =$result_footage + $proc;
            $result_footage = $this->rounding($result_footage, 2); //округление кратно 2 в большую сторону
            $this->result_array["footage"]  = $result_footage;
            $this->result_array["cost_window"] =  $result_footage * $this->cost_framing_window;
        }
        return $this->result_array;
    }

    //углы дома
    public function getCornersCount($corner_count, $height_corners){
        $footage_corners = $this->rounding($corner_count * $height_corners, 2); //округление кратно 2 в большую сторону
        $cost_corners = $footage_corners * $this->cost_corner;
        $this->result_array["footage_corners"] = $footage_corners;
        $this->result_array["cost_corners"] = $cost_corners;
        return $this->result_array;
    }

    //расчет защитного покрытия
    protected function getProtectiveCoating($weight_pc) {
        return $weight_pc  * $this->cost_bucket;
    }
    protected function getWeight($area) {
        $weight_pc = ($area * $this->expense_bucket) / $this->volume_bucket;
        return  $this->rounding($weight_pc) * $this->volume_bucket;
    }
    //приведение из м в см
    protected function getCmToM($param) {
        return $param / 100;
    }
    //получение процентов из числа
    protected function getToProcent($param, $count_proc){
        return ($param / 100) * $count_proc;
    }

    //округление по умолчанию в большую сторону
    protected function rounding($param, $number = 0){
        //округление  в большую сторону
        if($number == 0)
            return round($param, 0 , PHP_ROUND_HALF_UP);
        //округление кртано 2, 5, 10 и т.д
        return ceil($param/$number)*$number;

    }


    function __destruct() {
    }
}

?>