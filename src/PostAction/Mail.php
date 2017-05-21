<?php declare(strict_types=1);

namespace App\PostAction;

/**
 * Class Mail
 *
 * @author Jakub Igla <jakub.igla@valtech.co.uk>
 */
class Mail implements PostActionInterface
{
    /** @var array */
    private $config;

    /**
     * Mail constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function run(): void
    {
        $mail = new \PHPMailer();

        $mail->IsSMTP();
        $mail->Host = "smtp.office365.com";
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = "tls";
        $mail->Port = 587;
        $mail->Username = $this->config['login'];
        $mail->Password = $this->config['password'];
        $mail->SetFrom($this->config['login']);
        $mail->AddReplyTo($this->config['login']);
        $mail->Subject = $this->config['subject'];
        $body = $this->config['body'];
        $mail->Body = $body;
        $mail->addAddress($this->config['to']);
        if (isset($this->config['cc'])) {
            $mail->addCC($this->config['cc']);
        }

        if (!$mail->Send()) {
            echo "Mailer Error: " . $mail->ErrorInfo . PHP_EOL;
        } else {
            echo "Message sent!" . PHP_EOL;
        }
    }
}
