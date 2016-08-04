<?php namespace DeSmart\Mailer\Mandrill;

use DeSmart\Mailer\Attachment;
use DeSmart\Mailer\Header;
use DeSmart\Mailer\Job;
use DeSmart\Mailer\MailerInterface;
use DeSmart\Mailer\Recipient;
use DeSmart\Mailer\Variable;

class Mailer implements MailerInterface
{
    /**  @var \Weblee\Mandrill\Mail */
    protected $mandrill;
    /** @var \Illuminate\Contracts\Queue\Queue */
    protected $queue;
    /** @var string */
    protected $fromEmail;
    /** @var string */
    protected $fromName;
    /** @var string */
    protected $subject;
    /** @var string */
    protected $template;
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
     * @param \Illuminate\Contracts\Queue\Queue $queue
     * @param string $fromEmail
     * @param string $fromName
     */
    public function __construct(
        \Weblee\Mandrill\Mail $mandrill,
        \Illuminate\Contracts\Queue\Queue $queue,
        $fromEmail,
        $fromName
    ) {
        $this->mandrill = $mandrill;
        $this->queue = $queue;
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
     * @param string $email
     * @return void
     */
    public function setReplyTo($email)
    {
        $this->headers['Reply-To'] = $email;
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
            'content' => base64_encode($attachment->getContent()),
        ];
    }

    /**
     * @param string|null $subject
     * @param string|null $template
     * @return bool
     * @throws \Mandrill_Error
     */
    public function send($subject = null, $template = null)
    {
        if (null !== $subject) {
            $this->setSubject($subject);
        }

        if (null !== $template) {
            $this->setTemplate($template);
        }

        $recipients = [];

        foreach ($this->recipients as $recipient) {
            /** @var Recipient $recipient */
            $recipients[] = [
                'email' => $recipient->getEmail(),
                'name' => $recipient->getName(),
                'type' => $recipient->getType()->getType(),
            ];
        }

        $message = [
            'subject' => $this->subject,
            'from_email' => $this->fromEmail,
            'from_name' => $this->fromName,
            'to' => $recipients,
            'headers' => $this->headers,
            'merge_vars' => $this->getLocalVariables(),
            'global_merge_vars' => $this->getGlobalVariables(),
            'attachments' => $this->attachments,
        ];

        $this->mandrill->messages()->sendTemplate($this->template, [], $message);

        return true;
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
        $this->attachments = $data['attachments'];
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
