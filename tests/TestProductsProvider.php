<?php
/**
 * @version $Id$
 **/
include_once '../BootStrap.php';

class TestProductsProvider extends SimpleTest
{
    public function testPickClosetRecipeFromAvailable()
    {
        /** For example, the key would like
         * '2013-01-12,2014-12-01'
         * '2013-01-12,2014-12-01,2014-12-26'
         * '2014-12-01,2014-12-16'
         * Then, the first one get picked up.
         **/
        $availRecipes = array(
            '2013-01-12,2014-12-01' => array(1),
            '2013-01-12,2014-12-01,2014-12-26' => array(2),
            '2014-12-01,2014-12-16' => array(3)
        );
        $pp = new ProductsProvider();
        $result = $pp->pickClosetRecipeFromAvailable($availRecipes);
        $this->assertEquals($result, array(1));
    }

    public function testPickClosetRecipeFromAvailable2()
    {
        /** For example, the key would like
         * '2013-01-12,2014-12-01'
         * '2013-01-12,2014-12-01,2014-12-26'
         * '2014-12-01,2014-12-16'
         * Then, the first one get picked up.
         **/
        $availRecipes = array(
            '2013-01-12,2014-12-01,2014-12-26' => array(2),
            '2013-01-12,2014-12-01' => array(1),
            '2014-12-01,2014-12-16' => array(3)
        );
        $pp = new ProductsProvider();
        $result = $pp->pickClosetRecipeFromAvailable($availRecipes);
        $this->assertEquals($result, array(1));
    }

    public function testIsIngredientAvailableDateNormal()
    {
        /**
         * bread,10,slices,25/12/2014
         * cheese,10,slices,25/12/2014
         */
        $products = ProductsProvider::initFromCSV('TestFridge.csv');

        $stdClass = new stdClass();
        $stdClass->item = 'bread';
        $stdClass->amount = 2;
        $stdClass->unit = 'slices';
        $cookingDate = '2014-12-01';

        $ingredient = Ingredient::initFromStdClass($stdClass);

        $result = $products->isIngredientAvailable($ingredient, $cookingDate);
        $this->assertNotEquals($result, null);
    }

    public function testIsIngredientAvailableDateDue()
    {
        /**
         * bread,10,slices,25/12/2014
         * cheese,10,slices,25/12/2014
         */
        $products = ProductsProvider::initFromCSV('TestFridge.csv');

        $stdClass = new stdClass();
        $stdClass->item = 'bread';
        $stdClass->amount = 2;
        $stdClass->unit = 'slices';
        $cookingDate = '2014-12-27'; // Cooking date pass use by date.

        $ingredient = Ingredient::initFromStdClass($stdClass);

        $result = $products->isIngredientAvailable($ingredient, $cookingDate);
        $this->assertEquals($result, null);
    }

    public function testIsIngredientAvailableDateEquals()
    {
        /**
         * bread,10,slices,25/12/2014
         * cheese,10,slices,25/12/2014
         */
        $products = ProductsProvider::initFromCSV('TestFridge.csv');

        $stdClass = new stdClass();
        $stdClass->item = 'bread';
        $stdClass->amount = 2;
        $stdClass->unit = 'slices';
        $cookingDate = '2014-12-25'; // Cooking date the same as use by date.

        $ingredient = Ingredient::initFromStdClass($stdClass);

        $result = $products->isIngredientAvailable($ingredient, $cookingDate);
        $this->assertEquals($result, '2014-12-25');
    }

    public function testIsIngredientAvailableAmountUnitDiff()
    {
        /**
         * bread,10,slices,25/12/2014
         * cheese,10,slices,25/12/2014
         */
        $products = ProductsProvider::initFromCSV('TestFridge.csv');
        $cookingDate = '2014-12-01';

        $stdClass = new stdClass();
        $stdClass->item = 'bread';
        $stdClass->amount = 2;
        $stdClass->unit = 'grams';

        $ingredient = Ingredient::initFromStdClass($stdClass);

        $result = $products->isIngredientAvailable($ingredient, $cookingDate);
        $this->assertEquals($result, null); // Not found, since unit is diff

        $stdClass = new stdClass();
        $stdClass->item = 'bread';
        $stdClass->amount = 12; // amount is more than 10
        $stdClass->unit = 'slices';

        $ingredient = Ingredient::initFromStdClass($stdClass);

        $result = $products->isIngredientAvailable($ingredient, $cookingDate);
        $this->assertEquals($result, null); // Not found, not enough amount

        $stdClass = new stdClass();
        $stdClass->item = 'bread';
        $stdClass->amount = 10; // amount is just enough, edge case.
        $stdClass->unit = 'slices';

        $ingredient = Ingredient::initFromStdClass($stdClass);

        $result = $products->isIngredientAvailable($ingredient, $cookingDate);
        $this->assertEquals($result, '2014-12-25'); // found, just enough amount
    }

    public function testAreAllIngredientsAvailable()
    {
        /**
         * bread,10,slices,25/12/2014
         * cheese,10,slices,25/12/2014
         * ...
         * mixed salad,500,grams,26/12/2013
         */
        $products = ProductsProvider::initFromCSV('TestFridge.csv');

        $stdClass = new stdClass();
        $stdClass->item = 'bread';
        $stdClass->amount = 2;
        $stdClass->unit = 'slices';

        $ingredient1 = Ingredient::initFromStdClass($stdClass);

        $stdClass->item = 'mixed salad';
        $stdClass->amount = 20;
        $stdClass->unit = 'grams';
        $ingredient2 = Ingredient::initFromStdClass($stdClass);

        $cookingDate = '2013-12-26'; // set cooking date to 26/12/2013 so there are 2 ingredients available.

        $result = $products->areAllIngredientsAvailable(array($ingredient1, $ingredient2), $cookingDate);
        $this->assertEquals(count($result),2); // found 2 ingredients available

        $cookingDate = '2014-12-06'; // set cooking date to 6/12/2014, so only one ingredient available.

        $result = $products->areAllIngredientsAvailable(array($ingredient1, $ingredient2), $cookingDate);
        $this->assertEquals(count($result),0); // found all ingredients available, return null.
    }

    public function testFindClosetAvailableRecipe()
    {
        /**
         * bread,10,slices,25/12/2014
         * cheese,10,slices,25/12/2014
         * ...
         * mixed salad,500,grams,26/12/2013
         */
        $products = ProductsProvider::initFromCSV('TestFridge.csv'); // use test product file
        $recipes = RecipesProvider::initFromJson('TestRecipes.json'); // use test recipes file

        $cookingDate = '2013-12-26'; // set cooking date to 26/12/2013 so there are 2 ingredients available.

        $result = $products->FindClosetAvailableRecipe($recipes, $cookingDate);
        $this->assertEquals($result, 'salad sandwich'); // found the closest use by date

        $cookingDate = '2014-12-06'; // set cooking date to 6/12/2014, so only one ingredient available.

        $result = $products->FindClosetAvailableRecipe($recipes, $cookingDate);
        $this->assertEquals($result, 'grilled cheese on toast'); // found all ingredients available, return null.
    }
}


SimpleTest::run('TestProductsProvider');