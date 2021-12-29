<?php

namespace Source\App\Admin;

use Source\Models\Auth;
use Source\Models\Manoojob\File;
use Source\Models\Manoojob\Vacancy;
use Source\Models\Report\Access;
use Source\Models\Report\Online;
use Source\Models\User;
use Source\Models\Manoojob\Employer;

class Dash extends Admin
{
    public function __construct()
    {
        parent::__construct(__DIR__ . "/../../../themes/" . CONF_VIEW_ADMIN);
    }

    public function dash(): void
    {
        redirect("/admin/dash/home");
    }

    public function home(?array $data): void
    {

        //realtime refresh
        if (!empty($data['refresh'])) {
            $list = null;
            $itens = (new Online())->findByActive();
            if ($itens) {
                foreach ($itens as $item) {
                    $list[] = [
                        "dates" => date_fmt($item->created_at, "h\hi") . " - " . date_fmt($item->updated_at, "h\hi"),
                        "user" => ($item->user()->fullName()),
                        "pages" => $item->pages,
                        "url" => $item->url
                    ];
                }
            }

            echo json_encode([
                "count" => (new Online())->findByActive(true),
                "list" => $list
            ]);
            return;
        }


        $head = $this->seo->render(
            "Dashboard | " . CONF_SITE_NAME,
            CONF_SITE_DESC,
            url("/admin"),
            theme("/assets/images/shere.jpg", CONF_VIEW_ADMIN),
            false
        );

        echo $this->view->render("widgets/dash/home", [
            "app" => "dash",
            "head" => $head,
            "users" => (object)[
                "users" => (new User())->find("level < 5")->count(),
                "admins" => (new User())->find("level >= 5")->count()
            ],
            "online" => (new Online())->findByActive(),
            "onlineCount" => (new Online())->findByActive(true),
            "filesOn" => (new File())->find("status = :s", "s=approved")->count(),
            "filesOff" => (new File())->find("status != :s", "s=approved")->count(),
            "vacsOn" => (new Vacancy())->find()->count(),
            "employerOn" => (new Employer())->find("status = :s", "s=confirmed")->count(),
            "employerOff" => (new Employer())->find("status = :s", "s=registred")->count()
        ]);
    }

