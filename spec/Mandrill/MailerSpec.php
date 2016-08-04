<?php namespace spec\DeSmart\Mailer\Mandrill;

use DeSmart\Mailer\Attachment;
use DeSmart\Mailer\Header;
use DeSmart\Mailer\Job;
use DeSmart\Mailer\Recipient;
use DeSmart\Mailer\Variable;
use Illuminate\Contracts\Queue\Queue;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class MailerSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(\DeSmart\Mailer\Mandrill\Mailer::class);
    }

    public function let(\Weblee\Mandrill\Mail $mandrill, Queue $queue)
    {
        $this->beConstructedWith($mandrill, $queue, 'johndoe@example.com', 'John Doe');
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
            'headers' => [],
            'merge_vars' => [],
            'global_merge_vars' => [],
            'attachments' => [],
        ])->shouldBeCalled();

        $this->send('Example subject', 'example-template')->shouldReturn(true);
    }

    public function it_should_send_email_with_reply_to(
        \Weblee\Mandrill\Mail $mandrill,
        \Mandrill_Messages $mandrillMessages
    ) {
        $recipient = new Recipient('Jane Doe', 'janedoe@example.com');

        $this->addRecipient($recipient);
        $this->setReplyTo('reply-to@example.com');

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
            'headers' => [
                'Reply-To' => 'reply-to@example.com'
            ],
            'merge_vars' => [],
            'global_merge_vars' => [],
            'attachments' => [],
        ])->shouldBeCalled();

        $this->send('Example subject', 'example-template')->shouldReturn(true);
    }

    public function it_should_send_email_with_additional_headers(
        \Weblee\Mandrill\Mail $mandrill,
        \Mandrill_Messages $mandrillMessages
    ) {
        $recipient = new Recipient('Jane Doe', 'janedoe@example.com');
        $header = new Header('Reply-To', 'reply-to@example.com');
        $anotherHeader = new Header('Some header', 'Some value');

        $this->addRecipient($recipient);
        $this->addHeader($header);
        $this->addHeader($anotherHeader);

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
            'headers' => [
                'Reply-To' => 'reply-to@example.com',
                'Some header' => 'Some value'
            ],
            'merge_vars' => [],
            'global_merge_vars' => [],
            'attachments' => [],
        ])->shouldBeCalled();

        $this->send('Example subject', 'example-template')->shouldReturn(true);
    }

    public function it_should_prevent_header_duplicates(
        \Weblee\Mandrill\Mail $mandrill,
        \Mandrill_Messages $mandrillMessages
    ) {
        $recipient = new Recipient('Jane Doe', 'janedoe@example.com');
        $header = new Header('Reply-To', 'reply-to@example.com');
        $anotherHeader = new Header('Reply-To', 'another-reply-to@example.com');

        $this->addRecipient($recipient);
        $this->addHeader($header);
        $this->addHeader($anotherHeader);

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
            'headers' => [
                'Reply-To' => 'another-reply-to@example.com'
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
            'headers' => [],
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
            'headers' => [],
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
            'headers' => [],
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
            'headers' => [],
            'merge_vars' => [],
            'global_merge_vars' => [],
            'attachments' => [],
        ])->shouldBeCalled()->willThrow(new \Mandrill_Error);

        $this->shouldThrow(\Mandrill_Error::class)->during('send', ['Example subject', 'example-template']);
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
            'headers' => [],
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
            'headers' => [],
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

    public function it_gets_mailer_data()
    {
        $recipient = new Recipient('Jane Doe', 'janedoe@example.com');
        $variableOne = new Variable('global_one', 'Example');
        $variableTwo = new Variable('global_two', 'Another example');
        $variableThree = new Variable('local', 'Yet another example');
        $attachment = new Attachment('text/csv', 'test.csv', 'example;test;');

        $this->setSubject('Example subject');
        $this->setTemplate('example template');
        $this->addRecipient($recipient);
        $this->addGlobalVariable($variableOne);
        $this->addGlobalVariable($variableTwo);
        $this->addLocalVariable($recipient, $variableThree);
        $this->addAttachment($attachment);

        $this->getData()->shouldReturn([
            'from_name' => 'John Doe',
            'from_email' => 'johndoe@example.com',
            'subject' => 'Example subject',
            'template' => 'example template',
            'recipients' => [$recipient],
            'global_vars' => [
                'global_one' => [
                    'name' => 'GLOBAL_ONE',
                    'content' => 'Example'
                ],
                'global_two' => [
                    'name' => 'GLOBAL_TWO',
                    'content' => 'Another example'
                ]
            ],
            'local_vars' => [
                'janedoe@example.com' => [
                     'local' => [
                        'name' => 'LOCAL',
                        'content' => 'Yet another example'
                    ]
                ],
            ],
            'headers' => [],
            'attachments' => [
                [
                    'type' => 'text/csv',
                    'name' => 'test.csv',
                    'content' => 'ZXhhbXBsZTt0ZXN0Ow=='
                ]
            ],
        ]);
    }

    public function it_clears_mailer_data()
    {
        $recipient = new Recipient('Jane Doe', 'janedoe@example.com');
        $variableOne = new Variable('global_one', 'Example');
        $variableTwo = new Variable('global_two', 'Another example');
        $variableThree = new Variable('local', 'Yet another example');
        $attachment = new Attachment('text/csv', 'test.csv', 'example;test;');

        $this->setSubject('Example subject');
        $this->setTemplate('example template');
        $this->addRecipient($recipient);
        $this->addGlobalVariable($variableOne);
        $this->addGlobalVariable($variableTwo);
        $this->addLocalVariable($recipient, $variableThree);
        $this->addAttachment($attachment);

        $this->clear();

        $this->getData()->shouldReturn([
            'from_name' => 'John Doe',
            'from_email' => 'johndoe@example.com',
            'subject' => 'Example subject',
            'template' => 'example template',
            'recipients' => [],
            'global_vars' => [],
            'local_vars' => [],
            'headers' => [],
            'attachments' => [],
        ]);
    }

    public function it_sets_mailer_data(
        \Weblee\Mandrill\Mail $mandrill,
        \Mandrill_Messages $mandrillMessages
    ) {
        $this->setData([
            'from_name' => 'John Doe',
            'from_email' => 'johndoe@example.com',
            'subject' => 'Example subject',
            'template' => 'example template',
            'recipients' => [new Recipient('Jane Doe', 'janedoe@example.com')],
            'global_vars' => [
                'global_one' => [
                    'name' => 'GLOBAL_ONE',
                    'content' => 'Example'
                ],
                'global_two' => [
                    'name' => 'GLOBAL_TWO',
                    'content' => 'Another example'
                ]
            ],
            'local_vars' => [
                'janedoe@example.com' => [
                     'local' => [
                        'name' => 'LOCAL',
                        'content' => 'Yet another example'
                    ]
                ],
            ],
            'headers' => [],
            'attachments' => [
                [
                    'type' => 'text/csv',
                    'name' => 'test.csv',
                    'content' => 'ZXhhbXBsZTt0ZXN0Ow=='
                ]
            ]
        ]);

        $mandrill->messages()->willReturn($mandrillMessages);
        $mandrillMessages->sendTemplate('example template', [], [
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
            'headers' => [],
            'merge_vars' => [
                [
                    'rcpt' => 'janedoe@example.com',
                    'vars' => [
                        [
                            'name' => 'LOCAL',
                            'content' => 'Yet another example'
                        ]
                    ]
                ]
            ],
            'global_merge_vars' => [
                [
                    'name' => 'GLOBAL_ONE',
                    'content' => 'Example'
                ],
                [
                    'name' => 'GLOBAL_TWO',
                    'content' => 'Another example'
                ]
            ],
            'attachments' => [
                [
                    'type' => 'text/csv',
                    'name' => 'test.csv',
                    'content' => 'ZXhhbXBsZTt0ZXN0Ow=='
                ]
            ],
        ])->shouldBeCalled();

        $this->send()->shouldReturn(true);
    }

    public function it_pushes_mail_to_queue(Queue $queue)
    {
        $recipient = new Recipient('Jane Doe', 'janedoe@example.com');
        $variableOne = new Variable('global_one', 'Example');
        $variableTwo = new Variable('global_two', 'Another example');
        $variableThree = new Variable('local', 'Yet another example');
        $attachment = new Attachment('text/csv', 'test.csv', 'example;test;');

        $data = [
            'from_name' => 'John Doe',
            'from_email' => 'johndoe@example.com',
            'subject' => 'Example subject',
            'template' => 'example template',
            'recipients' => [new Recipient('Jane Doe', 'janedoe@example.com')],
            'global_vars' => [
                'global_one' => [
                    'name' => 'GLOBAL_ONE',
                    'content' => 'Example'
                ],
                'global_two' => [
                    'name' => 'GLOBAL_TWO',
                    'content' => 'Another example'
                ]
            ],
            'local_vars' => [
                'janedoe@example.com' => [
                    'local' => [
                        'name' => 'LOCAL',
                        'content' => 'Yet another example'
                    ]
                ],
            ],
            'headers' => [],
            'attachments' => [
                [
                    'type' => 'text/csv',
                    'name' => 'test.csv',
                    'content' => 'ZXhhbXBsZTt0ZXN0Ow=='
                ]
            ]
        ];

        $this->setSubject('Example subject');
        $this->setTemplate('example template');
        $this->addRecipient($recipient);
        $this->addGlobalVariable($variableOne);
        $this->addGlobalVariable($variableTwo);
        $this->addLocalVariable($recipient, $variableThree);
        $this->addAttachment($attachment);

        $job = new Job($data);
        $queue->pushOn('mandrill', $job)->shouldBeCalled();

        $this->queue('mandrill')->shouldReturn(true);
    }
}
