<?php namespace DeSmart\Mailer\Sendgrid;

use DeSmart\Mailer\Attachment;
use DeSmart\Mailer\MailerInterface;
use DeSmart\Mailer\Recipient;
use DeSmart\Mailer\Variable;

class Mailer implements MailerInterface
{
    /**
     * @param string $email
     * @return void
     */
    public function setFromEmail($email)
    {
    }

    /**
     * @param string $name
     * @return void
     */
    public function setFromName($name)
    {
    }

    /**
     * @param Recipient $recipient
     * @return void
     */
    public function addRecipient(Recipient $recipient)
    {
    }

    /**
     * @param Variable $variable
     * @return void
     */
    public function addGlobalVariable(Variable $variable)
    {
    }

    /**
     * @param Recipient $recipient
     * @param Variable $variable
     * @return void
     */
    public function addLocalVariable(Recipient $recipient, Variable $variable)
    {
    }

    /**
     * @param Attachment $attachment
     * @return void
     */
    public function addAttachment(Attachment $attachment)
    {
    }

    /**
     * @param string $subject
     * @param string $template
     * @return bool
     */
    public function send($subject, $template)
    {
    }
}