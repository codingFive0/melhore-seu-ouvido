<?php

namespace Source\Support;

use Source\Core\Routes;
use Source\Core\View;

class Assets
{
    /** @var Routes */
    protected $router;

    /** @var View */
    protected $view;

    /** @var Assets */
    private $assets;

    public function __construct(Routes $router, View $view)
    {
        $this->router = $router;
        $this->view = $view;

        $this->assetsLoad();
        $this->addAssets();
    }

    private function assetsLoad()
    {
        require dirname(__DIR__) . '/Boot/Assets.php';
        $this->assets = $asset;
    }

    public function addAssets()
    {
        $load = ($this->assets[$this->router->getRouteName()] ?? null);

        if (!empty($load)) {
            $this->addCss($load['css']);
            $this->addJs($load['js']);
        }
    }

    private function addJs(array $js)
    {
        $this->view->addData(["js" => $js]);
    }

    private function addCss(array $css)
    {
        $this->view->addData(["css" => $css]);
    }
}