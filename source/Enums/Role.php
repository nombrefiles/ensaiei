<?php

namespace Source\Enums;

enum Role: string
{
case ADMIN = "Administrador";
case STANDART = "Comum";
case ORGANIZER = "Organizador";
case PERFORMER = "Performer";
}