<?php

namespace Source\Support;

use CoffeeCode\Paginator\Paginator;

/**
 * FSPHP | Class Pager
 *
 * @author Robson V. Leite <cursos@upinside.com.br>
 * @package Source\Support
 */
class Pager extends Paginator
{
    /**
     * Pager constructor.
     *
     * @param string $link
     * @param null|string $title
     * @param array|null $first
     * @param array|null $last
     */
    public function __construct(string $link, ?string $title = null, ?array $first = null, ?array $last = null)
    {
        parent::__construct($link, $title, $first, $last);
    }

    public function ilustration()
    {
        $paginator = "<nav class=\"paginator\">";
        $paginator .= "<a class='paginator_item' title=\"Primeira Página\" href=\"#\">Primeira Página</a>";
        $paginator .= "<a class='paginator_item' title=\"Página 1\" href=\"#\">1</a>";
        $paginator .= "<a class='paginator_item' title=\"Página 2\" href=\"#\">2</a>";
        $paginator .= "<a class='paginator_item' title=\"Página 3\" href=\"#\">3</a>";
        $paginator .= "<span class=\"paginator_item paginator_active\">4</span>";
        $paginator .= "<a class='paginator_item' title=\"Página 5\" href=\"#\">5</a>";
        $paginator .= "<a class='paginator_item' title=\"Página 6\" href=\"#\">6</a>";
        $paginator .= "<a class='paginator_item' title=\"Página 7\" href=\"#\">7</a>";
        $paginator .= "<a class='paginator_item' title=\"Ultima Página\" href=\"#\">Ultima Página</a>";
        $paginator .= "</nav>";
        return $paginator;
    }
}