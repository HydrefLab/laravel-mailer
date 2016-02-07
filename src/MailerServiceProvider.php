<?php namespace DeSmart\Mailer;

use DeSmart\Mailer\Mandrill\Console\MandrillTemplatesSeedCommand;
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
        $this->registerCommands();
    }

    /**
     * @return void
     */
    protected function loadConfiguration()
    {
        $configPath = __DIR__ . '/../../config/mailer.php';
        $mandrillTemplatesConfigPath = __DIR__ . '/../../config/mandrill-templates.php';

        $this->publishes([
            $configPath => config_path('mailer.php'),
            $mandrillTemplatesConfigPath => config_path('mandrill-templates.php'),
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

        $this->app->bind(\DeSmart\Mailer\Mandrill\Console\MandrillTemplatesSeedCommand::class, function () {
            return new MandrillTemplatesSeedCommand(
                $this->app->make(\Weblee\Mandrill\Mail::class),
                config('mandrill-templates')
            );
        });
    }

    /**
     * @return void
     */
    protected function registerCommands()
    {
        $this->commands(\DeSmart\Mailer\Mandrill\Console\MandrillTemplatesSeedCommand::class);
    }
} 