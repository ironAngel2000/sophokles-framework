<?php

spl_autoload_register(function ($classRequierd) {
    $arrCls = explode('\\',$classRequierd);
    $className = end($arrCls);
    $path = str_replace($className,'',$classRequierd);
    $path = strtolower($path);
    $path = str_replace('\\','/',$path);
    $path = str_replace('sophokles/','',$path);
    $path = str_replace('system/','',$path);
    $baseDir = str_replace('/system','',__DIR__);
    $file = $baseDir.'/'.$path.$className.'.php';

    if(is_file($file)){
        require_once $file;
    }
    else{
        $path .= 'class/';
        $file = $baseDir.'/'.$path.$className.'.php';
        if(is_file($file)){
            require_once $file;
        }
        else{
            dir(($file));
        }
    }

});

