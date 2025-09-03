<?php

require __DIR__ . "/vendor/autoload.php";

use CoffeeCode\Router\Router;

ob_start();

$route = new Router("http://localhost/ensaiei-main", ":");

$route->namespace("Source\Web");
// Rotas amigáveis da área pública
$route->get("/", "Site:home");
$route->get("/sobre", "Site:about");
$route->get("/faqs","Site:faqs");
$route->get("/login","Site:login");
$route->get("/cadastro","Site:register");

// Rotas amigáveis da área restrita
$route->group("/app");
$route->get("/perfil", "App:profile");
$route->get("/eventos","App:events");
$route->get("/bye","App:logOut");
$route->get("/sobre", "App:about");
$route->get("/faqs","App:faqs");
$route->get("/hi","App:home");
$route->group(null);


$route->group("/admin");
$route->get("/", "Admin:home");
$route->get("/perfil", "Admin:profile");
$route->get("/eventos","Admin:events");
$route->get("/bye","Admin:logout");
$route->get("/sobre", "App:about");
$route->get("/faqs","App:faqs");
$route->group(null);

$route->get("/ops/{errcode}", "Site:error");

$route->group(null);

$route->dispatch();

if ($route->error()) {
    $route->redirect("/ops/{$route->error()}");
}

ob_end_flush();