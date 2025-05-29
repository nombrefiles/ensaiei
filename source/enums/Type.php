<?php

namespace Source\enums;

enum Type: string
{
    case MUSIC = 'Apresentação musical';
    case VISUAL = 'Exposição visual';
    case CINEMA = 'Apresentação cinematográfica';
    case THEATER = 'Apresentação teatral';
    case DANCE = 'Apresentação de dança';
    case OTHER = 'Apresentação diversa';
}
