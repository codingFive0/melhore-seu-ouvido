<?php

namespace Source\Core;

use CoffeeCode\Router\Router;
use Source\Support\Assets;
use Source\Support\Message;
use Source\Support\Seo;

/**
 * Class Controller
 * @package Source\Core
 */
class Controller
{

    /** @var View */
    protected $view;

    /** @var Seo */
    protected $seo;

    /** @var Message */
    protected $message;

    /** @var Router */
    protected $route;

    /** @var Assets */
    protected $assets;

    /**
     * Controller constructor.
     * @param string|null $pathToViews
     */
    public function __construct($router, string $pathToViews = null)
    {
        $this->route = $router;
        $this->view = new View($pathToViews);
        $this->view->addData(["route" => $this->route]);
        $this->seo = new Seo();
        $this->message = new Message();
        $this->assets = new Assets($this->route, $this->view);
    }

    public function ajaxResponse(string $param, $content)
    {
        return json_encode([$param => $content]);
    }
}