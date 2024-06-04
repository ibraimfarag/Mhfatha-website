<?php

// Define the URL of your Laravel application dynamically
// $laravelUrl = 'http://mhfatha.net/api/update-discounts';

// Make a GET request to the route using file_get_contents
$response = file_get_contents($laravelUrl);

// Output the response
echo $response;
