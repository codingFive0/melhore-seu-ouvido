<?php

namespace Source\App;

use CoffeeCode\Paginator\Paginator;
use Source\Core\Connect;
use Source\Core\Controller;
use Source\Core\Session;
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
//        Connect::getInstance();
//        (new Access())->report();
//        (new Online())->report();
    }

    /**
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

    public function questions(array $post)
    {
        $notes["-1"] = [
            "A",
            ["A#", "Bb"],
            "B",
            ["B#", "C"],
            ["C#", "Db"],
            "D",
            ["D#", "Eb"],
            "E",
            ["E#", "F"],
            ["F#", "Gb"],
            "G",
            ["G#", "Ab"]
        ];
        $notes["0"] = [
            "A",
            ["A#", "Bb"],
            "B",
            ["B#", "C"],
            ["C#", "Db"],
            "D",
            ["D#", "Eb"],
            "E",
            ["E#", "F"],
            ["F#", "Gb"],
            "G",
            ["G#", "Ab"]
        ];
        $notes["1"] = [
            "A",
            ["A#", "Bb"],
            "B",
            ["B#", "C"],
            ["C#", "Db"],
            "D",
            ["D#", "Eb"],
            "E",
            ["E#", "F"],
            ["F#", "Gb"],
            "G",
            ["G#", "Ab"]
        ];

        $intruments = [
            "Keyboard",
            "actGuitar"
        ];
        // DEFINE ALEATORIAMENTE O INTRUMENTO A SER TOCADO
        $instrument = $intruments[0];

        // DEFINE ALEATORIAMENTE A OITAVA A SER TOCADA
        $randOctave = random_int(-1, 1);

        // DEFINE ALEATORIAMENTE AS NOTAS QUE SERÃO ESCOLHIDAS
        $definedNotes = [];

        for ($i = 1; $i < 5; $i++) {
            $rand = mt_rand(0, count($notes[$randOctave]) - 1);
            $definedNotes[] = $notes[$randOctave][$rand];
            unset($notes[$randOctave][$rand]);
            $notes[$randOctave] = array_values($notes[$randOctave]);
        }

        //Escolhe alternativas entre nomenclatura de notas (# ou b)
        $cleanDefinedNotes = [];
        foreach ($definedNotes as $value) {
            if (is_array($value)) {
                $randI = mt_rand(0, 1);
                $cleanDefinedNotes[] = $value[$randI];
            } else {
                $cleanDefinedNotes[] = $value;
            }
        }

        $reproducedNote = $cleanDefinedNotes[mt_rand(0, 3)];

        $namedFilesBase = [
            "B#" => "B#_C",
            "C" => "B#_C",
            "E#" => "E#_F",
            "F" => "E#_F",
            "A#" => "A#_Bb",
            "Bb" => "A#_Bb",
            "C#" => "C#_Db",
            "Db" => "C#_Db",
            "D#" => "D#_Eb",
            "Eb" => "D#_Eb",
            "F#" => "F#_Gb",
            "Gb" => "F#_Gb",
            "G#" => "G#_Ab",
            "Ab" => "G#_Ab"
        ];

        $jsonResponse = [
            "midiaFile" => ($namedFilesBase[$reproducedNote] ?? $reproducedNote) . '-' . $instrument . "({$randOctave}).wav",
            "responseOptions" => $cleanDefinedNotes
        ];
        echo json_encode($jsonResponse);
    }

    public function response(array $post)
    {
        $post = filter_var_array($post, FILTER_DEFAULT);

        $file = $post['soundName'];
        $correntResponses = explode("_", substr($file, 0, mb_strpos($file, '-')));
        $response = $post['response'];
        sleep(2);

        echo json_encode(["responseStatus" => in_array($response, $correntResponses)]);
    }
}