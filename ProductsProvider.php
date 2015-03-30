<?php

/**
 * @version $Id$
 **/
class ProductsProvider
{
    private $productsArray; // array of items

    /**
     * Read a list of product from Fridge.csv
     * @param $csvFile
     * @return ProductsProvider
     */
    public static function initFromCSV($csvFile)
    {
        $provider = new self;
        $fh = fopen($csvFile, 'r');
        if ($fh !== FALSE) {
            while (($data = fgetcsv($fh, 1000, ",")) !== FALSE) {
                $obj = new stdClass();
                $obj->item = $data[0];
                $obj->amount = (int)$data[1];
                $obj->unit = $data[2];
                $obj->useBy = DateTime::createFromFormat('d/m/Y', $data[3])->format('Y-m-d');
                $p = Product::initFromStdClass($obj);
                $provider->productsArray[] = $p;
            }
        }
        return $provider;
    }

    /**
     * @return Product[]
     */
    public function getProducts()
    {
        return $this->productsArray;
    }

    /**
     * Find out if the given ingredient exist in our provider.
     * @param Ingredient $ingredient
     * @param string $cookingDate
     * @return null or use by date
     */
    public function isIngredientAvailable($ingredient, $cookingDate)
    {
        $item = $ingredient->getItem();
        $amount = $ingredient->getAmount();
        $unit = $ingredient->getUnit();

        foreach ($this->getProducts() as $product) {
            if ($product->getItem() == $item && $product->getAmount() >= $amount && $product->getUnit() == $unit) {
                if (strtotime($product->getUseBy()) >= strtotime($cookingDate)) {
                    return $product->getUseBy();// Found one valid ingredient in products, return the use by date.
                }
            }
        }

        return null;// Not found  in all products for this ingredient, return null.
    }

    /**
     * Get suitable products by ingredients and cooking date, cooking date used to test if a product expired or not.
     * @param Ingredient[] $ingredients
     * @param string|null $cookingDate
     * @return array of use by date
     */
    public function areAllIngredientsAvailable($ingredients, $cookingDate = null)
    {
        if ($cookingDate == null) {
            $cookingDate = date('Y-m-d');
        }
        $useByInfo = array();
        foreach ($ingredients as $ingredient) {
            // Check one ingredient in all products.
            $foundInfo = $this->isIngredientAvailable($ingredient, $cookingDate);
            if(!$foundInfo) return null; // not found one return null, else continue for other ingredients.
            $useByInfo[] = $foundInfo;
        }
        // All ingredients checked, all found
        sort($useByInfo);
        return $useByInfo;
    }

    /**
     * @param RecipesProvider $recipesProvider
     * @param string|null $cookingDate
     * @return string|null $recipeName
     */
    public function findClosetAvailableRecipe($recipesProvider, $cookingDate = null)
    {
        if ($cookingDate == null) {
            $cookingDate = date('Y-m-d');
        }
        $availRecipes = array();
        foreach ($recipesProvider->getRecipes() as $recipe) {
            $useByInfo = $this->areAllIngredientsAvailable($recipe->getIngredients(), $cookingDate);
            if (!empty($useByInfo)) {
                $key = implode(',', $useByInfo);
                $availRecipes[$key] = $recipe->getName();
            }
        }

        return $this->pickClosetRecipeFromAvailable($availRecipes);
    }

    /**
     * Pick a recipe according to its products' use by date
     *
     * For example, the key would like
     * '2013-01-12,2014-12-01'
     * '2013-01-12,2014-12-01,2014-12-26'
     * '2014-12-01,2014-12-16'
     * Then, the first one get picked up.
     *
     * @param Array of recipe name $availRecipes
     * @return mixed
     */
    public function pickClosetRecipeFromAvailable($availRecipes)
    {
        if(empty($availRecipes)) return null;
        $keys = array_keys($availRecipes);
        sort($keys);
        $closestKey = $keys[0];
        return $availRecipes[$closestKey];
    }

}

return;
include_once 'BootStrap.php';
$p = ProductsProvider::initFromCSV('fridge.csv');
print_r($p);

$i1 = new stdClass();
$i1->item = 'bread';
$i1->amount = 10;
$i1->unit = 'slices';

$ingredients = array($i1);
$a = $p->findClosetAvailableRecipe($ingredients, ' 2013-01-01');
print_r($a);