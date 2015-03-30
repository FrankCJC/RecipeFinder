<?php
/**
  * @version $Id$
**/
function __autoload($class_name) {
    include $class_name . '.php';
}