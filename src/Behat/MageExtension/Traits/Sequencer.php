<?php

namespace Behat\MageExtension\Traits;

trait Sequencer {

    private $_sequences = array();

    public function nextValue($key, $func = null)
    {
        if($func === null) {
            $func = function($i) use($key) {
                return $key . '_' . $i;
            };
        }
        if(!isset($this->_sequences[$key])) {
            $this->_sequences[$key] = array();
        }
        $index = count($this->_sequences[$key]);
        $this->_sequences[$key][$index] = $func($index);
        return $this->_sequences[$key][$index];
    }

}
