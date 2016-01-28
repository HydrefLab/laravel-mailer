<?php namespace spec\DeSmart\Mailer;

use DeSmart\Mailer\RecipientType;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RecipientTypeSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(\DeSmart\Mailer\RecipientType::class);
    }

    public function it_returns_to_recipient_type_when_constructed_with_named_constructor()
    {
        $this->beConstructedThrough('to');
        $this->getType()->shouldReturn(RecipientType::TO_TYPE);
    }

    public function it_returns_cc_recipient_type_when_constructed_with_named_constructor()
    {
        $this->beConstructedThrough('cc');
        $this->getType()->shouldReturn(RecipientType::CC_TYPE);
    }

    public function it_returns_bcc_recipient_type_when_constructed_with_named_constructor()
    {
        $this->beConstructedThrough('bcc');
        $this->getType()->shouldReturn(RecipientType::BCC_TYPE);
    }

    public function it_checks_if_recipient_types_are_equal()
    {
        $this->beConstructedThrough('to');

        $this->equals(RecipientType::to())->shouldReturn(true);
        $this->equals(RecipientType::cc())->shouldReturn(false);
        $this->equals(RecipientType::bcc())->shouldReturn(false);
    }
}