    public function charts(): void
    {
        $head = $this->seo->render(
            "Dashboard :: Acompanhamentos | " . CONF_SITE_NAME,
            CONF_SITE_DESC,
            url("/admin"),
            theme("/assets/images/shere.jpg", CONF_VIEW_ADMIN),
            false
        );

        /* CHART - Access */
        $daysChart = [];
        for ($day = -7; $day <= 0; $day++) {
            $daysChart[] = date("d/m", strtotime("{$day}day"));
        }

        $dataChart = new \stdClass();
        $dataChart->label = "'" . implode("','", $daysChart) . "'";
        $dataChart->users = "0, 0, 0, 0, 0, 0, 0";
        $dataChart->viewa = "0, 0, 0, 0, 0, 0, 0";
        $dataChart->pages = "0, 0, 0, 0, 0, 0, 0";


        $charRead = (new Access())
            ->find("created_at >= DATE(now() - INTERVAL 7 DAY) GROUP BY day(created_at) ASC", null,
                "day(created_at) AS day,
                        month(created_at) AS month,
                        DATE_FORMAT(created_at, '%d/%m') AS dateViews,
                        (SELECT SUM(users) FROM report_access WHERE day(created_at) = day AND month(created_at) = month) AS users,
                        (SELECT SUM(views) FROM report_access WHERE day(created_at) = day AND month(created_at) = month) AS views,
                        (SELECT SUM(pages) FROM report_access WHERE day(created_at) = day AND month(created_at) = month) AS pages
                ")->limit(7)->fetch(true);


        if ($charRead) {
            $chartLabel = [];
            $chartUsers = [];
            $chartViews = [];
            $chartPages = [];

            foreach ($charRead as $chartItem) {
                $chartLabel[] = $chartItem->dateViews;
                $chartUsers[] = $chartItem->users;
                $chartViews[] = $chartItem->views;
                $chartPages[] = $chartItem->pages;
            }

            $dataChart->label = "'" . implode("','", $chartLabel) . "'";
            $dataChart->users = implode(",", array_map("abs", $chartUsers));
            $dataChart->views = implode(",", array_map("abs", $chartViews));
            $dataChart->pages = implode(",", array_map("abs", $chartPages));
        }
        /* END CHART - Access */

        /* CHART - new-users */
        $weekChart = [];
        for ($week = -7; $week <= 0; $week++) {
            $weekChart[] = date("W", strtotime("{$week}week"));
        }

        $newsUserChart = new \stdClass();
        $newsUserChart->label = "'" . implode("','", $weekChart) . "'";
        $newsUserChart->newsCount = "0, 0, 0, 0, 0, 0, 0";

        $readNewUsers = (new User())->find(null, null, "
                (SELECT COUNT(*) FROM users WHERE YEARWEEK(`created_at`, 1) = YEARWEEK(CURDATE(), 1)) AS thisWeek, 
                (SELECT COUNT(*) FROM users WHERE YEARWEEK(`created_at`, 1) = YEARWEEK(CURDATE() - INTERVAL 1 WEEK, 1)) AS firstWeek,
                (SELECT COUNT(*) FROM users WHERE YEARWEEK(`created_at`, 1) = YEARWEEK(CURDATE() - INTERVAL 2 WEEK, 1)) AS secondWeek,
                (SELECT COUNT(*) FROM users WHERE YEARWEEK(`created_at`, 1) = YEARWEEK(CURDATE() - INTERVAL 3 WEEK, 1)) AS thirthWeek,
                (SELECT COUNT(*) FROM users WHERE YEARWEEK(`created_at`, 1) = YEARWEEK(CURDATE() - INTERVAL 4 WEEK, 1)) AS fourthWeek,
                (SELECT COUNT(*) FROM users WHERE YEARWEEK(`created_at`, 1) = YEARWEEK(CURDATE() - INTERVAL 5 WEEK, 1)) AS fifthWeek,
                (SELECT COUNT(*) FROM users WHERE YEARWEEK(`created_at`, 1) = YEARWEEK(CURDATE() - INTERVAL 6 WEEK, 1)) AS sixthWeek,
                (SELECT COUNT(*) FROM users WHERE YEARWEEK(`created_at`, 1) = YEARWEEK(CURDATE() - INTERVAL 7 WEEK, 1)) AS seventhWeek
            ")->fetch();

        $newsUserChart = new \stdClass();
        $newsUserChart->label = "'" . implode("','", $weekChart) . "'";

        if ($readNewUsers) {

            $chartLabelNewsUser = [];
            $chartLabelNewsUser[] = (int)$readNewUsers->thisWeek;
            $chartLabelNewsUser[] = (int)$readNewUsers->firstWeek;
            $chartLabelNewsUser[] = (int)$readNewUsers->secondWeek;
            $chartLabelNewsUser[] = (int)$readNewUsers->thirthWeek;
            $chartLabelNewsUser[] = (int)$readNewUsers->fourthWeek;
            $chartLabelNewsUser[] = (int)$readNewUsers->fifthWeek;
            $chartLabelNewsUser[] = (int)$readNewUsers->sixthWeek;
            $chartLabelNewsUser[] = (int)$readNewUsers->seventhWeek;
            $chartLabelNewsUser = array_reverse($chartLabelNewsUser);
            $newsUserChart->userNews = implode(",", $chartLabelNewsUser);


        }
        /* END CHART - demografic-users */

        /* CHART - demografic-users */
        $userRead = (new User())->find(null, null, "
            (SELECT COUNT(datebirth) FROM users WHERE year(datebirth) <= 2001 AND year(datebirth) >= 1995) AS first,
            (SELECT COUNT(datebirth) FROM users WHERE year(datebirth) <= 1995 AND year(datebirth) >= 1989) AS second,
            (SELECT COUNT(datebirth) FROM users WHERE year(datebirth) <= 1988 AND year(datebirth) >= 1981) AS third,
            (SELECT COUNT(datebirth) FROM users WHERE year(datebirth) <= 1980 AND year(datebirth) >= 1969) AS fourth,
            (SELECT COUNT(datebirth) FROM users WHERE year(datebirth) <= 1968) AS fifth
        ")->fetch();

        /* END CHART - demografic-users */

        echo $this->view->render("widgets/dash/charts", [
            "app" => "dash/charts",
            "head" => $head,
            "dataChart" => $dataChart,
            "userChart" => $userRead,
            "newsUserChart" => $newsUserChart
        ]);
    }

    public function logoff(): void
    {
        $this->message->success("Você saiu com sucesso {$this->user->first_name}. Não esta mais conectado na administração.")->flash();

        Auth::logout();
        redirect("/admin/login");
    }
}