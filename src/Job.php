<?php namespace DeSmart\Mailer;

class Job
{
    /** @var array */
    protected $data = [];

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @param MailerInterface $mailer
     * @return bool
     */
    public function handle(MailerInterface $mailer)
    {
        $mailer->setData($this->data);
        $mailer->send();
    }
}