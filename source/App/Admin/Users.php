<?php

namespace Source\App\Admin;

use Source\Models\User;
use Source\Support\Pager;
use Source\Support\Thumb;
use Source\Support\Upload;

class Users extends Admin
{
    public function __construct()
    {
        parent::__construct(__DIR__ . "/../../../themes/" . CONF_VIEW_ADMIN);
    }

    public function home(?array $data): void
    {
        if (!empty($data['s'])) {
            $s = str_search($data['s']);
            echo json_encode(["redirect" => url("/admin/users/home/{$s}/1")]);
            return;
        }

        $search = null;
        $users = (new User())->find();
        if (!empty($data['search']) && str_search($data['search']) != "all") {
            $search = str_search($data['search']);
            $users = (new User())->find("MATCH(first_name, last_name, email) AGAINST(:s)", "s={$search}");
            if (!$users->count()) {
                $this->message->info("Sua pesquisa não retornou resultados")->flash();
                redirect("/admin/users/home");
            }
        }

        $all = (!empty($search) ? $search : "all");

        $pager = new Pager(url("/admin/users/home/{$all}/"));
        $pager->pager($users->count(), 12, ($data['page'] ?? 1));

        $head = $this->seo->render(
            "Usuários | " . CONF_SITE_NAME,
            CONF_SITE_DESC,
            url("/admin"),
            theme("/assets/images/shere.jpg", CONF_VIEW_ADMIN),
            false
        );

        echo $this->view->render("widgets/users/home", [
            "app" => "users/home",
            "head" => $head,
            "search" => "",
            "users" => $users->order("created_at DESC")->limit($pager->limit())->offset($pager->offset())->fetch(true),
            "paginator" => $pager->render()
        ]);
    }

    public function user(?array $data): void
    {
        //CREATE
        if (!empty($data['action']) && $data['action'] == "create") {
            $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

            $userCreate = new User();
            $userCreate->first_name = $data['first_name'];
            $userCreate->last_name = $data['last_name'];
            $userCreate->email = $data['email'];
            $userCreate->password = $data['password'];
            $userCreate->level = $data['level'];
            $userCreate->genre = $data['genre'];
            $userCreate->datebirth = date_fmt_back($data['datebirth']);
            $userCreate->document = preg_replace("/[^0-9]/", "", $data['document']);
            $userCreate->status = $data['status'];

            if (!empty($_FILES['photo'])) {
                $files = $_FILES['photo'];
                $upload = new Upload();
                $image = $upload->image($files, $userCreate->first_name . "-" . $userCreate->last_name . time());

                if (!$image) {
                    $json['message'] = $upload->message()->mount();
                    echo json_encode($json);
                    return;
                }

                $userCreate->photo = $image;
            }
        }

        //UPDATE
        if (!empty($data['action']) && $data['action'] == "update") {
            $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

            $userUpdate = (new User())->findById($data['user_id']);

            if (!$userUpdate) {
                $this->message->error("O usuário que você tentou editar não consta no sistema.")->flash();
                echo json_encode(["redirect" => url("/admin/users/home")]);
                return;
            }

            $userUpdate->first_name = $data['first_name'];
            $userUpdate->last_name = $data['last_name'];
            $userUpdate->email = $data['email'];
            $userUpdate->password = (!empty($data['password']) ? $data['password'] : $userUpdate->password);
            $userUpdate->level = $data['level'];
            $userUpdate->genre = $data['genre'];
            $userUpdate->datebirth = date_fmt_back($data['datebirth']);
            $userUpdate->document = preg_replace("/[^0-9]/", "", $data['document']);
            $userUpdate->status = $data['status'];

            if (!empty($_FILES['photo'])) {

                if ($userUpdate->cover && file_exists(__DIR__ . "/../../../" . CONF_UPLOAD_DIR . "/{$userUpdate->cover}")) {
                    unlink(__DIR__ . "/../../../" . CONF_UPLOAD_DIR . "/{$userUpdate->cover}");
                    (new Thumb())->flush($userUpdate->cover);
                }

                $files = $_FILES['photo'];
                $upload = new Upload();
                $image = $upload->image($files, $userUpdate->first_name . "-" . $userUpdate->last_name . time());

                if (!$image) {
                    $json['message'] = $upload->message()->mount();
                    echo json_encode($json);
                    return;
                }

                $userUpdate->photo = $image;
            }

            if (!$userUpdate->save()) {
                $json['message'] = $userUpdate->message()->mount();
                echo json_encode($json);
                return;
            }

            $this->message->success("Usuário atualizado com sucesso")->flash();
            echo json_encode(["reload" => true]);
            return;
        }

        //DELETE
        if (!empty($data['action']) && $data['action'] == "delete") {
            $userDelete = (new User())->findById($data['user_id']);

            if (!$userDelete) {
                $this->message->error("O usuário que você tentou excluir não consta no sistema.")->flash();
                echo json_encode(["redirect" => url("/admin/users/home")]);
                return;
            }

            if ($userDelete->cover && file_exists(__DIR__ . "/../../../" . CONF_UPLOAD_DIR . "/{$userDelete->cover}")) {
                unlink(__DIR__ . "/../../../" . CONF_UPLOAD_DIR . "/{$userDelete->cover}");
                (new Thumb())->flush($userDelete->cover);
            }

            $userDelete->destroy();
            $this->message->success("o usuário foi excluido com sucesso...")->flash();
            echo json_encode(["redirect" => url("/admin/users/home")]);
            return;
        }


        $user = null;
        if (!empty($data['user_id'])) {
            $userId = filter_var($data['user_id'], FILTER_VALIDATE_INT);
            $user = (new User())->findById($userId);
        }

        $head = $this->seo->render(
            "Usuários | " . CONF_SITE_NAME,
            CONF_SITE_DESC,
            url("/admin"),
            theme("/assets/images/shere.jpg", CONF_VIEW_ADMIN),
            false
        );

        echo $this->view->render("widgets/users/user", [
            "app" => "users/home",
            "head" => $head,
            "user" => $user
        ]);
    }
}