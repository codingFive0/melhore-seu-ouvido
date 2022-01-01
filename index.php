<?php ob_start();
require __DIR__ . "/vendor/autoload.php";

(new \Source\Core\Session());

$route = (new \Source\Core\Routes(URL()));

$route->namespace("Source\App");
$route->get("/", "Web:home", "web.home");
$route->post("/questoes", "Web:questions", "web.questions");
$route->get("/questoes", "Web:questions", "web.questions");
$route->post("/resposta", "Web:response", "web.response");
$route->get("/desafio", "Web:challenge", "web.challenge");


/* Errors */
$route->get("/erro/{code}", "Errors:erro", "errors.erro");


$route->dispatch();
/**
 * ERROR REDIRECT
 */
if ($route->error()) {
    redirect(url("/erro/{$route->error()}"));
}

ob_end_flush();