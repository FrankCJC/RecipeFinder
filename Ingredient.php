<?php
/**
  * @version $Id$
**/

class Ingredient
{
    protected $item;
    protected $amount;
    protected $unit;


    public static function initFromStdClass($std)
    {
        $instance = new self;
        $instance->item = $std->item;
        $instance->amount = $std->amount; // todo add some sanity check
        $instance->unit = $std->unit;
        return $instance;
    }

    public function getItem()
    {
        return $this->item;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function getUnit()
    {
        return $this->unit;
    }
}

