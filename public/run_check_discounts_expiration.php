<?php

// Define the URL of your Laravel application dynamically
$laravelUrl = 'http://' . $_SERVER['HTTP_HOST'] . '/check-discounts-expiration'; // Construct the URL using $_SERVER['HTTP_HOST']

// Make a GET request to the route using file_get_contents
$response = file_get_contents($laravelUrl);

// Output the response
echo $response;
