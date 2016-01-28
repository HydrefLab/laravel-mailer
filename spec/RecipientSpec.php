<?php namespace spec\DeSmart\Mailer;

use DeSmart\Mailer\RecipientType;
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

    public function it_should_get_default_recipient_type()
    {
        $this->getType()->equals(RecipientType::to())->shouldReturn(true);
    }

    public function it_should_get_recipient_type()
    {
        $this->beConstructedWith('John Doe', 'johndoe@example.com', RecipientType::bcc());

        $this->getType()->equals(RecipientType::to())->shouldReturn(false);
        $this->getType()->equals(RecipientType::bcc())->shouldReturn(true);
    }
}
