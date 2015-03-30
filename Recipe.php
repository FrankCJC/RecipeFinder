<?php

/**
 * @version $Id$
 **/
class Recipe
{
    private $name;
    private $ingredientsArray; // array of class Ingredient

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function setIngredients($ingredientsArr)
    {
        $this->ingredientsArray = $ingredientsArr;
        return $this;
    }

    public function getIngredients()
    {
        return $this->ingredientsArray;
    }

    public function getName()
    {
        return $this->name;
    }
}
