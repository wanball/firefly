<?php
require 'plugins/claviska/SimpleImage.php';

// Ignore notices
error_reporting(E_ALL & ~E_NOTICE);

$File = $_GET['p'];

isset($_GET['w']) ? $width = $_GET['w'] : $width = 500;
isset($_GET['h']) ? $height = $_GET['h'] : $height = 500;

try {
  // Create a new SimpleImage object
  $image = new \claviska\SimpleImage();

  // Manipulate it
  $image
    ->fromFile($File)             
    ->autoOrient()
    ->thumbnail($width , $height,  'center')                      
    ->toScreen();                      // output to the screen

} catch(Exception $err) {
  // Handle errors
  echo $err->getMessage();
}