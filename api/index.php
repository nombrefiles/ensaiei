<?php

ob_start();

require  __DIR__ . "/../vendor/autoload.php";

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header('Access-Control-Allow-Credentials: true');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

use CoffeeCode\Router\Router;

$route = new Router("http://localhost/ensaiei/api",":");

$route->namespace("Source\WebService");

/* USERS */

$route->group("/users");

$route->post("/login", "Users:login"); // ok

$route->get("/", "Users:listUsers"); //ok
$route->get("/{username}", "Users:listUserByUsername"); //ok

$route->post("/add", "Users:createUser"); //ok

$route->put("/update", "Users:updateUser"); // ok

$route->delete("/delete", "Users:deleteUser"); // ok


/* ATTRACTIONS */
$route->group("/attraction");

$route->get("/", "Attraction:listAttractions"); // ok
$route->get("/{id}", "Attraction:listAttractionById"); // ok
//$route->get("/{search}", "Plays:listPlayByName"); // FAZER

$route->post("/{eventId}/add", "Attractions:createAttraction"); // ok

$route->put("/update/{eventId}/{id}", "Attractions:updateAttraction"); // ok

$route->delete("/delete/{id}", "Attractions:deleteAttraction");  // ok

$route->group("null");

$route->dispatch();



if ($route->error()) {
    header('Content-Type: application/json; charset=UTF-8');
    http_response_code(404);

    echo json_encode([
        "code" => 404,
        "status" => "not_found",
        "message" => "URL não encontrada"
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

}

ob_end_flush();