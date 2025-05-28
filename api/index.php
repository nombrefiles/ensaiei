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


/* PLAYS */
$route->group("/plays");

$route->get("/", "Plays:listPlays"); // ok
$route->get("/{id}", "Plays:listPlayById"); // ok

$route->post("/add", "Plays:createPlay"); // falta fazer a costumes

$route->put("/update/{id}", "Plays:updatePlay"); // falta fazer

$route->delete("/delete/{id}", "Plays:deletePlay");  // nem fiz

/* ACTORS  */

// acho que pode tudo ser um user por sinal... acho meio inutil ter uma classe actors
// ou isso ou fazer uma forma de mostrar as peças que os atores estão no listActors
$route->group("/actors");

$route->get("/", "Actors:listActors");  // ok
$route->get("/{id}", "Actors:listActorById"); // ok

$route->post("/add", "Actors:createActor"); // falta adicionar para a tabela actors caso eles ja estejam em alguma peca

$route->put("/update", "Actors:updateActor"); // falta fazer

$route->delete("/delete/{id}", "Actors:deleteActor"); // falta fazer


/*  COSTUMES  */
$route->group("/costumes");

$route->get("/", "Costumes:listCostumes"); // nao sei
$route->get("/{id}", "Costumes:listCostumeById"); // nao sei

$route->post("/add", "Costumes:createCostume"); // nao sei

$route->put("/update", "Costumes:updateCostume"); // falta fazer

$route->delete("/delete/{id}", "Costumes:deleteCostumeById"); // falta fazer

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