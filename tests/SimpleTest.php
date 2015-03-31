<?php
/**
  * @version $Id$
**/

/**
 * Class simpleTest
 *
 * Really simplified test to avoid including phpunit framework
 */
class SimpleTest
{
    public function assertTrue($condition)
    {
        if($condition==true){
            echo "Test passed\n";
        }else{
            die("Test failed\n");
        }
    }

    public function assertFalse($condition)
    {
        $this->assertTrue(!$condition);
    }

    public function assertEquals($condition1, $condition2)
    {
        if($condition1===$condition2){
           echo "Test passed\n";
        }else{
            echo("Fail to assert ");
            print_r($condition1);
            echo " equals to ";
            print_r($condition2);
            echo ",Test failed\n";
        }
    }

    public function assertNotEquals($condition1, $condition2)
    {
        if($condition1!==$condition2){
           echo "Test passed\n";
        }else{
            echo("Fail to assert ");
            print_r($condition1);
            echo " equals to ";
            print_r($condition2);
            echo ",Test failed\n";
        }
    }

    public static function run($className)
    {
        $r = new ReflectionClass($className);
        foreach($r->getMethods() as $methodObj){
            if(substr($methodObj->name,0,4)=='test'){
                $class = $r->newInstance();
                $name = $methodObj->name;
                echo "Running $name...\n";
                $class->$name();
            }
        }
        //print_r($r->getMethods());
    }
}

