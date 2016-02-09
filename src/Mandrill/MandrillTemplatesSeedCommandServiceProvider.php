<?php namespace DeSmart\Mailer\Mandrill;

use DeSmart\Mailer\Mandrill\Console\MandrillTemplatesSeedCommand;
use Illuminate\Support\ServiceProvider;

class MandrillTemplatesSeedCommandServiceProvider extends ServiceProvider
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
        $configPath = __DIR__ . '/../../config/mandrill-templates.php';

        $this->publishes([
            $configPath => config_path('mandrill-templates.php'),
        ]);
    }

    /**
     * @return void
     */
    protected function registerClasses()
    {
        $this->app->bind(MandrillTemplatesSeedCommand::class, function () {
            return new MandrillTemplatesSeedCommand(
                new \Weblee\Mandrill\Mail($this->app['config']['mailer']['apikey']),
                config('mandrill-templates') ?: []
            );
        });
    }

    /**
     * @return void
     */
    protected function registerCommands()
    {
        $this->commands(MandrillTemplatesSeedCommand::class);
    }
}
