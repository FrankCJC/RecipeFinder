<?php
/**
  * @version $Id$
**/

class RecipesProvider
{
    private $recipesObjArr; // Array of class Recipe

    /**
     * @return Recipe[]
     */
    public function getRecipes()
    {
        return $this->recipesObjArr;
    }

    /**
     * @param $jsonFile
     * @return RecipesProvider
     */
    public static function initFromJson($jsonFile)
    {
        $provider = new self;
        $recipesJson = json_decode(file_get_contents($jsonFile));

        foreach ($recipesJson as $objReceipt) {
            $ingredients = array();
            foreach ($objReceipt->ingredients as $ingredientObj) {
                $ingredients[] = Ingredient::initFromStdClass($ingredientObj);
            }
            $recipe = new Recipe($objReceipt->name);
            $provider->recipesObjArr[] = $recipe->setIngredients($ingredients);
        }
        return $provider;
    }

    /**
     * @return Recipes[]
     */
    public function getRecipeNameIngredients()
    {
        return $this->recipesObjArr;
    }

}
