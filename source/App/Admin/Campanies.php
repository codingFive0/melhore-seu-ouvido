<?php

namespace Source\App\Admin;

use Source\Models\Manoojob\Employer;

class Campanies extends Admin
{
    public function __construct()
    {
        parent::__construct(__DIR__ . "/../../../themes/" . CONF_VIEW_ADMIN);
    }

    public function home()
    {
        $head = $this->seo->render(
            "Empresas | " . CONF_SITE_NAME,
            CONF_SITE_DESC,
            url("/admin"),
            theme("/assets/images/shere.jpg", CONF_VIEW_ADMIN),
            false
        );

        echo $this->view->render("widgets/campanies/home", [
            "app" => "campany/home",
            "head" => $head,
            "companies" => (new Employer())->find()->fetch(true)
        ]);
    }
}