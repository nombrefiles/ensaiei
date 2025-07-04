<?php

require __DIR__ . "/vendor/autoload.php";

use CoffeeCode\Router\Router;

ob_start();

$route = new Router("http://localhost/ensaiei-main", ":");

$route->namespace("Source\Web");
// Rotas amigáveis da área pública
$route->get("/", "Site:home");
$route->get("/sobre", "Site:about");
$route->get("/contato", "Site:contact");
$route->get("/faqs","Site:faqs");
$route->get("/login","Site:login");
$route->get("/cadastro","Site:register");

// Rotas amigáveis da área restrita
$route->group("/app");
$route->get("/", "App:home");
$route->group(null);

$route->group("/admin");
$route->get("/", "Admin:home");
$route->group(null);

$route->get("/ops/{errcode}", "Site:error");

$route->group(null);

$route->dispatch();

if ($route->error()) {
    $route->redirect("/ops/{$route->error()}");
}

ob_end_flush();