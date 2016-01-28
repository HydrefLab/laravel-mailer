<?php namespace spec\DeSmart\Mailer;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class HeaderSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(\DeSmart\Mailer\Header::class);
    }

    public function let()
    {
        $this->beConstructedWith('Reply-To', 'reply-to@example.com');
    }

    public function it_should_get_name()
    {
        $this->getName()->shouldReturn('Reply-To');
    }

    public function it_should_get_value()
    {
        $this->getValue()->shouldReturn('reply-to@example.com');
    }
}
