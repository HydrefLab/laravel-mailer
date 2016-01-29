<?php namespace spec\DeSmart\Mailer\SendGrid;

use DeSmart\Mailer\Header;
use DeSmart\Mailer\Recipient;
use DeSmart\Mailer\RecipientType;
use DeSmart\Mailer\Variable;
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
        $this->beConstructedWith($sendGrid, 'yoda@jedi.com', 'Master Jedi Yoda');
    }

    public function it_should_send_email(\SendGrid $sendGrid)
    {
        $recipient = new Recipient('Jane Doe', 'janedoe@example.com');

        $this->addRecipient($recipient);

        $email = new \SendGrid\Email();
        $email->setFrom('yoda@jedi.com');
        $email->setFromName('Master Jedi Yoda');
        $email->addTo('janedoe@example.com', 'Jane Doe');
        $email->setSubject('Example subject');
        $email->setTemplateId('example-template');

        $sendGrid->send($email)->shouldBeCalled();

        $this->send('Example subject', 'example-template')->shouldReturn(true);
    }

    public function it_should_send_email_with_multiple_recipients(\SendGrid $sendGrid)
    {
        $recipient = new Recipient('Jane Doe', 'janedoe@example.com');
        $anotherRecipient = new Recipient('John Doe', 'johndoe@example.com');
        $ccRecipient = new Recipient('Bruce Wayne', 'brucewayne@gotham.com', RecipientType::cc());
        $bccRecipient = new Recipient('Clark Kent', 'clarkkent@dailyplanet.com', RecipientType::bcc());

        $this->addRecipient($recipient);
        $this->addRecipient($anotherRecipient);
        $this->addRecipient($ccRecipient);
        $this->addRecipient($bccRecipient);

        $email = new \SendGrid\Email();
        $email->setFrom('yoda@jedi.com');
        $email->setFromName('Master Jedi Yoda');
        $email->addTo('janedoe@example.com', 'Jane Doe');
        $email->addTo('johndoe@example.com', 'John Doe');
        $email->addCc('brucewayne@gotham.com', 'Bruce Wayne');
        $email->addBcc('clarkkent@dailyplanet.com', 'Clark Kent');
        $email->setSubject('Example subject');
        $email->setTemplateId('example-template');

        $sendGrid->send($email)->shouldBeCalled();

        $this->send('Example subject', 'example-template')->shouldReturn(true);
    }

    public function it_should_send_email_with_headers(\SendGrid $sendGrid)
    {
        $recipient = new Recipient('Jane Doe', 'janedoe@example.com');

        $header = new Header('Some-SMTP-header', 'Example value 1');
        $anotherHeader = new Header('Some-another-SMTP-header', 'Example value 2');

        $this->addRecipient($recipient);
        $this->setReplyTo('reply-to@example.com');
        $this->addHeader($header);
        $this->addHeader($anotherHeader);

        $email = new \SendGrid\Email();
        $email->setFrom('yoda@jedi.com');
        $email->setFromName('Master Jedi Yoda');
        $email->addTo('janedoe@example.com', 'Jane Doe');
        $email->setSubject('Example subject');
        $email->setTemplateId('example-template');
        $email->setReplyTo('reply-to@example.com');
        $email->setHeaders([
            'Some-SMTP-header' => 'Example value 1',
            'Some-another-SMTP-header' => 'Example value 2'
        ]);

        $sendGrid->send($email)->shouldBeCalled();

        $this->send('Example subject', 'example-template')->shouldReturn(true);
    }

    public function it_should_send_email_with_global_vars(\SendGrid $sendGrid)
    {
        $recipient = new Recipient('Jane Doe', 'janedoe@example.com');

        $variable = new Variable('Some variable', 'Some variable value');
        $anotherVariable = new Variable('Some different variable', 'Some different variable value');

        $this->addRecipient($recipient);
        $this->addGlobalVariable($variable);
        $this->addGlobalVariable($anotherVariable);

        $email = new \SendGrid\Email();
        $email->setFrom('yoda@jedi.com');
        $email->setFromName('Master Jedi Yoda');
        $email->addTo('janedoe@example.com', 'Jane Doe');
        $email->setSubject('Example subject');
        $email->setTemplateId('example-template');
        $email->setSections([
            'Some variable' => 'Some variable value',
            'Some different variable' => 'Some different variable value'
        ]);

        $sendGrid->send($email)->shouldBeCalled();

        $this->send('Example subject', 'example-template')->shouldReturn(true);
    }

    public function it_should_send_email_with_local_vars(\SendGrid $sendGrid)
    {
        $janeRecipient = new Recipient('Jane Doe', 'janedoe@example.com');
        $johnRecipient = new Recipient('John Doe', 'johndoe@example.com');

        $janeVariable = new Variable('Some variable', 'Jane value for variable');
        $johnVariable = new Variable('Some variable', 'John value for variable');
        $anotherJaneVariable = new Variable('Some different variable', 'Jane value for some different variable');
        $anotherJohnVariable = new Variable('Some different variable', 'John value for some different variable');

        $this->addRecipient($janeRecipient);
        $this->addRecipient($johnRecipient);
        $this->addLocalVariable($janeRecipient, $janeVariable);
        $this->addLocalVariable($johnRecipient, $johnVariable);
        $this->addLocalVariable($johnRecipient, $anotherJohnVariable);
        $this->addLocalVariable($janeRecipient, $anotherJaneVariable);

        $email = new \SendGrid\Email();
        $email->setFrom('yoda@jedi.com');
        $email->setFromName('Master Jedi Yoda');
        $email->addTo('janedoe@example.com', 'Jane Doe');
        $email->addTo('johndoe@example.com', 'John Doe');
        $email->setSubject('Example subject');
        $email->setTemplateId('example-template');
        $email->setSubstitutions([
            'Some variable' => ['Jane value for variable', 'John value for variable'],
            'Some different variable' => ['Jane value for some different variable', 'John value for some different variable']
        ]);

        $sendGrid->send($email)->shouldBeCalled();

        $this->send('Example subject', 'example-template')->shouldReturn(true);
    }
}
