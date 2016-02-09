<?php namespace DeSmart\Mailer;

use Illuminate\Support\ServiceProvider;

class MailerServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function boot()
    {
        $this->loadConfiguration();
    }

    /**
     * @return void
     */
    public function register()
    {
        $this->registerClasses();
    }

    /**
     * @return void
     */
    protected function loadConfiguration()
    {
        $configPath = __DIR__ . '/../config/mailer.php';

        $this->publishes([
            $configPath => config_path('mailer.php'),
        ]);
    }

    /**
     * @return void
     */
    protected function registerClasses()
    {
        $this->app->bind(\DeSmart\Mailer\MailerInterface::class, function ($app) {
            $manager = new MailerManager($app);

            return $manager->driver();
        });
    }
}
