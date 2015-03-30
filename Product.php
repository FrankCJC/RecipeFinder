<?php
/**
  * @version $Id$
**/
/**
 * Class Product
 *
 * Product is Ingredient with a due date, to avoid confusion with an ingredient, we use product here.
 */
class Product extends Ingredient
{
    protected $useBy;

    public static function initFromStdClass($std)
    {
        $instance = new self;
        $instance->item = $std->item;
        $instance->amount = $std->amount; // todo add some sanity check
        $instance->unit = $std->unit;
        $instance->useBy = $std->useBy; // todo add some sanity check
        return $instance;
    }

    public function getUseBy()
    {
        return $this->useBy;
    }
}