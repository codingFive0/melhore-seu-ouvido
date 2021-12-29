<?php

namespace Source\App\Admin;

use Source\Models\Category;
use Source\Models\Post\Post;
use Source\Models\User;
use Source\Support\Pager;
use Source\Support\Thumb;
use Source\Support\Upload;

class Blog extends Admin
{
    public function __construct()
    {
        parent::__construct(__DIR__ . "/../../../themes/" . CONF_VIEW_ADMIN);
    }

    public function home(?array $data): void
    {
        if (!empty($data['s'])) {
            $s = str_search($data['s']);
            echo json_encode(["redirect" => url("/admin/blog/home/{$s}/1")]);
            return;
        }

        $search = null;
        $posts = (new Post())->find();

        if (!empty($data['search']) && str_search($data['search']) != "all") {
            $search = str_search($data['search']);
            $posts = (new Post())->find("MATCH(title, subtitle) AGAINST(:s)", "s={$search}");
            if (!$posts->count()) {
                $this->message->info("Sua pesquisa não retornou resultados")->flash();
                redirect("/admin/blog/home");
            }
        }

        $all = ($search ?? "all");
        $pager = new Pager(url("/admin/blog/home/{$all}/"));
        $pager->pager($posts->count(), 12, (!empty($data['page']) ? $data['page'] : 1));

        $head = $this->seo->render(
            "Blog | " . CONF_SITE_NAME,
            CONF_SITE_DESC,
            url("/admin"),
            theme("/assets/images/shere.jpg", CONF_VIEW_ADMIN),
            false
        );

        echo $this->view->render("widgets/blog/home", [
            "app" => "blog/home",
            "head" => $head,
            "posts" => $posts->order("post_at DESC")->limit($pager->limit())->offset($pager->offset())->fetch(true),
            "search" => $search,
            "paginator" => $pager->render()
        ]);
    }

