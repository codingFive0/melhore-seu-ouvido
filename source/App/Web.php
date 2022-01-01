<?php

namespace Source\App;

use CoffeeCode\Paginator\Paginator;
use Source\Core\Connect;
use Source\Core\Controller;
use Source\Core\Session;
use Source\Models\App\File;
use Source\Models\App\Note;
use Source\Models\Auth;
use Source\Models\Manoojob\Category;
use Source\Models\Manoojob\Employer;
use Source\Models\Manoojob\Vacancy;
use Source\Models\Report\Access;
use Source\Models\Report\Online;
use Source\Models\Route;
use Source\Models\System\Cities;
use Source\Models\System\State;
use Source\Models\User;

/**
 * Class Web
 * @package Source\App
 */
class Web extends Controller
{
    /**
     * @var
     */
    private $location;

    /**
     * Web constructor.
     */
    public function __construct($route)
    {
        parent::__construct($route, __DIR__ . "/../../themes/" . CONF_VIEW_THEME . "/");

        Connect::getInstance();
        (new Access())->report();
        (new Online())->report();
    }

    /**
     * <i>GET</i>
     * <b>Renderiza e controla a página incial<b>
     *
     * SITE HOME
     */
    public function home(): void
    {
        $head = $this->seo->render(
            CONF_SITE_NAME . " - " . CONF_SITE_TITLE,
            CONF_SITE_DESC,
            url(),
            url("shared/images/share.png")
        );

        echo $this->view->render("home", [
            "head" => $head
        ]);
    }

    /**
     * <i>POST (ajax)</i>
     * <b>Organiza dados de treino<b>
     *
     * AJAX QUESTIONS
     */
    public function questions(array $post)
    {

        $octave = mt_rand(-1, 1);
        $notas = (new Note())->find("octave = :o", "o={$octave}")->order("RAND()")->limit(4)->fetch(true);

        $reproducedNote = $notas[mt_rand(0, 3)];

        $selectedNotes = [];
        foreach ($notas as $nota){
            $selectedNotes[] = $nota->name;
        }

        $jsonResponse = [
            "midiaFile" => $reproducedNote->file()->fullName(),
            "responseOptions" => $selectedNotes
        ];
        echo json_encode($jsonResponse);
    }

    /**
     * <i>POST (ajax)</i>
     * <b>Valida Resposta da questão<b>
     *
     * AJAX RESPONSE
     */
    public function response(array $post)
    {
        $post = filter_var_array($post, FILTER_DEFAULT);

        $file = $post['soundName'];
        $correntResponses = explode("_", substr($file, 0, mb_strpos($file, '-')));
        $response = $post['response'];
        sleep(2);

        echo json_encode(["responseStatus" => in_array($response, $correntResponses)]);
    }

    /**
     * <i>GET</i>
     * <b>Renderiza página de desafio<b>
     *
     * SITE CHALLENGE
     */
    public function challenge()
    {
        $octave = mt_rand(-1, 1);
        $nota = (new Note())->find("octave = :o", "o={$octave}")->order("RAND()")->limit(4)->fetch(true);
    }
}