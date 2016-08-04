<?php namespace DeSmart\Mailer\SendGrid;

use DeSmart\Mailer\Attachment;
use DeSmart\Mailer\Header;
use DeSmart\Mailer\Job;
use DeSmart\Mailer\MailerInterface;
use DeSmart\Mailer\Recipient;
use DeSmart\Mailer\RecipientType;
use DeSmart\Mailer\Variable;

class Mailer implements MailerInterface
{
    /** @var \SendGrid */
    protected $sendgrid;
    /** @var \Illuminate\Contracts\Queue\Queue */
    protected $queue;
    /** @var \Illuminate\Contracts\Filesystem\Filesystem */
    protected $storage;
    /** @var string */
    protected $fromEmail;
    /** @var string */
    protected $fromName;
    /** @var string */
    protected $subject;
    /** @var string */
    protected $template;
    /** @var Recipient[] */
    protected $recipients = [];
    /** @var Header[] */
    protected $headers = [];
    /** @var string|null */
    protected $replyTo = null;
    /** @var Variable */
    protected $globalVariables = [];
    /** @var Variable[] */
    protected $localVariables = [];
    /** @var Attachment[] */
    protected $attachments = [];

    /**
     * @param \SendGrid $sendgrid
     * @param \Illuminate\Contracts\Queue\Queue $queue
     * @param \Illuminate\Contracts\Filesystem\Filesystem $storage
     * @param $fromEmail
     * @param $fromName
     */
    public function __construct(
        \SendGrid $sendgrid,
        \Illuminate\Contracts\Queue\Queue $queue,
        \Illuminate\Contracts\Filesystem\Filesystem $storage,
        $fromEmail,
        $fromName
    ) {
        $this->sendgrid = $sendgrid;
        $this->queue = $queue;
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
     * @return void
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
     * @param string $template
     * @return void
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }

    /**
     * @param string|null $subject
     * @param string|null $template
     * @return bool
     * @throws \SendGrid\Exception
     */
    public function send($subject = null, $template = null)
    {
        if (null !== $subject) {
            $this->setSubject($subject);
        }

        if (null !== $template) {
            $this->setTemplate($template);
        }

        $email = new \SendGrid\Email();
        $email->setFrom($this->fromEmail);
        $email->setFromName($this->fromName);
        $email->setSubject($this->subject);
        $email->setHtml(' ');
        $email->setTemplateId($this->template);

        foreach ($this->recipients as $recipient) {
            /**
             * SendGrid API library requires to use 'addSmtpapiTo' method in case of many recipients.
             * On the other side, it does not allow to mix Smtpapi methods with non-Smtpapi methods, therefore,
             * original methods 'addBcc' and 'addCc' cannot be used here.
             */
            $email->addSmtpapiTo($recipient->getEmail(), $recipient->getName());
        }

        $email->setSubstitutions($this->getLocalVariables());
        $email->setAttachments($this->attachments);

        $this->setSections($email);

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
     * @return void
     */
    public function clear()
    {
        $this->recipients = [];
        $this->headers = [];
        $this->globalVariables = [];
        $this->localVariables = [];
        $this->attachments = [];
    }

    /**
     * @param string $queue
     * @param string|null $subject
     * @param string|null $template
     * @return bool
     */
    public function queue($queue, $subject = null, $template = null)
    {
        if (null !== $subject) {
            $this->setSubject($subject);
        }

        if (null !== $template) {
            $this->setTemplate($template);
        }

        $this->queue->pushOn($queue, new Job($this->getData()));

        return true;
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
    public function getData()
    {
        return [
            'from_name' => $this->fromName,
            'from_email' => $this->fromEmail,
            'subject' => $this->subject,
            'template' => $this->template,
            'recipients' => $this->recipients,
            'global_vars' => $this->globalVariables,
            'local_vars' => $this->localVariables,
            'headers' => $this->headers,
            'reply_to' => $this->replyTo,
            'attachments' => $this->attachments,
        ];
    }

    /**
     * @param array $data
     * @return void
     */
    public function setData(array $data)
    {
        $this->fromName = $data['from_name'];
        $this->fromEmail = $data['from_email'];
        $this->subject = $data['subject'];
        $this->template = $data['template'];
        $this->recipients = $data['recipients'];
        $this->globalVariables = $data['global_vars'];
        $this->localVariables = $data['local_vars'];
        $this->headers = $data['headers'];
        $this->replyTo = $data['reply_to'];
        $this->attachments = $data['attachments'];
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
     * In SendGrid sections are used only with connection to substitutions.
     * Here, we're adding 'fake' substitutions that use defined sections.
     *
     * @param \SendGrid\Email $email
     * @return void
     */
    protected function setSections(\SendGrid\Email $email)
    {
        if (false === empty($this->globalVariables)) {
            foreach ($this->globalVariables as $name => $value) {
                /** @var Variable $globalVariable */

                $sectionName = $name . '_SECTION';
                $substitution = array_fill(0, count($this->recipients), $sectionName);

                $email->addSection($sectionName, $value);
                $email->addSubstitution($name, $substitution);
            }
        }
    }

    /**
     * @return void
     */
    protected function cleanAttachments()
    {
        $this->storage->delete(array_keys($this->attachments));
    }
}
