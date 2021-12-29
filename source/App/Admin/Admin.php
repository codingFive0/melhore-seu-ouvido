<?php

namespace Source\App\Admin;

use Source\Core\Controller;
use Source\Models\Auth;

/**
 * Class Admin
 * @package Source\App\Admin
 */
class Admin extends Controller
{
    /** @var \Source\Models\User|null */
    protected $user;

    /**
     * Admin constructor.
     * @param string|null $pathToViews
     */
    public function __construct($router, string $pathToViews = null)
    {
        parent::__construct($router, __DIR__ . "/../../../themes/" . CONF_VIEW_ADMIN);

        $this->user = (!empty(Auth::User()) ? Auth::User() : null);

        if ($this->user->level < 5) {
            $this->message->error("Para acessar está área e necessário o login e a permissão de administrador")->flash();
            redirect("/admin/login");
        }
    }
}