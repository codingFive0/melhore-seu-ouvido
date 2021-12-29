<?php

namespace Source\App\Admin;

use Source\Models\Notification;

class Notifications extends Admin
{
    public function __construct()
    {
        parent::__construct(__DIR__ . "/../../../themes/" . CONF_VIEW_ADMIN);
    }

    public function count()
    {
        $json['count'] = (new Notification())->find("view < 1")->count();
        echo json_encode($json);
    }

    public function list()
    {
        $notifications = (new Notification())->find()->order("view ASC, created_at DESC")->limit(3)->fetch(true);

        if(!$notifications){
            $json['message'] = $this->message->success("Não existem notificações novas no mometo")->mount();
            echo json_encode($json);
            return;
        }

        $notificationsList = null;

        foreach ($notifications as $notification){
            $notification->view = 1;
            $notification->save();
            $notification->created_at = date_fmt($notification->created_at, "d/m/Y - H\h\s i\m\i\n");

            $notificationsList[] = $notification->data();
        }

        echo json_encode(["notifications" => $notificationsList]);
    }
}