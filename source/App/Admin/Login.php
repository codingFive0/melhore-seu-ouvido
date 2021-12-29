<?php

namespace Source\App\Admin;

use Source\Core\Controller;
use Source\Core\Session;
use Source\Models\Auth;

/**
 * Class Login
 * @package Source\App\Admin
 */
class Login extends Controller
{
    /**
     * Login constructor.
     */
    public function __construct($router, string $pathToViews = null)
    {
        parent::__construct($router, __DIR__ . "/../../../themes/" . CONF_VIEW_ADMIN);
    }

    /**
     *
     */
    public function root(): void
    {
        $user = Auth::User();
        if ($user && $user->level > 5) {
            redirect("/admin/dash");
        } else {
            redirect("/admin/login");
        }
    }

    /**
     * @param array|null $data
     */
    public function login(?array $data): void
    {
        $user = Auth::User();
        if ($user && $user->level > 5) {
            redirect("/admin/dash");
        }

        if (!empty($data['email']) && !empty($data['password'])) {
            if (request_limit("loginlogin", 3, 10 * 60)) {
                $json['message'] = $this->message->warning("ACESSO NEGADO: Agurde 10 minutos para tentar logar novamente.")->mount();
                echo json_encode($json);
                return;
            }

            $auth = new Auth();
            $login = $auth->login($data['email'], $data['password'], true, 5);

            if ($login) {
                $json['redirect'] = url("/admin/dash");
            } else {
                $json['message'] = $auth->message()->mount();
            }

            echo json_encode($json);
            return;
        }

        $head = $this->seo->render(
            "Administração | " . CONF_SITE_NAME,
            CONF_SITE_DESC,
            url("/admin"),
            theme("/assets/images/shere.jpg", CONF_VIEW_ADMIN),
            false
        );

        echo $this->view->render("widgets/login/login", [
            "head" => $head
        ]);
    }
}