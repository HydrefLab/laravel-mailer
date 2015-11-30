<?php namespace spec\DeSmart\Mailer\Mandrill\Console;

use DeSmart\Mailer\Mandrill\Console\MandrillTemplatesSeedCommand;
use Illuminate\Console\Command;
use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class MandrillTemplatesSeedCommandSpec extends ObjectBehavior
{
    protected $templates = [
        'Example template #1' => [
            'from_email' => null,
            'from_name' => null,
            'subject' => 'Example subject #1',
            'code' => 'Example template content #1',
            'text' => null,
            'publish' => true,
            'labels' => array(),
        ],
        'Example template #2' => [
            'from_email' => null,
            'from_name' => null,
            'subject' => 'Example subject #2',
            'code' => 'Example template content #2',
            'text' => null,
            'publish' => true,
            'labels' => array(),
        ],
    ];

    public function it_is_initializable()
    {
        $this->shouldHaveType(\DeSmart\Mailer\Mandrill\Console\MandrillTemplatesSeedCommand::class);
    }

    public function let(\Weblee\Mandrill\Mail $mandrill)
    {
        $this->beConstructedWith($mandrill, $this->templates);
    }

    public function it_should_be_command()
    {
        $this->shouldHaveType(\Illuminate\Console\Command::class);
    }
}
