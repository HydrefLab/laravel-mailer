<?php namespace spec\DeSmart\Mailer;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class AttachmentSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(\DeSmart\Mailer\Attachment::class);
    }

    public function let()
    {
        $this->beConstructedWith('text/csv', 'test.csv', 'example;test;');
    }

    public function it_should_get_type()
    {

        $this->getType()->shouldReturn('text/csv');
    }

    public function it_should_get_name()
    {
        $this->getName()->shouldReturn('test.csv');
    }

    public function it_should_get_content()
    {
        $this->getContent()->shouldReturn('example;test;');
    }
}
