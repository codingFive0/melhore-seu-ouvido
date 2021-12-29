<?php

namespace Source\Support;

/**
 * FSPHP | Class Email
 *
 * @author Robson V. Leite <cursos@upinside.com.br>
 * @package Source\Core
 */
class EmailWithSwift
{
    /** @var array */
    private $data;

    /** @var  \Swift_Mailer*/
    private $mail;

    /** @var  \Swift_Message*/
    private $mailMsg;

    /** @var Message */
    private $message;

    /**
     * Email constructor.
     */
    public function __construct()
    {
        $transport = new \Swift_SmtpTransport(CONF_MAIL_HOST, CONF_MAIL_PORT);
        $transport->setUsername(CONF_MAIL_USER);
        $transport->setPassword(CONF_MAIL_PASS);

        $this->mail = new \Swift_Mailer($transport);
        $this->mailMsg = new \Swift_Message();
        $this->data = new \stdClass();
        $this->message = new Message();
    }

    /**
     * @param string $subject
     * @param string $body
     * @param string $recipient
     * @param string $recipientName
     * @return Email
     */
    public function bootstrap(string $subject, string $body, string $recipient, string $recipientName): EmailWithSwift
    {
        $this->data->subject = $subject;
        $this->data->body = $body;
        $this->data->recipient_email = $recipient;
        $this->data->recipient_name = $recipientName;
        return $this;
    }

    /**
     * @param string $filePath
     * @param string $fileName
     * @return Email
     */
    public function attach(string $filePath, string $fileName): EmailWithSwift
    {
        $this->data->attach[$filePath] = $fileName;
        return $this;
    }

    /**
     * @param $from
     * @param $fromName
     * @return bool
     */
    public function send(string $from = CONF_MAIL_SENDER['address'], string $fromName = CONF_MAIL_SENDER["name"]): bool
    {
        if (empty($this->data)) {
            $this->message->error("Erro ao enviar, favor verifique os dados");
            return false;
        }

        if (!is_email($this->data->recipient_email)) {
            $this->message->warning("O e-mail de destinatário não é válido");
            return false;
        }

        if (!is_email($from)) {
            $this->message->warning("O e-mail de remetente não é válido");
            return false;
        }

        try {
            $this->mailMsg->setContentType("text/html");
            $this->mailMsg->setSubject($this->data->subject) ;
            $this->mailMsg->setBody($this->data->body);
            $this->mailMsg->setTo($this->data->recipient_email, $this->data->recipient_name);
            $this->mailMsg->setFrom($from, $fromName);

            if (!empty($this->data->attach)) {
                foreach ($this->data->attach as $path => $name) {
                    $this->mailMsg->attach(\Swift_Attachment::fromPath($path)->setFilename($name));
                }
            }

            $this->mail->send($this->mailMsg);
            return true;
        } catch (Exception $exception) {
            $this->message->error($exception->getMessage());
            return false;
        }
    }

    /**
     * @param string $from
     * @param string $fromName
     * @return bool
     */
    public function queue(string $from = CONF_MAIL_SENDER['address'], string $fromName = CONF_MAIL_SENDER["name"]): bool
    {
        try {
            $stmt = Connect::getInstance()->prepare(
                "INSERT INTO
                    mail_queue (subject, body, from_email, from_name, recipient_email, recipient_name)
                    VALUES (:subject, :body, :from_email, :from_name, :recipient_email, :recipient_name)"
            );

            $stmt->bindValue(":subject", $this->data->subject, \PDO::PARAM_STR);
            $stmt->bindValue(":body", $this->data->body, \PDO::PARAM_STR);
            $stmt->bindValue(":from_email", $from, \PDO::PARAM_STR);
            $stmt->bindValue(":from_name", $fromName, \PDO::PARAM_STR);
            $stmt->bindValue(":recipient_email", $this->data->recipient_email, \PDO::PARAM_STR);
            $stmt->bindValue(":recipient_name", $this->data->recipient_name, \PDO::PARAM_STR);

            $stmt->execute();
            return true;
        } catch (\PDOException $exception) {
            $this->message->error($exception->getMessage());
            return false;
        }
    }

    /**
     * @param int $perSecond
     */
    public function sendQueue(int $perSecond = 5)
    {
        $stmt = Connect::getInstance()->query("SELECT * FROM mail_queue WHERE sent_at IS NULL");
        if ($stmt->rowCount()) {
            foreach ($stmt->fetchAll() as $send) {
                $email = $this->bootstrap(
                    $send->subject,
                    $send->body,
                    $send->recipient_email,
                    $send->recipient_name
                );

                if ($email->send($send->from_email, $send->from_name)) {
                    usleep(1000000 / $perSecond);
                    Connect::getInstance()->exec("UPDATE mail_queue SET sent_at = NOW() WHERE id = {$send->id}");
                }
            }
        }
    }

    /**
     * @return PHPMailer
     */
    public function mail(): PHPMailer
    {
        return $this->mail;
    }

    /**
     * @return Message
     */
    public function message(): Message
    {
        return $this->message;
    }
}