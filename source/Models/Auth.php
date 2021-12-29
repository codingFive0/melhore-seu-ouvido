<?php

namespace Source\Models;

use Source\Core\Model;
use Source\Core\Session;
use Source\Core\View;
use Source\Support\Email;
use Source\Support\EmailWithSwift;

/**
 * Class Auth
 * @package Source\Models
 */
class Auth extends Model
{
    /**
     * Auth constructor.
     */
    public function __construct()
    {
        parent::__construct("users", ["id"], ["email", "password"]);
    }

    /**
     * @return null|User
     */
    public static function User(): ?User
    {
        $session = new Session();
        if (!$session->has("authUser")) {
            return null;
        }

        return (new User())->findById($session->authUser);
    }

    /**
     * log-out
     */
    public static function logout(): void
    {
        $session = new Session();
        $session->unset("authUser");
    }

    /**
     * <b>Restro de Usuários:</b> Salva o usuário no banco de dados e envia o e-mail de confirmação ao mesmo.
     *
     * @param User $user
     * @return bool
     */
    public function register(User $user): bool
    {
        if (!$user->save()) {
            $this->message = $user->message();
            return false;
        }

        $message = (new View(CONF_VIEW_EMAIL_PATH))->render("confirm", [
            "first_name" => $user->first_name,
            "confirm_link" => url("/cadastro/confirmado/" . base64_encode($user->email))
        ]);

        $email = (new Email())->bootstrap(
            CONF_SITE_NAME . " - Confirme da sua conta",
            $message,
            $user->email,
            "{$user->first_name} {$user->last_name}"
        );

        $emailResult = $email->send();
        if(!$emailResult){
            $this->message = $email->message();
            return false;
        }

        return true;
    }

    /**
     * <b>Verificações:</b> Verifica as informações do usuário e retorna o mesmo em active record
     *
     * @param string $email
     * @param string $password
     * @param bool $save
     * @param int $level
     * @return User|null
     */
    public function attempt(string $email, string $password, int $level = 1): ?User
    {
        if (!is_email($email)) {
            $this->message->warning("O e-mail informado não é válido");
            return null;
        }

        if (!is_passwd($password)) {
            $this->message->warning("A senha informada não é válida");
            return null;
        }

        $user = (new User())->findByEmail($email);

        if (!$user) {
            $this->message->error("O e-mail informado não está cadastrado");
            return null;
        }

        if (!passwd_verify($password, $user->password)) {
            $this->message->error("A senha informada não confere");
            return null;
        }


        if ($user->level < $level) {
            $this->message->error("Desculpe, mas você não tem permissão para logar-se aqui");
            return null;
        }

        if (passwd_rehash($user->password)) {
            $user->password = $password;
            $user->save();
        }

        return $user;
    }

    /**
     * <b>Realiza o login:</b> realiza as verificações e cria a sessão do usuário.
     *
     * @param string $email
     * @param string $password
     * @param bool $save
     * @param int $level
     * @return bool
     */
    public function login(string $email, string $password, bool $save = false, int $level = 1): bool
    {
        $user = $this->attempt($email, $password, $level);
        if (empty($user)) {
            return false;
        }

        if ($save) {
            setcookie("authEmail", $email, time() + 604800, "/");
        } else {
            setcookie("authEmail", null, time() - 3600, "/");
        }

        //LOGIN
        (new Session())->set("authUser", $user->id);
        $this->message->success("Olá {$user->first_name}. Você está logado na administração :)")->flash();
        return true;
    }

    /**
     * @param string $email
     * @return bool
     */
    public function forget(string $email): bool
    {
        $user = (new User())->findByEmail($email);

        if (!$user) {
            $this->message->warning("O e-mail informado não está cadastrado no sistema, favor confira os dados");
            return false;
        }

        $user->forget = md5(uniqid(rand(), true));
        $user->save();

        $view = new View(__DIR__ . "/../../shared/views/email");
        $message = $view->render("forget", [
            "first_name" => $user->first_name,
            "forget_link" => url("/recuperar/{$user->email}|{$user->forget}")
        ]);

        (new EmailWithSwift())->bootstrap(
            "Recuperação de senha no " . CONF_SITE_NAME,
            $message,
            $user->email,
            "{$user->first_name} {$user->last_name}"
        )->send();

        return true;
    }

    /**
     * @param string $email
     * @param string $code
     * @param string $password
     * @param string $passwordRe
     * @return bool
     */
    public function reset(string $email, string $code, string $password, string $passwordRe): bool
    {
        $user = (new User())->findByEmail($email);

        if (!$user) {
            $this->message->warning("O e-mail informado não está vinculado a uma conta e não foi encontrado no sistema");
            return false;
        }

        if ($user->forget != $code) {
            $this->message->error("Desculpe, o codigo de verificação não é válido");
            return false;
        }

        if (!is_passwd($password)) {
            $min = CONF_PASSWD_MIN_LEN;
            $max = CONF_PASSWD_MAX_LEN;
            $this->message->info("Sua nova senha deve conter entre {$min} e {$max} caracteres");
            return false;
        }

        if ($password != $passwordRe) {
            $this->message->warning("As senhas informadas são diferentes, favor confiri-las");
            return false;
        }

        $user->password = $password;
        $user->forget = null;
        $user->save();
        return true;
    }
}