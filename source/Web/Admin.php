<?php

namespace Source\Web;

class Admin extends Controller
{
    public function __construct()
    {
        parent::__construct("admin");
    }

    public function profile(): void
    {
        echo $this->view->render("profile", []);
    }

    public function about(): void
    {
        echo $this->view->render("about", []);
    }

    public function faqs(): void
    {
        echo $this->view->render("faqs", []);
    }

    public function events(): void
    {
        echo $this->view->render("events", []);
    }

    public function logOut(): void
    {
        echo $this->view->render("logout", []);
    }

    public function error(array $data): void
    {
        echo "ERRO {$data["errcode"]}...";
    }

}