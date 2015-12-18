<?php namespace spec\DeSmart\Mailer\Mandrill;

use DeSmart\Mailer\Attachment;
use DeSmart\Mailer\Recipient;
use DeSmart\Mailer\Variable;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class MailerSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(\DeSmart\Mailer\Mandrill\Mailer::class);
    }

    public function let(\Weblee\Mandrill\Mail $mandrill)
    {
        $this->beConstructedWith($mandrill, 'johndoe@example.com', 'John Doe');
    }

    public function it_should_implement_mailer_interface()
    {
        $this->shouldImplement(\DeSmart\Mailer\MailerInterface::class);
    }

    public function it_should_send_email(
        \Weblee\Mandrill\Mail $mandrill,
        \Mandrill_Messages $mandrillMessages
    ) {
        $recipient = new Recipient('Jane Doe', 'janedoe@example.com');

        $this->addRecipient($recipient);

        $mandrill->messages()->willReturn($mandrillMessages);
        $mandrillMessages->sendTemplate('example-template', [], [
            'subject' => 'Example subject',
            'from_email' => 'johndoe@example.com',
            'from_name' => 'John Doe',
            'to' => [
                [
                    'email' => 'janedoe@example.com',
                    'name' => 'Jane Doe',
                    'type' => 'to'
                ]
            ],
            'merge_vars' => [],
            'global_merge_vars' => [],
            'attachments' => [],
        ])->shouldBeCalled();

        $this->send('Example subject', 'example-template')->shouldReturn(true);
    }

    public function it_should_send_email_with_global_vars(
        \Weblee\Mandrill\Mail $mandrill,
        \Mandrill_Messages $mandrillMessages
    ) {
        $recipient = new Recipient('Jane Doe', 'janedoe@example.com');
        $variableOne = new Variable('example', 'Example');
        $variableTwo = new Variable('secondexample', 'Second example');

        $this->addRecipient($recipient);
        $this->addGlobalVariable($variableOne);
        $this->addGlobalVariable($variableTwo);

        $mandrill->messages()->willReturn($mandrillMessages);
        $mandrillMessages->sendTemplate('example-template', [], [
            'subject' => 'Example subject',
            'from_email' => 'johndoe@example.com',
            'from_name' => 'John Doe',
            'to' => [
                [
                    'email' => 'janedoe@example.com',
                    'name' => 'Jane Doe',
                    'type' => 'to'
                ]
            ],
            'merge_vars' => [],
            'global_merge_vars' => [
                [
                    'name' => 'EXAMPLE',
                    'content' => 'Example'
                ],
                [
                    'name' => 'SECONDEXAMPLE',
                    'content' => 'Second example'
                ]
            ],
            'attachments' => [],
        ])->shouldBeCalled();

        $this->send('Example subject', 'example-template')->shouldReturn(true);
    }

    public function it_should_send_email_with_local_vars(
        \Weblee\Mandrill\Mail $mandrill,
        \Mandrill_Messages $mandrillMessages
    ) {
        $recipientOne = new Recipient('Jane Doe', 'janedoe@example.com');
        $recipientTwo = new Recipient('Will Smith', 'willsmith@example.com');

        $variableOne = new Variable('example', 'Example');
        $variableTwo = new Variable('secondexample', 'Second example');
        $variableThree = new Variable('secondexample', 'Second example different value');

        $this->addRecipient($recipientOne);
        $this->addRecipient($recipientTwo);

        $this->addLocalVariable($recipientOne, $variableOne);
        $this->addLocalVariable($recipientOne, $variableTwo);
        $this->addLocalVariable($recipientTwo, $variableOne);
        $this->addLocalVariable($recipientTwo, $variableThree);

        $mandrill->messages()->willReturn($mandrillMessages);
        $mandrillMessages->sendTemplate('example-template', [], [
            'subject' => 'Example subject',
            'from_email' => 'johndoe@example.com',
            'from_name' => 'John Doe',
            'to' => [
                [
                    'email' => 'janedoe@example.com',
                    'name' => 'Jane Doe',
                    'type' => 'to'
                ],
                [
                    'email' => 'willsmith@example.com',
                    'name' => 'Will Smith',
                    'type' => 'to'
                ]
            ],
            'merge_vars' => [
                [
                    'rcpt' => 'janedoe@example.com',
                    'vars' => [
                        [
                            'name' => 'EXAMPLE',
                            'content' => 'Example'
                        ],
                        [
                            'name' => 'SECONDEXAMPLE',
                            'content' => 'Second example'
                        ]
                    ]
                ],
                [
                    'rcpt' => 'willsmith@example.com',
                    'vars' => [
                        [
                            'name' => 'EXAMPLE',
                            'content' => 'Example'
                        ],
                        [
                            'name' => 'SECONDEXAMPLE',
                            'content' => 'Second example different value'
                        ]
                    ]
                ]
            ],
            'global_merge_vars' => [],
            'attachments' => [],
        ])->shouldBeCalled();

        $this->send('Example subject', 'example-template')->shouldReturn(true);
    }

    public function it_should_send_email_with_attachments(
        \Weblee\Mandrill\Mail $mandrill,
        \Mandrill_Messages $mandrillMessages
    ) {
        $recipient = new Recipient('Jane Doe', 'janedoe@example.com');
        $attachment = new Attachment('text/csv', 'test.csv', 'example;test;');

        $this->addRecipient($recipient);
        $this->addAttachment($attachment);

        $mandrill->messages()->willReturn($mandrillMessages);
        $mandrillMessages->sendTemplate('example-template', [], [
            'subject' => 'Example subject',
            'from_email' => 'johndoe@example.com',
            'from_name' => 'John Doe',
            'to' => [
                [
                    'email' => 'janedoe@example.com',
                    'name' => 'Jane Doe',
                    'type' => 'to'
                ],
            ],
            'merge_vars' => [],
            'global_merge_vars' => [],
            'attachments' => [
                [
                    'type' => 'text/csv',
                    'name' => 'test.csv',
                    'content' => 'ZXhhbXBsZTt0ZXN0Ow=='
                ],
            ],
        ])->shouldBeCalled();

        $this->send('Example subject', 'example-template')->shouldReturn(true);
    }

    public function it_should_not_send_email(
        \Weblee\Mandrill\Mail $mandrill,
        \Mandrill_Messages $mandrillMessages
    ) {
        $recipient = new Recipient('Jane Doe', 'janedoe@example.com');

        $this->addRecipient($recipient);

        $mandrill->messages()->willReturn($mandrillMessages);
        $mandrillMessages->sendTemplate('example-template', [], [
            'subject' => 'Example subject',
            'from_email' => 'johndoe@example.com',
            'from_name' => 'John Doe',
            'to' => [
                [
                    'email' => 'janedoe@example.com',
                    'name' => 'Jane Doe',
                    'type' => 'to'
                ]
            ],
            'merge_vars' => [],
            'global_merge_vars' => [],
            'attachments' => [],
        ])->shouldBeCalled()->willThrow(new \Mandrill_Error);

        $this->send('Example subject', 'example-template')->shouldReturn(false);
    }

    public function it_should_prevent_local_variable_duplicates(
        \Weblee\Mandrill\Mail $mandrill,
        \Mandrill_Messages $mandrillMessages
    ) {
        $recipient = new Recipient('Jane Doe', 'janedoe@example.com');

        $variableOne = new Variable('example', 'Example');
        $variableTwo = new Variable('example', 'Changed example');

        $this->addRecipient($recipient);

        $this->addLocalVariable($recipient, $variableOne);
        $this->addLocalVariable($recipient, $variableTwo);

        $mandrill->messages()->willReturn($mandrillMessages);
        $mandrillMessages->sendTemplate('example-template', [], [
            'subject' => 'Example subject',
            'from_email' => 'johndoe@example.com',
            'from_name' => 'John Doe',
            'to' => [
                [
                    'email' => 'janedoe@example.com',
                    'name' => 'Jane Doe',
                    'type' => 'to'
                ]
            ],
            'merge_vars' => [
                [
                    'rcpt' => 'janedoe@example.com',
                    'vars' => [
                        [
                            'name' => 'EXAMPLE',
                            'content' => 'Changed example'
                        ]
                    ]
                ]
            ],
            'global_merge_vars' => [],
            'attachments' => [],
        ])->shouldBeCalled();

        $this->send('Example subject', 'example-template')->shouldReturn(true);
    }

    public function it_should_prevent_global_variable_duplicates(
        \Weblee\Mandrill\Mail $mandrill,
        \Mandrill_Messages $mandrillMessages
    ) {
        $recipient = new Recipient('Jane Doe', 'janedoe@example.com');

        $variableOne = new Variable('example', 'Example');
        $variableTwo = new Variable('example', 'Changed example');

        $this->addRecipient($recipient);

        $this->addGlobalVariable($variableOne);
        $this->addGlobalVariable($variableTwo);

        $mandrill->messages()->willReturn($mandrillMessages);
        $mandrillMessages->sendTemplate('example-template', [], [
            'subject' => 'Example subject',
            'from_email' => 'johndoe@example.com',
            'from_name' => 'John Doe',
            'to' => [
                [
                    'email' => 'janedoe@example.com',
                    'name' => 'Jane Doe',
                    'type' => 'to'
                ]
            ],
            'merge_vars' => [],
            'global_merge_vars' => [
                [
                    'name' => 'EXAMPLE',
                    'content' => 'Changed example'
                ]
            ],
            'attachments' => [],
        ])->shouldBeCalled();

        $this->send('Example subject', 'example-template')->shouldReturn(true);
    }
}
