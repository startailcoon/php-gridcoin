<?php

namespace CoonDesign\phpGridcoin;

/**
 * Coin is a class to handle the monetary value of Gridcoin
 * 
 * Pass the value to the add() method 
 * Get the value as float with getFloat() or as int with getInt()
 */
class Coin {

    private int $decimals = 8;  // Gridcoin default decimals
    private int $value = 0;

    public function __construct(int|float $v = 0) {
        $this->add($v);
    }
    
    public function add(int|float $v) {

        if(is_float($v)) {
            $precision = strlen(substr(strrchr($v, "."), 1));
            $v = intval(str_replace(".", "", $v));

            for($c = 0;$c < $this->decimals - $precision;$c++) {
                $v .= "0";
            }
        } 

        $this->value += $v;
    }

    public function getFloat() {
        return $this->value / 100000000;
    }

    public function getInt() {
        return $this->value;
    }

}

?>