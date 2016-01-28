<?php namespace spec\DeSmart\Mailer\SendGrid;

use DeSmart\Mailer\Recipient;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class MailerSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(\DeSmart\Mailer\SendGrid\Mailer::class);
    }

    public function let(\SendGrid $sendGrid)
    {
        $this->beConstructedWith($sendGrid);
    }

    public function it_should_send_email(\SendGrid $sendGrid, \SendGrid\Email $email)
    {
        $recipient = new Recipient('Jane Doe', 'janedoe@example.com');

        $this->addRecipient($recipient);

        $email = new \SendGrid\Email();
        $email->addTo('janedoe@example.com', 'Jane Doe');
        $email->setSubject('Example subject');
        $email->setTemplateId('example-template');

        $sendGrid->send($email)->shouldBeCalled();

        $this->send('Example subject', 'example-template')->shouldReturn(true);
    }
}
