<?php namespace DeSmart\Mailer;

interface MailerInterface
{
    /**
     * @param string $email
     * @return void
     */
    public function setFromEmail($email);

    /**
     * @param string $name
     * @return void
     */
    public function setFromName($name);

    /**
     * @param string $subject
     * @return void
     */
    public function setSubject($subject);

    /**
     * @param string $template
     * @return void
     */
    public function setTemplate($template);

    /**
     * @param string|null $subject
     * @param string|null $template
     * @return bool
     */
    public function send($subject = null, $template = null);

    /**
     * @return void
     */
    public function clear();

    /**
     * @param string $queue
     * @param string|null $subject
     * @param string|null $template
     * @return bool
     */
    public function queue($queue, $subject = null, $template = null);

    /**
     * @param Recipient $recipient
     * @return void
     */
    public function addRecipient(Recipient $recipient);

    /**
     * @param Header $header
     * @return void
     */
    public function addHeader(Header $header);

    /**
     * @param string $email
     * @return void
     */
    public function setReplyTo($email);

    /**
     * @param Variable $variable
     * @return void
     */
    public function addGlobalVariable(Variable $variable);

    /**
     * @param Recipient $recipient
     * @param Variable $variable
     * @return void
     */
    public function addLocalVariable(Recipient $recipient, Variable $variable);

    /**
     * @param Attachment $attachment
     * @return void
     */
    public function addAttachment(Attachment $attachment);

    /**
     * @return array
     */
    public function getData();

    /**
     * @param array $data
     * @return void
     */
    public function setData(array $data);
}