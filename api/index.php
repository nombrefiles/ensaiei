<?php

ob_start();

require  __DIR__ . "/../vendor/autoload.php";

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header('Access-Control-Allow-Credentials: true'); // Permitir credenciais

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

use CoffeeCode\Router\Router;

$route = new Router("http://localhost/ensaiei/api",":");

$route->namespace("Source\WebService");

/* USERS */

$route->group("/users");

$route->post("/login", "Users:login");

$route->get("/", "Users:listUsers");
$route->get("/{id}", "Users:listUserById");
$route->post("/add", "Users:createUser");
$route->put("/update", "Users:updateUser");

/* PLAYS */
$route->group("/plays");

$route->get("/", "Plays:listPlays");
$route->get("/{id}", "Plays:listPlayById");

$route->post("/add", "Plays:createPlay");
$route->put("/update", "Plays:updatePlay");

$route->group("null");

$route->dispatch();

/** ERROR REDIRECT */
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