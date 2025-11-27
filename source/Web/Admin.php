<?php

namespace Source\Web;

class Admin extends Controller
{
    public function __construct()
    {
        parent::__construct("admin");
    }

    public function home(): void
    {
        echo $this->view->render("home", []);
    }

    public function profile(): void
    {
        echo $this->view->render("profile", []);
    }

    public function about(): void
    {
        echo $this->view->render("about", []);
    }

    public function events(): void
    {
        echo $this->view->render("events", []);
    }

    public function logout(): void
    {
        echo $this->view->render("logout", []);
    }

    public function error(array $data): void
    {
        echo "ERRO {$data["errcode"]}...";
    }
}