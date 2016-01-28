<?php namespace DeSmart\Mailer\Mandrill;

use DeSmart\Mailer\Attachment;
use DeSmart\Mailer\Header;
use DeSmart\Mailer\MailerInterface;
use DeSmart\Mailer\Recipient;
use DeSmart\Mailer\Variable;

class Mailer implements MailerInterface
{
    /**  @var \Weblee\Mandrill\Mail */
    protected $mandrill;
    /** @var string */
    protected $fromEmail;
    /** @var string */
    protected $fromName;
    /** @var array */
    protected $recipients = [];
    /** @var array */
    protected $headers = [];
    /** @var array */
    protected $globalVariables = [];
    /** @var array */
    protected $localVariables = [];
    /** @var array */
    protected $attachments = [];

    /**
     * @param \Weblee\Mandrill\Mail $mandrill
     * @param string $fromEmail
     * @param string $fromName
     */
    public function __construct(\Weblee\Mandrill\Mail $mandrill, $fromEmail, $fromName)
    {
        $this->mandrill = $mandrill;
        $this->fromEmail = $fromEmail;
        $this->fromName = $fromName;
    }

    /**
     * @param string $email
     * @return void
     */
    public function setFromEmail($email)
    {
        $this->fromEmail = $email;
    }

    /**
     * @param string $name
     * @return void
     */
    public function setFromName($name)
    {
        $this->fromName = $name;
    }

    /**
     * @param Recipient $recipient
     * @return void
     */
    public function addRecipient(Recipient $recipient)
    {
        $this->recipients[] = $recipient;
    }

    /**
     * @param Header $header
     * @return void
     */
    public function addHeader(Header $header)
    {
        $this->headers[$header->getName()] = $header->getValue();
    }

    /**
     * @param Variable $variable
     * @return void
     */
    public function addGlobalVariable(Variable $variable)
    {
        $this->globalVariables[$variable->getName()] = [
            'name' => strtoupper($variable->getName()),
            'content' => $variable->getValue(),
        ];
    }

    /**
     * @param Recipient $recipient
     * @param Variable $variable
     * @return void
     */
    public function addLocalVariable(Recipient $recipient, Variable $variable)
    {
        $this->localVariables[$recipient->getEmail()][$variable->getName()] = [
            'name' => strtoupper($variable->getName()),
            'content' => $variable->getValue(),
        ];
    }

    /**
     * @param Attachment $attachment
     * @return void
     */
    public function addAttachment(Attachment $attachment)
    {
        $this->attachments[] = [
            'type' => $attachment->getType(),
            'name' => $attachment->getName(),
            'content' => $attachment->getContent(),
        ];
    }

    /**
     * @param string $subject
     * @param string $template
     * @return bool
     */
    public function send($subject, $template)
    {
        $recipients = [];

        foreach ($this->recipients as $recipient) {
            /** @var Recipient $recipient */
            $recipients[] = [
                'email' => $recipient->getEmail(),
                'name' => $recipient->getName(),
                'type' => 'to',
            ];
        }

        $message = [
            'subject' => $subject,
            'from_email' => $this->fromEmail,
            'from_name' => $this->fromName,
            'to' => $recipients,
            'headers' => $this->headers,
            'merge_vars' => $this->getLocalVariables(),
            'global_merge_vars' => $this->getGlobalVariables(),
            'attachments' => $this->attachments,
        ];

        try {
            $this->mandrill->messages()->sendTemplate($template, [], $message);

            return true;
        } catch (\Mandrill_Error $e) {
            return false;
        }
    }

    /**
     * @return array
     */
    protected function getGlobalVariables()
    {
        return array_values($this->globalVariables);
    }

    /**
     * @return array
     */
    protected function getLocalVariables()
    {
        $localVariables = [];

        foreach ($this->localVariables as $recipient => $variables) {
            $localVariables[] = [
                'rcpt' => $recipient,
                'vars' => array_values($variables),
            ];
        }

        return $localVariables;
    }
}