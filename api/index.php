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

$route = new Router("http://localhost/ensaiei-main/api",":");

$route->namespace("Source\WebService");

/* USERS */

$route->group("/users");

$route->post("/login", "Users:login"); // funciona

$route->get("/", "Users:listUsers"); // funciona
$route->get("/{username}", "Users:listUserByUsername"); //funciona

$route->post("/add", "Users:createUser"); // funciona

$route->put("/update", "Users:updateUser"); // funciona

$route->delete("/delete", "Users:deleteUser"); // funciona

$route->post("/photo", "Users:updatePhoto"); // funciona

$route->get("/perfil", "Users:getLoggedUser"); // funciona

$route->put("/password", "Users:changePassword"); // nao funciona


/* ATTRACTIONS */
$route->group("/attraction");

$route->get("/{id}", "Attractions:listAttractionById"); // funciona
$route->get("/event/{eventId}", "Attractions:listAttractionsByEvent"); // funciona
$route->get("/event/{eventId}/type/{type}", "Attractions:listAttractionsByEvent"); // falta fazer o type
$route->post("/{eventId}/add", "Attractions:createAttraction"); // funciona
$route->put("/update/{id}", "Attractions:updateAttraction"); // ok
$route->delete("/delete/{id}", "Attractions:deleteAttraction");  // ok

$route->group("null");

$route->group("/event");

$route->get("/", "Events:listEvents"); // funciona
$route->get("/{id}", "Events:listEventById"); // funciona

//$route->get("/{search}", "Plays:listPlayByName"); // FAZER

$route->post("/add", "Events:createEvent"); // funciona

$route->put("/update/{id}", "Events:updateEvent"); // funciona

$route->delete("/delete/{id}", "Events:deleteEvent");  // funciona

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