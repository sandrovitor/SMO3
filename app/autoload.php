<?php

function __autoload($classname) {
    if(strrpos($classname, 'Controller') !== false) {
        require_once('Controllers/'.$classname.'.php');
    }
}
