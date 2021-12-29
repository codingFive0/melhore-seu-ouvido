<?php

namespace Source\App\Admin;

use Source\Models\Manoojob\Employer;
use Source\Models\Manoojob\File;
use Source\Models\Manoojob\Vacancy;
use Source\Models\Manoojob\Category;
use Source\Models\User;

class Control extends Admin
{
    public function __construct()
    {
        parent::__construct(__DIR__ . "/../../../themes/" . CONF_VIEW_ADMIN);
    }

    public function home()
    {
        $head = $this->seo->render(
            "Gestão | " . CONF_SITE_NAME,
            CONF_SITE_DESC,
            url("/admin"),
            theme("/assets/images/shere.jpg", CONF_VIEW_ADMIN),
            false
        );

        echo $this->view->render("widgets/control/home", [
            "app" => "control/home",
            "head" => $head,
            "vacsOn" => (new Vacancy())->find()->count(),
            "employer" => (new Employer())->find()->count(),
            "vacsAll" => (new Vacancy())->find("MONTH(created_at) = MONTH(now())")->count(),
            "employers" => (new Employer)->find()->fetch(true)
        ]);
    }

    public function company(?array $data)
    {
        //create
        if (!empty($data["action"]) && $data["action"] == "create") {
            if((new Employer())->findByEmail($data["email"])){
                echo $this->ajaxResponse(
                    "message",
                    $this->message->warning("Ja existe uma empresa com este e-mail no sistema.")->mount()
                );
                return;
            }

            $cadCompany = new Employer();
            $cadCompany->user_id = $data["user_id"];
            $cadCompany->document = str_replace([".", "/", "-", " "], ["", "", "", ""], $data["document"]);
            $cadCompany->category_id = $data["category_id"];
            $cadCompany->company_name = $data["company_name"];
            $cadCompany->fantasy_name = $data["fantasy_name"];
            $cadCompany->uri = str_slug($data["fantasy_name"]);
            $cadCompany->email = $data["email"];
            $cadCompany->phone = $data["phone"];
            $cadCompany->status = $data["status"];

            if(!$cadCompany->save()){
                echo $this->ajaxResponse(
                    "message",
                    $cadCompany->message()->mount()
                );
                return;
            }

            $this->message->success("Empresa criada com sucesso.")->flash();
            echo $this->ajaxResponse(
                "redirect",
                url("/admin/control/company/{$cadCompany->id}")
            );
            return;
        }

        //update
        if (!empty($data["action"]) && $data["action"] == "update") {

            $updateCompany = (new Employer())->findById($data["id"]);
            $updateCompany->user_id = $data["user_id"];
            $updateCompany->document = str_replace([".", "/", "-", " "], ["", "", "", ""], $data["document"]);
            $updateCompany->category_id = $data["category_id"];
            $updateCompany->company_name = $data["company_name"];
            $updateCompany->fantasy_name = $data["fantasy_name"];
            $updateCompany->uri = str_slug($data["fantasy_name"]);
            $updateCompany->email = $data["email"];
            $updateCompany->phone = $data["phone"];
            $updateCompany->status = $data["status"];

            if(!$updateCompany->save()){
                echo $this->ajaxResponse(
                    "message",
                    $updateCompany->message()->mount()
                );
                return;
            }

            $this->message->success("Empresa atualizada com sucesso.")->flash();
            echo $this->ajaxResponse(
                "reload",
                true
            );
            return;
        }

        if(!empty($data["action"]) && $data["action"] == "update_sts"){
            $updateStatus = (new Employer())->findById($data["id"]);
            $updateStatus->status = "confirmed";
            if(!$updateStatus->save()){
                echo $this->ajaxResponse(
                    "message",
                    $updateStatus->message()->mount()
                );
                return;
            }
            $this->message->success("Empresa atualizada! Esta empresa agora, está ativa na plataforma.")->flash();
            echo $this->ajaxResponse(
                "reload",
                true
            );
            return;
        }

        $head = $this->seo->render(
            "Gestão de empresas| " . CONF_SITE_NAME,
            CONF_SITE_DESC,
            url("/admin"),
            theme("/assets/images/shere.jpg", CONF_VIEW_ADMIN),
            false
        );

        $edit = null;
        if($data["id"]){
            $edit = (new Employer())->findById($data["id"]);
        }

        echo $this->view->render("widgets/control/company", [
            "app" => "control/home",
            "head" => $head,
            "cats" => (new Category())->find()->fetch(true),
            "users" => (new User())->find()->fetch(true),
            "company" => $edit
        ]);
    }

    public function files()
    {
        $head = $this->seo->render(
            "Gestão de arquivos | " . CONF_SITE_NAME,
            CONF_SITE_DESC,
            url("/admin"),
            theme("/assets/images/shere.jpg", CONF_VIEW_ADMIN),
            false
        );

        echo $this->view->render("widgets/control/files", [
            "app" => "control/files",
            "head" => $head,
            "files" => (new File())->find()->fetch(true)
        ]);
    }

    public function file(?array $data)
    {
        if (!empty($data["action"])) {

            if($data["action"] === "update_sts"){
                $file = (new File())->findById($data["id"]);
                $file->status = "approved";

                if (!$file->save()) {
                    echo $this->ajaxResponse(
                        "message",
                        $file->message()->mount()
                    );
                    return;
                }
                $this->message->success("O arquivo foi atualizado com sucesso.")->flash();
                echo $this->ajaxResponse(
                    "reload",
                    true
                );
            }

            // UPDATE
            if ($data["action"] === "update") {
                if (in_array("", $data)) {
                    echo $this->ajaxResponse(
                        "message",
                        $this->message->info("Informe todos os dados do formulário para completar sua solicitação")->mount()
                    );
                    return;
                }

                if (!filter_var($data["url"], FILTER_VALIDATE_URL) || pathinfo($data["url"])["extension"] !== "xml") {
                    echo $this->ajaxResponse(
                        "message",
                        $this->message->warning("O seu arquivo deve ser um xml válido")->mount()
                    );
                    return;
                }

                if (empty($data["id"]) || !filter_var($data["id"], FILTER_VALIDATE_INT)) {
                    echo $this->ajaxResponse(
                        "message",
                        $this->message->error("Não foi possível concluir sua solicitação. Atualize a página ou tente mais tarde.")->mount()
                    );
                    return;
                }

                $file = (new File())->findById($data["id"]);
                $file->url = $data["url"];
                $file->name = $data["name"];
                $file->period = $data["period"];
                $file->status = $data["status"];

                if (!$file->save()) {
                    echo $this->ajaxResponse(
                        "message",
                        $file->message()->mount()
                    );
                    return;
                }
                $this->message->success("O arquivo foi atualizado com sucesso.")->flash();
                echo $this->ajaxResponse(
                    "reload",
                    true
                );
            }
            return;
        }

        $head = $this->seo->render(
            "Gestão de arquivos | " . CONF_SITE_NAME,
            CONF_SITE_DESC,
            url("/admin"),
            theme("/assets/images/shere.jpg", CONF_VIEW_ADMIN),
            false
        );

        echo $this->view->render("widgets/control/file", [
            "app" => "control/files",
            "head" => $head,
            "file" => (new File())->findById($data["id"])
        ]);
    }
}