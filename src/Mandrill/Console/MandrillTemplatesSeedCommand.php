<?php namespace DeSmart\Mailer\Mandrill\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Weblee\Mandrill\Mail;

class MandrillTemplatesSeedCommand extends Command
{
    /** @var string */
    protected $signature = 'mandrill_templates:seed {--templates= : Set templates for seeding}';
    /** @var string */
    protected $description = 'Seed Mandrill with templates.';
    /** @var Mail */
    protected $mandrill;
    /** @var array */
    protected $templates;
    /** @var array */
    protected $templatesFilter = [];

    /**
     * @param Mail $mandrill
     * @param array $templates
     */
    public function __construct(Mail $mandrill, array $templates)
    {
        parent::__construct();

        $this->mandrill = $mandrill;
        $this->templates = $templates;
    }

    /**
     * @return void
     */
    public function handle()
    {
        if ($this->option('templates')) {
            $this->templatesFilter = array_map('trim', explode(',', $this->option('templates')));
        }

        foreach ($this->getTemplatesToSeed() as $name => $data) {
            /**
             * This function will create following variables:
             * $from_email, $from_name, $subject, $code, $text, $publish, $labels.
             */
            extract($data);

            try {
                $this->mandrill->templates()->add(
                    $name,
                    $from_email,
                    $from_name,
                    $subject,
                    $code,
                    $text,
                    $publish,
                    $labels
                );

                $this->info(sprintf('"%s" template has been added.', $name));
            } catch (\Mandrill_Invalid_Template $e) {
                $this->mandrill->templates()->update(
                    $name,
                    $from_email,
                    $from_name,
                    $subject,
                    $code,
                    $text,
                    $publish,
                    $labels
                );

                $this->info(sprintf('"%s" template already exists. Template has been updated.', $name));
            } catch (\Mandrill_Error $e) {
                $this->error(sprintf(
                    '"%s" template cannot be added/updated. There has been an error: %s.',
                    $name,
                    $e->getMessage()
                ));
            }
        }
    }

    /**
     * @return array
     */
    protected function getTemplatesToSeed()
    {
        if (null !== $this->templatesFilter) {
            return array_intersect_key($this->templates, array_flip($this->templatesFilter));
        }

        return $this->templates;
    }
}
