<?php

namespace Source\Web;

class Site extends Controller
{
    public function __construct()
    {
        parent::__construct("web");
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

    public function error(array $data): void
    {
        echo "ERO {$data["errcode"]}...";
    }
}