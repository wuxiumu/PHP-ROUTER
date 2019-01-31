<?php

require 'Ms.php';

use Ms;

Ms::get('/', function() {
  echo "Welcome";
});

Ms::get('/name/(:all)', function($name) {
  echo 'Your name is '.$name;
});

Ms::error(function() {
  echo '404';
});

Ms::dispatch();
