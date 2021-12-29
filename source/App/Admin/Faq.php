<?php

namespace Source\App\Admin;

use Source\Models\Faq\Channel;
use Source\Models\Faq\Questions;
use Source\Support\Pager;

class Faq extends Admin
{
    public function __construct()
    {
        parent::__construct(__DIR__ . "/../../../themes/" . CONF_VIEW_ADMIN);
    }

    public function home(?array $data): void
    {
        $channel = (new Channel())->find();
        $pager = new Pager(url("/admin/faq/home/"));
        $pager->pager($channel->count(), 5, ($data['page'] ?? 1));

        $head = $this->seo->render(
            "Gestão de FAQs | " . CONF_SITE_NAME,
            CONF_SITE_DESC,
            url("/admin"),
            theme("/assets/images/shere.jpg", CONF_VIEW_ADMIN),
            false
        );

        echo $this->view->render("widgets/faqs/home", [
            "app" => "faq/home",
            "head" => $head,
            "channels" => $channel->limit($pager->limit())->offset($pager->offset())->order("created_at DESC")->fetch(true),
            "paginator" => $pager->render()
        ]);
    }

    public function channel(?array $data): void
    {
        //CREATE
        if (!empty($data['action']) && $data['action'] == "create") {
            $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

            $channelCreate = new Channel();
            $channelCreate->channel = $data['channel'];
            $channelCreate->description = $data['description'];
            $channelCreate->page = $data['page'];

            if (!$channelCreate->save()) {
                $json['message'] = $channelCreate->message()->mount();
                echo json_encode($json);
                return;
            }

            $this->message->success("Novo canal de perguntas criado com sucesso. Confira...")->flash();
            echo json_encode(["redirect" => url("/admin/faq/channel/{$channelCreate->id}")]);
            return;
        }
        //UPDATE
        if (!empty($data['action']) && $data['action'] == "update") {
            $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

            $channelUpdate = (new Channel())->findById($data['channel_id']);

            if (!$channelUpdate) {
                $this->message->warning("O canal que você tentou editar não existe ou foi removido")->flash();
                echo json_encode(["redirect" => url("/admin/faq/home")]);
                return;
            }

            $channelUpdate->channel = $data['channel'];
            $channelUpdate->description = $data['description'];
            $channelUpdate->page = $data['page'];

            if (!$channelUpdate->save()) {
                $json['message'] = $channelUpdate->message()->mount();
                echo json_encode($json);
                return;
            }

            $this->message->success("Novo canal de perguntas atualizado com sucesso")->flash();
            echo json_encode(["reload" => true]);
            return;
        }
        //DELETE
        if (!empty($data['action']) && $data['action'] == "delete") {
            $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);
            $channelDelete = (new Channel())->findById($data['channel_id']);

            if (!$channelDelete) {
                $this->message->warning("O canal que você tentou excluir não existe ou já foi removido")->flash();
                echo json_encode(["redirect" => url("/admin/faq/channel")]);
                return;
            }

            $channelDelete->destroy();

            $this->message->success("O canal foi excluido com sucesso")->flash();
            echo json_encode(["redirect" => url("/admin/faq/home")]);
            return;
        }

        $channelId = (!empty($data['channel_id']) ? filter_var($data['channel_id'], FILTER_VALIDATE_INT) : null);
        $channel = null;

        if ($channelId) {
            $channel = (new Channel())->findById($channelId);
        }

        $head = $this->seo->render(
            "Gestão das FAQs | " . CONF_SITE_NAME,
            CONF_SITE_DESC,
            url("/admin"),
            theme("/assets/images/shere.jpg", CONF_VIEW_ADMIN),
            false
        );

        echo $this->view->render("widgets/faqs/channel", [
            "app" => "faq/home",
            "head" => $head,
            "channel" => $channel,
        ]);
    }

    public function question(?array $data): void
    {

        //CREATE
        if (!empty($data['action']) && $data['action'] == "create") {
            $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

            $questionCreate = new Questions();
            $questionCreate->channel_id = $data['channel_id'];
            $questionCreate->question = $data['question'];
            $questionCreate->response = $data['response'];
            $questionCreate->order_by = $data['order_by'];

            if (!$questionCreate->save()) {
                $json['message'] = $questionCreate->message()->mount();
                echo json_encode($json);
                return;
            }

            $this->message->success("Nova pergunta criada com sucesso. Confira...")->flash();
            echo json_encode(["redirect" => url("/admin/faq/question/{$questionCreate->channel_id}/{$questionCreate->id}")]);
            return;
        }

        //UPDATE
        if (!empty($data['action']) && $data['action'] == "update") {
            $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

            $questionUpdate = (new Questions())->findById($data['question_id']);

            if (!$questionUpdate) {
                $this->message->warning("A pergunta que você tentou editar não existe ou foi removida")->flash();
                echo json_encode(["redirect" => url("/admin/faq/home")]);
                return;
            }

            $questionUpdate->channel_id = $data['channel_id'];
            $questionUpdate->question = $data['question'];
            $questionUpdate->response = $data['response'];
            $questionUpdate->order_by = $data['order_by'];

            if (!$questionUpdate->save()) {
                $json['message'] = $questionUpdate->message()->mount();
                echo json_encode($json);
                return;
            }

            $this->message->success("Perguntas atualizada com sucesso")->flash();
            echo json_encode(["reload" => true]);
            return;
        }

        //DELETE
        if (!empty($data['action']) && $data['action'] == "delete") {
            $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);
            $questionDelete = (new Questions())->findById($data['question_id']);

            if (!$questionDelete) {
                $this->message->warning("A pergunta que você tentou excluir não existe ou já foi removida")->flash();
                echo json_encode(["redirect" => url("/admin/faq/channel")]);
                return;
            }

            $questionDelete->destroy();

            $this->message->success("A pergunta foi excluida com sucesso")->flash();
            echo json_encode(["redirect" => url("/admin/faq/home")]);
            return;
        }

        $data = (!empty($data) ? filter_var_array($data, FILTER_SANITIZE_STRIPPED) : null);
        $question = null;

        $channel = (new Channel())->findById($data['channel_id']);
        if (!$channel) {
            $this->message->warning("Você tentou editar uma pergunta de um canal que não existe ou foi removido")->flash();
            echo json_encode(["redirect" => url("/admin/faq/home")]);
            return;
        }

        if (!empty($data['question_id'])) {
            $questionId = filter_var($data['question_id'], FILTER_VALIDATE_INT);
            $question = (new Questions())->findById($questionId);
        }

        $head = $this->seo->render(
            "Gestão das FAQs | " . CONF_SITE_NAME,
            CONF_SITE_DESC,
            url("/admin"),
            theme("/assets/images/shere.jpg", CONF_VIEW_ADMIN),
            false
        );

        echo $this->view->render("widgets/faqs/question", [
            "app" => "faq/home",
            "head" => $head,
            "question" => $question,
            "channel" => $channel
        ]);
    }
}