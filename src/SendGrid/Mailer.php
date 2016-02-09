<?php namespace DeSmart\Mailer\SendGrid;

use DeSmart\Mailer\Attachment;
use DeSmart\Mailer\Header;
use DeSmart\Mailer\MailerInterface;
use DeSmart\Mailer\Recipient;
use DeSmart\Mailer\RecipientType;
use DeSmart\Mailer\Variable;
use Illuminate\Contracts\Filesystem\Filesystem;

class Mailer implements MailerInterface
{
    /** @var \SendGrid */
    protected $sendgrid;
    /** @var string */
    protected $fromEmail;
    /** @var string */
    protected $fromName;
    /** @var Filesystem */
    protected $storage;
    /** @var Recipient[] */
    protected $recipients = [];
    /** @var Header[] */
    protected $headers = [];
    /** @var string|null */
    protected $replyTo = null;
    /** @var array */
    protected $globalVariables = [];
    /** @var Variable[] */
    protected $localVariables = [];
    /** @var Attachment[] */
    protected $attachments = [];

    /**
     * @param \SendGrid $sendgrid
     * @param Filesystem $storage
     * @param $fromEmail
     * @param $fromName
     */
    public function __construct(\SendGrid $sendgrid, Filesystem $storage, $fromEmail, $fromName)
    {
        $this->sendgrid = $sendgrid;
        $this->storage = $storage;
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
     * @param string $subject
     * @param string $template
     * @return bool
     * @throws \SendGrid\Exception
     */
    public function send($subject, $template)
    {
        $email = new \SendGrid\Email();
        $email->setFrom($this->fromEmail);
        $email->setFromName($this->fromName);
        $email->setSubject($subject);
        $email->setTemplateId($template);

        foreach ($this->recipients as $recipient) {
            if (true === $recipient->getType()->equals(RecipientType::to())) {
                $email->addTo($recipient->getEmail(), $recipient->getName());
            } else if (true === $recipient->getType()->equals(RecipientType::bcc())) {
                $email->addBcc($recipient->getEmail(), $recipient->getName());
            } else if (true === $recipient->getType()->equals(RecipientType::cc())) {
                $email->addCc($recipient->getEmail(), $recipient->getName());
            }
        }

        $email->setSections($this->globalVariables);
        $email->setSubstitutions($this->getLocalVariables());
        $email->setAttachments($this->attachments);

        if (null !== $this->replyTo) {
            $email->setReplyTo($this->replyTo);
        }

        if (false === empty($this->headers)) {
            $email->setHeaders($this->headers);
        }

        try {
            $this->sendgrid->send($email);
            $this->cleanAttachments();

            return true;
        } catch (\SendGrid\Exception $e) {
            $this->cleanAttachments();

            throw $e;
        }
    }

    /**
     * @param Recipient $recipient
     * @return void
     */
    public function addRecipient(Recipient $recipient)
    {
        $this->recipients[$recipient->getEmail()] = $recipient;
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
     * @param string $email
     * @return void
     */
    public function setReplyTo($email)
    {
        $this->replyTo = $email;
    }

    /**
     * @param Variable $variable
     * @return void
     */
    public function addGlobalVariable(Variable $variable)
    {
        $this->globalVariables[$variable->getName()] = $variable->getValue();
    }

    /**
     * @param Recipient $recipient
     * @param Variable $variable
     * @return void
     */
    public function addLocalVariable(Recipient $recipient, Variable $variable)
    {
        $this->localVariables[$recipient->getEmail()][] = $variable;
    }

    /**
     * @param Attachment $attachment
     * @return void
     */
    public function addAttachment(Attachment $attachment)
    {
        /** @var \League\Flysystem\Adapter\AbstractAdapter $adapter */
        $adapter = $this->storage->getAdapter();

        $attachmentPath = 'attachments' . DIRECTORY_SEPARATOR . $attachment->getName();

        $this->storage->put($attachmentPath, $attachment->getContent());
        $this->attachments[$attachmentPath] = $adapter->getPathPrefix() . $attachmentPath;
    }

    /**
     * @return array
     */
    protected function getLocalVariables()
    {
        $localVariables = [];

        if (false === empty($this->localVariables)) {
            foreach (array_keys($this->recipients) as $email) {
                foreach ($this->localVariables[$email] as $variable) {
                    /** @var Variable $variable */
                    $localVariables[$variable->getName()][] = $variable->getValue();
                }
            }
        }

        return $localVariables;
    }

    /**
     * @return void
     */
    protected function cleanAttachments()
    {
        $this->storage->delete(array_keys($this->attachments));
    }
}
