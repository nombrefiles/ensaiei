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

$route = new Router("http://localhost/ensaiei-main/api", ":");

$route->namespace("Source\WebService");

/* USERS */
$route->group("/users");
$route->post("/login", "Users:login");
$route->get("/", "Users:listUsers");
$route->get("/{username}", "Users:listUserByUsername");
$route->post("/add", "Users:createUser");
$route->put("/update", "Users:updateUser");
$route->delete("/delete", "Users:deleteUser");
$route->post("/photo", "Users:updatePhoto");
$route->get("/perfil", "Users:getLoggedUser");
$route->put("/password", "Users:changePassword");
$route->post("/verifyemail", "Users:verifyEmail");
$route->post("/resendcode", "Users:resendVerificationCode");
$route->delete("/cancelregistration", "Users:cancelRegistration");
$route->group(null);

/* ATTRACTIONS */
$route->group("/attraction");
$route->get("/{id}", "Attractions:listAttractionById");
$route->get("/event/{eventId}", "Attractions:listAttractionsByEvent");
$route->get("/event/{eventId}/type/{type}", "Attractions:listAttractionsByEvent");
$route->post("/{eventId}/add", "Attractions:createAttraction");
$route->put("/update/{id}", "Attractions:updateAttraction");
$route->delete("/delete/{id}", "Attractions:deleteAttraction");
$route->group(null);

/* EVENTS */
$route->group("/event");
$route->get("/", "Events:listEvents");
$route->get("/my", "Events:listMyEvents");
$route->get("/{id}", "Events:listEventById");
$route->post("/add", "Events:createEvent");
$route->put("/update/{id}", "Events:updateEvent");
$route->delete("/delete/{id}", "Events:deleteEvent");
$route->get("/{eventId}/photos", "EventPhotos:listPhotosByEvent");
$route->post("/{eventId}/photos", "EventPhotos:uploadPhotos");
$route->put("/photos/{photoId}/main", "EventPhotos:setMainPhoto");
$route->delete("/photos/{photoId}", "EventPhotos:deletePhoto");

$route->group(null);

$route->group("/admin");
$route->put("/events/{id}/approve", "AdminEvents:approveEvent");
$route->put("/events/{id}/reject", "AdminEvents:rejectEvent");
$route->get("/events/pending", "AdminEvents:listPendingEvents");
$route->get("/events/stats", "AdminEvents:getEventStats");
$route->group(null);

$route->dispatch();

if ($route->error()) {
    header('Content-Type: application/json; charset=UTF-8');
    http_response_code(404);

    echo json_encode([
        "code" => 404,
        "status" => "not_found",
        "message" => "URL não encontrada",
        "requested_route" => $_GET['route'] ?? 'N/A'
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}

ob_end_flush();