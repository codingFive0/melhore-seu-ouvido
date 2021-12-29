<?php

namespace Source\App\Admin;

class System extends Admin
{
    public function __construct()
    {
        parent::__construct(__DIR__ . "/../../../themes/" . CONF_VIEW_ADMIN);
    }

    public function home(?array $data)
    {
        $head = $this->seo->render(
            "Gestão | " . CONF_SITE_NAME,
            CONF_SITE_DESC,
            url("/admin"),
            theme("/assets/images/shere.jpg", CONF_VIEW_ADMIN),
            false
        );

        echo $this->view->render("widgets/system/home", [
            "app" => "system/home",
            "head" => $head,
            "edit" => (new \Source\Models\System\System())->findById(1)
        ]);
    }

    public function edit(?array $data)
    {
        if (!empty($data["action"]) && $data["action"] == "update") {
            $system = new \Source\Models\System\System();
            $system->id = $data["id"];
            $system->site_name = $data["site_name"];
            $system->site_title = $data["site_title"];
            $system->site_desc = $data["site_desc"];
            $system->site_domain = $data["site_domain"];
            $system->site_inative_time = $data["site_inative_time"];
            $system->fb_page = $data["fb_page"];
            $system->ig_page = $data["ig_page"];
            $system->tt_page = $data["tt_page"];
            $system->li_page = $data["li_page"];
            $system->front_theme = $data["front_theme"];
            $system->app_theme = $data["app_theme"];
            $system->upload_folder = $data["upload_folder"];
            $system->images_folder = $data["images_folder"];
            $system->files_folder = $data["files_folder"];
            $system->medias_folder = $data["medias_folder"];
            $system->sender_email = $data["sender_email"];
            $system->sender_name = $data["sender_name"];
            if (!$system->save()) {
                echo $this->ajaxResponse(
                    "message",
                    $system->message()->mount()
                );
                return;
            }

            $this->message->success("Dados globais alterados com sucesso! Logo o sistema será atualizado.")->flash();
            echo $this->ajaxResponse(
                "reload",
                true
            );
            return;
        }

        $head = $this->seo->render(
            "Gestão | " . CONF_SITE_NAME,
            CONF_SITE_DESC,
            url("/admin"),
            theme("/assets/images/shere.jpg", CONF_VIEW_ADMIN),
            false
        );

        echo $this->view->render("widgets/system/edit", [
            "app" => "system/edit",
            "head" => $head,
            "edit" => (new \Source\Models\System\System())->findById(1)
        ]);
    }
}