    public function post(?array $data): void
    {
        $imageCad = (!empty($imageCad) ? $imageCad : array());
        //MCE Uploads
        if (!empty($data['upload']) && !empty($_FILES['image'])) {
            $files = $_FILES['image'];
            $upload = new Upload();

            $image = $upload->image($files, "post" . time());

            if (!$image) {
                $json['message'] = $upload->message()->mount();
                echo json_encode($json);
                return;
            }

            $json['mce_image'] = '<img src="' . url("/storage/{$image}") . '" style="width: 100%;" alt="{title}" title="{title}">';
            echo json_encode($json);
            return;
        }

        //CREATE
        if (!empty($data['action']) && $data['action'] == "create") {
            $content = $data['content'];
            $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

            $postCreate = new Post();
            $postCreate->author = $data['author'];
            $postCreate->category = $data['category'];
            $postCreate->title = $data['title'];
            $postCreate->uri = str_slug($postCreate->title);
            $postCreate->subtitle = $data['subtitle'];
            $postCreate->content = str_replace("{title}", $postCreate->title, $content);
            $postCreate->video = $data['video'];
            $postCreate->status = $data['status'];
            $postCreate->post_at = date_fmt_back($data['post_at']);

            if (!empty($_FILES['cover'])) {
                $files = $_FILES['cover'];
                $upload = new Upload();
                $image = $upload->image($files, $postCreate->uri . time());

                if (!$image) {
                    $json['message'] = $upload->message()->mount();
                    echo json_encode($json);
                    return;
                }

                $postCreate->cover = $image;
            }

            if (!$postCreate->save()) {
                $json['message'] = $postCreate->message()->mount();
                echo json_encode($json);
                return;
            }

            $this->message->success("Post criado com sucesso...")->flash();
            echo json_encode(["redirect" => url("/admin/blog/post/{$postCreate->id}")]);
            return;
        }

        //UPDATE
        if (!empty($data['action']) && $data['action'] == "update") {
            $content = $data['content'];
            $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

            $postUpdate = (new Post())->findById($data['post_id']);

            if (!$postUpdate) {
                $this->message->error("O post que você tentou editar não consta no sistema.")->flash();
                echo json_encode(["redirect" => url("/admin/blog/post}")]);
                return;
            }

            $postUpdate->author = $data['author'];
            $postUpdate->category = $data['category'];
            $postUpdate->title = $data['title'];
            $postUpdate->uri = str_slug($postUpdate->title);
            $postUpdate->subtitle = $data['subtitle'];
            $postUpdate->content = str_replace("{title}", $postUpdate->title, $content);
            $postUpdate->video = $data['video'];
            $postUpdate->status = $data['status'];
            $postUpdate->post_at = date_fmt_back($data['post_at']);


            if (!empty($_FILES['cover'])) {
                if ($postUpdate->cover && file_exists(__DIR__ . "/../../../" . CONF_UPLOAD_DIR . "/{$postUpdate->cover}")) {
                    unlink(__DIR__ . "/../../../" . CONF_UPLOAD_DIR . "/{$postUpdate->cover}");
                    (new Thumb())->flush($postUpdate->cover);
                }

                $files = $_FILES['cover'];
                $upload = new Upload();
                $image = $upload->image($files, $postCreate->uri . time());

                if (!$image) {
                    $json['message'] = $upload->message()->mount();
                    echo json_encode($json);
                    return;
                }

                $postUpdate->cover = $image;
            }

            if (!$postUpdate->save()) {
                $json['message'] = $postUpdate->message()->mount();
                echo json_encode($json);
                return;
            }

            $this->message->success("Post atualizado com sucesso...")->flash();
            echo json_encode(["reload" => true]);
            return;
        }

        //DELETE
        if (!empty($data['action']) && $data['action'] == "delete") {
            $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

            $postDelete = (new Post())->findById($data['post_id']);

            if (!$postDelete) {
                $this->message->error("O post que você tentou excluir não consta no sistema.")->flash();
                echo json_encode(["redirect" => url("/admin/blog/post}")]);
                return;
            }

            if ($postDelete->cover && file_exists(__DIR__ . "/../../../" . CONF_UPLOAD_DIR . "/{$postDelete->cover}")) {
                unlink(__DIR__ . "/../../../" . CONF_UPLOAD_DIR . "/{$postDelete->cover}");
                (new Thumb())->flush($postDelete->cover);
            }

            $postDelete->destroy();
            $this->message->success("Post excluido com sucesso...")->flash();
            echo json_encode(["reload" => true]);
            return;
        }


        $postUpdate = null;
        if (!empty($data['post_id'])) {
            $postId = filter_var($data['post_id'], FILTER_VALIDATE_INT);
            $postUpdate = (new Post())->findById($postId);
        }

        $head = $this->seo->render(
            (!empty($postUpdate->title) ? "Editar {$postUpdate->title}" : "Novo artigo") . " | " . CONF_SITE_NAME,
            CONF_SITE_DESC,
            url("/admin"),
            theme("/assets/images/shere.jpg", CONF_VIEW_ADMIN),
            false
        );

        echo $this->view->render("widgets/blog/post", [
            "app" => "blog/post",
            "head" => $head,
            "post" => $postUpdate,
            "categories" => (new Category())->find("type = :t", "t=post")->order("title")->fetch(true),
            "authors" => (new User())->find("level >= :lv", "lv=5")->fetch(true)
        ]);
    }

    public function categories(?array $data): void
    {
        $categories = (new Category())->find("type = :t", "t=post");
        $pager = new Pager(url("/admin/blog/categories/"));
        $pager->pager($categories->count(), 5, (!empty($data['page']) ? $data['page'] : 1));

        $head = $this->seo->render(
            "Gestão de categorias | " . CONF_SITE_NAME,
            CONF_SITE_DESC,
            url("/admin"),
            theme("/assets/images/shere.jpg", CONF_VIEW_ADMIN),
            false
        );

        echo $this->view->render("widgets/blog/categories", [
            "app" => "blog/categories",
            "head" => $head,
            "categories" => $categories->limit($pager->limit())->offset($pager->offset())->order("title")->fetch(true),
            "paginator" => $pager->render()
        ]);
    }

