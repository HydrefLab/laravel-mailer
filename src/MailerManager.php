<?php namespace DeSmart\Mailer;

use Illuminate\Support\Manager;

class MailerManager extends Manager
{
    /**
     * @return string
     */
    public function getDefaultDriver()
    {
        return $this->app['config']['mailer']['driver'];
    }

    /**
     * @return Mandrill\Mailer
     */
    protected function createMandrillDriver()
    {
        return new \DeSmart\Mailer\Mandrill\Mailer(
            new \Weblee\Mandrill\Mail($this->app['config']['mailer']['apikey']),
            $this->app->make(\Illuminate\Contracts\Queue\Queue::class),
            $this->app['config']['mailer']['from']['address'],
            $this->app['config']['mailer']['from']['name']
        );
    }

    /**
     * @return SendGrid\Mailer
     */
    protected function createSendgridDriver()
    {
        return new \DeSmart\Mailer\SendGrid\Mailer(
            new \SendGrid($this->app['config']['mailer']['apikey']),
            $this->app->make(\Illuminate\Contracts\Queue\Queue::class),
            $this->app->make(\Illuminate\Contracts\Filesystem\Filesystem::class),
            $this->app['config']['mailer']['from']['address'],
            $this->app['config']['mailer']['from']['name']
        );
    }
}
