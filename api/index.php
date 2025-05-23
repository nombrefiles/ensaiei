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
$route->get("/{username}", "Users:listUserByUsername");

$route->post("/add", "Users:createUser");

$route->put("/update", "Users:updateUser");

$route->delete("/delete/{id}", "Users:deleteUser"); //fazer metodo SOFTDELETE


/* PLAYS */
$route->group("/plays");

$route->get("/", "Plays:listPlays");
$route->get("/{id}", "Plays:listPlayById");

$route->post("/add", "Plays:createPlay");

$route->put("/update", "Plays:updatePlay");

$route->delete("/delete/{id}", "Plays:deletePlay"); //fazer metodo


/* ACTORS  */
$route->group("/actors");

$route->get("/", "Actors:listActors");
$route->get("/{id}", "Actors:listActorById");

$route->post("/add", "Actors:createActor");

$route->put("/update", "Actors:updateActor");

$route->delete("/delete/{id}", "Actors:deleteActor"); //fazer metodo


/*  COSTUMES  */
$route->group("/costumes");

$route->get("/", "Costumes:listCostumes");
$route->get("/{id}", "Costumes:listCostumeById");

$route->post("/add", "Costumes:createCostume");

$route->put("/update", "Costumes:updateCostume");

$route->delete("/delete/{id}", "Costumes:deleteCostumeById"); //fazer metodo

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