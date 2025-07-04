<?php

namespace Source\Web;

class App extends Controller
{
    public function __construct()
    {
        parent::__construct("app");
    }

    public function profile(): void
    {
        echo "PERFIL";
    }

    public function home(): void
    {
        echo $this->view->render("home", []);
    }

    public function about(): void
    {
        echo "SOBRE NÓS";
    }

    public function contact(): void
    {
        echo "CONTATOOO";
    }

    public function faqs(): void
    {
        echo "perguntinhas...";
    }

    public function logIn(): void
    {
        echo "#logando";
    }

    public function sigIn(): void
    {
        echo "cadastro";
    }

    public function events(): void
    {
        echo "seus eventos.... MEUS?";
    }

    public function attractions(array $data): void
    {
        echo "ATRAÇÕES";
    }

    public function error(array $data): void
    {
        echo "ERO {$data["errcode"]}...";
    }

}