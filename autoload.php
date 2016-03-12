<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include('../log4php/Logger.php');
Logger::configure('config.xml');

spl_autoload_register(function ($class_name) {
    if (file_exists($class_name . '.class.php')) {
    include $class_name . '.class.php';
    }
});
