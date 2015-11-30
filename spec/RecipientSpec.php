<?php namespace spec\DeSmart\Mailer;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RecipientSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(\DeSmart\Mailer\Recipient::class);
    }

    public function let()
    {
        $this->beConstructedWith('John Doe', 'johndoe@example.com');
    }

    public function it_should_get_name()
    {

        $this->getName()->shouldReturn('John Doe');
    }

    public function it_should_get_email()
    {
        $this->getEmail()->shouldReturn('johndoe@example.com');
    }
}
