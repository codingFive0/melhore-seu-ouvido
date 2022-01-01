<?php

namespace Source\App;

use Source\Core\Controller;

/**
 * Class Web
 * @package Source\App
 */
class Errors extends Controller
{
    /**
     * @var
     */
    private $location;

    /**
     * Web constructor.
     */
    public function __construct($route)
    {
        parent::__construct($route, __DIR__ . "/../../shared/views/errors/");
    }

    /**
     * <b>Erros 404<b>
     *
     * SITE ERROS
     */
    public function erro($data): void
    {

        echo $this->view->render($data["code"], [
        ]);
    }

}