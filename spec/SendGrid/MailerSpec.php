<?php namespace spec\DeSmart\Mailer\SendGrid;

use DeSmart\Mailer\Header;
use DeSmart\Mailer\Job;
use DeSmart\Mailer\Recipient;
use DeSmart\Mailer\RecipientType;
use DeSmart\Mailer\Variable;
use Illuminate\Contracts\Filesystem\Filesystem as FilesystemInterface;
use Illuminate\Contracts\Queue\Queue;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class MailerSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(\DeSmart\Mailer\SendGrid\Mailer::class);
    }

    public function let(\SendGrid $sendGrid, Queue $queue, FilesystemInterface $storage)
    {
        $this->beConstructedWith($sendGrid, $queue, $storage, 'yoda@jedi.com', 'Master Jedi Yoda');
    }

    public function it_should_send_email(\SendGrid $sendGrid)
    {
        $recipient = new Recipient('Jane Doe', 'janedoe@example.com');

        $this->addRecipient($recipient);

        $email = new \SendGrid\Email();
        $email->setFrom('yoda@jedi.com');
        $email->setFromName('Master Jedi Yoda');
        $email->addSmtpapiTo('janedoe@example.com', 'Jane Doe');
        $email->setSubject('Example subject');
        $email->setHtml(' ');
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
        $email->addSmtpapiTo('janedoe@example.com', 'Jane Doe');
        $email->addSmtpapiTo('johndoe@example.com', 'John Doe');
        $email->addSmtpapiTo('brucewayne@gotham.com', 'Bruce Wayne');
        $email->addSmtpapiTo('clarkkent@dailyplanet.com', 'Clark Kent');
        $email->setSubject('Example subject');
        $email->setHtml(' ');
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
        $email->addSmtpapiTo('janedoe@example.com', 'Jane Doe');
        $email->setSubject('Example subject');
        $email->setHtml(' ');
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
        $email->addSmtpapiTo('janedoe@example.com', 'Jane Doe');
        $email->setSubject('Example subject');
        $email->setHtml(' ');
        $email->setTemplateId('example-template');
        $email->setSections([
            'Some variable_SECTION' => 'Some variable value',
            'Some different variable_SECTION' => 'Some different variable value'
        ]);
        $email->setSubstitutions([
            'Some variable' => ['Some variable_SECTION'],
            'Some different variable' => ['Some different variable_SECTION']
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
        $email->addSmtpapiTo('janedoe@example.com', 'Jane Doe');
        $email->addSmtpapiTo('johndoe@example.com', 'John Doe');
        $email->setSubject('Example subject');
        $email->setHtml(' ');
        $email->setTemplateId('example-template');
        $email->setSubstitutions([
            'Some variable' => ['Jane value for variable', 'John value for variable'],
            'Some different variable' => ['Jane value for some different variable', 'John value for some different variable']
        ]);

        $sendGrid->send($email)->shouldBeCalled();

        $this->send('Example subject', 'example-template')->shouldReturn(true);
    }

    public function it_gets_mailer_data()
    {
        $recipient = new Recipient('Jane Doe', 'janedoe@example.com');
        $variableOne = new Variable('global_one', 'Example');
        $variableTwo = new Variable('global_two', 'Another example');
        $variableThree = new Variable('local', 'Yet another example');

        $this->setSubject('Example subject');
        $this->setTemplate('example template');
        $this->addRecipient($recipient);
        $this->addGlobalVariable($variableOne);
        $this->addGlobalVariable($variableTwo);
        $this->addLocalVariable($recipient, $variableThree);

        $this->getData()->shouldReturn([
            'from_name' => 'Master Jedi Yoda',
            'from_email' => 'yoda@jedi.com',
            'subject' => 'Example subject',
            'template' => 'example template',
            'recipients' => [
                'janedoe@example.com' => $recipient
            ],
            'global_vars' => [
                'global_one' => 'Example',
                'global_two' => 'Another example'
            ],
            'local_vars' => [
                'janedoe@example.com' => [$variableThree]
            ],
            'headers' => [],
            'reply_to' => null,
            'attachments' => [],
        ]);
    }

    public function it_clears_mailer_data()
    {
        $recipient = new Recipient('Jane Doe', 'janedoe@example.com');
        $variableOne = new Variable('global_one', 'Example');
        $variableTwo = new Variable('global_two', 'Another example');
        $variableThree = new Variable('local', 'Yet another example');

        $this->setSubject('Example subject');
        $this->setTemplate('example template');
        $this->addRecipient($recipient);
        $this->addGlobalVariable($variableOne);
        $this->addGlobalVariable($variableTwo);
        $this->addLocalVariable($recipient, $variableThree);

        $this->clear();

        $this->getData()->shouldReturn([
            'from_name' => 'Master Jedi Yoda',
            'from_email' => 'yoda@jedi.com',
            'subject' => 'Example subject',
            'template' => 'example template',
            'recipients' => [],
            'global_vars' => [],
            'local_vars' => [],
            'headers' => [],
            'reply_to' => null,
            'attachments' => [],
        ]);
    }

    public function it_sets_mailer_data(\SendGrid $sendGrid)
    {
        $this->setData([
            'from_name' => 'Master Jedi Yoda',
            'from_email' => 'yoda@jedi.com',
            'subject' => 'Example subject',
            'template' => 'example template',
            'recipients' => [
                'janedoe@example.com' => new Recipient('Jane Doe', 'janedoe@example.com')
            ],
            'global_vars' => [
                'global_one' => 'Example',
                'global_two' => 'Another example'
            ],
            'local_vars' => [
                'janedoe@example.com' => [new Variable('local', 'Yet another example')]
            ],
            'headers' => [],
            'reply_to' => null,
            'attachments' => [],
        ]);

        $email = new \SendGrid\Email();
        $email->setFrom('yoda@jedi.com');
        $email->setFromName('Master Jedi Yoda');
        $email->addSmtpapiTo('janedoe@example.com', 'Jane Doe');
        $email->setSubject('Example subject');
        $email->setHtml(' ');
        $email->setTemplateId('example template');
        $email->setSections([
            'global_one_SECTION' => 'Example',
            'global_two_SECTION' => 'Another example'
        ]);
        $email->setSubstitutions([
            'local' => ['Yet another example'],
            'global_one' => ['global_one_SECTION'],
            'global_two' => ['global_two_SECTION']
        ]);

        $sendGrid->send($email)->shouldBeCalled();

        $this->send('Example subject', 'example template')->shouldReturn(true);
    }

    public function it_pushes_mail_to_queue(Queue $queue)
    {
        $recipient = new Recipient('Jane Doe', 'janedoe@example.com');
        $variableOne = new Variable('global_one', 'Example');
        $variableTwo = new Variable('global_two', 'Another example');
        $variableThree = new Variable('local', 'Yet another example');

        $data = [
            'from_name' => 'Master Jedi Yoda',
            'from_email' => 'yoda@jedi.com',
            'subject' => 'Example subject',
            'template' => 'example template',
            'recipients' => [
                'janedoe@example.com' => $recipient
            ],
            'global_vars' => [
                'global_one' => 'Example',
                'global_two' => 'Another example'
            ],
            'local_vars' => [
                'janedoe@example.com' => [$variableThree]
            ],
            'headers' => [],
            'reply_to' => null,
            'attachments' => [],
        ];

        $this->setSubject('Example subject');
        $this->setTemplate('example template');
        $this->addRecipient($recipient);
        $this->addGlobalVariable($variableOne);
        $this->addGlobalVariable($variableTwo);
        $this->addLocalVariable($recipient, $variableThree);

        $job = new Job($data);
        $queue->pushOn('sendgrid', $job)->shouldBeCalled();

        $this->queue('sendgrid')->shouldReturn(true);
    }
}