    public function category(?array $data): void
    {
        $data = (!empty($data) ? filter_var_array($data) : $data);

        //CREATE
        if(!empty($data["action"]) && $data["action"] == "create"){
            $catCreate = new Category();
            $catCreate->title = $data["title"];
            $catCreate->uri = str_slug($catCreate->title);
            $catCreate->description = $data["description"];
            $catCreate->type = "post";

            if (!empty($_FILES['cover'])) {
                $files = $_FILES['cover'];
                $upload = new Upload();
                $image = $upload->image($files, $catCreate->uri . time());

                if (!$image) {
                    $json['message'] = $upload->message()->mount();
                    echo json_encode($json);
                    return;
                }

                $catCreate->cover = $image;
            }


            if (!$catCreate->save()) {
                $json['message'] = $catCreate->message()->mount();
                echo json_encode($json);
                return;
            }

            $this->message->success("Categoria criada com sucesso...")->flash();
            echo json_encode(["redirect" => url("/admin/blog/category/{$catCreate->id}")]);
            return;
        }

        //UPDATE
        if(!empty($data["action"]) && $data["action"] == "update"){
            $catCreate = (new Category())->findById($data['category_id']);

            if (!$catCreate) {
                $this->message->error("A categoria que você tentou editar não consta no sistema.")->flash();
                echo json_encode(["redirect" => url("/admin/blog/category")]);
                return;
            }


            $catCreate->title = $data["title"];
            $catCreate->uri = str_slug($catCreate->title);
            $catCreate->description = $data["description"];

            if (!empty($_FILES['cover'])) {
                if ($catCreate->cover && file_exists(__DIR__ . "/../../../" . CONF_UPLOAD_DIR . "/{$catCreate->cover}")) {
                    unlink(__DIR__ . "/../../../" . CONF_UPLOAD_DIR . "/{$catCreate->cover}");
                    (new Thumb())->flush($catCreate->cover);
                }

                $files = $_FILES['cover'];
                $upload = new Upload();
                $image = $upload->image($files, $catCreate->uri . time());

                if (!$image) {
                    $json['message'] = $upload->message()->mount();
                    echo json_encode($json);
                    return;
                }

                $catCreate->cover = $image;
            }


            if (!$catCreate->save()) {
                $json['message'] = $catCreate->message()->mount();
                echo json_encode($json);
                return;
            }

            $this->message->success("Categoria atualizada com sucesso...")->flash();
            echo json_encode(["reload" => true]);
            return;
        }

        //DELETE
        if (!empty($data['action']) && $data['action'] == "delete") {
            $catDelete = (new Category())->findById($data['category_id']);

            if (!$catDelete) {
                $this->message->error("A categoria que você tentou excluir não consta no sistema.")->flash();
                echo json_encode(["redirect" => url("/admin/blog/categories")]);
                return;
            }

            if ($catDelete->posts()->count()) {
                $this->message->warning("A artigos cadastrados na categoria que você tentou excluir.")->flash();
                echo json_encode(["reload" => true]);
                return;
            }

            if ($catDelete->cover && file_exists(__DIR__ . "/../../../" . CONF_UPLOAD_DIR . "/{$catDelete->cover}")) {
                unlink(__DIR__ . "/../../../" . CONF_UPLOAD_DIR . "/{$catDelete->cover}");
                (new Thumb())->flush($catDelete->cover);
            }

            $catDelete->destroy();
            $this->message->success("A categoria excluido com sucesso...")->flash();
            echo json_encode(["reload" => true]);
            return;
        }

        $category = null;
        if(!empty($data['category_id'])){
            $category = (new Category())->find("id = :c", "c={$data['category_id']}")->fetch();
        }

        $head = $this->seo->render(
            (!empty($categories->name) ? "Editar {$categories->name}" : "Nova categoria"). " | " . CONF_SITE_NAME,
            CONF_SITE_DESC,
            url("/admin"),
            theme("/assets/images/shere.jpg", CONF_VIEW_ADMIN),
            false
        );

        echo $this->view->render("widgets/blog/category", [
            "app" => "blog/categories",
            "head" => $head,
            "category" => $category
        ]);
    }
}