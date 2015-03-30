<?php
/**
  * @version $Id$
**/
include_once 'BootStrap.php';

$cookingDate = '2013-01-01';
$recipesProvider = RecipesProvider::initFromJson('recipes.json');
$productsProvider = ProductsProvider::initFromCSV('fridge.csv');
$recipeName = $productsProvider->findClosetAvailableRecipe($recipesProvider,$cookingDate);
echo "\nFor today ($cookingDate), the best one for you to cook is $recipeName.\n";
