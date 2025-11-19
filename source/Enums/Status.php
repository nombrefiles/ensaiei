<?php

namespace Source\Enums;

enum Status: string
{
    case PENDING = "PENDING";
    case APPROVED = "APPROVED";
    case REJECTED = "REJECTED";
}
