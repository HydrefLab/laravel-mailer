<?php  namespace spec\DeSmart\Mailer;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class VariableSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(\DeSmart\Mailer\Variable::class);
    }

    public function let()
    {
        $this->beConstructedWith('example', 'Example');
    }

    public function it_should_get_name()
    {

        $this->getName()->shouldReturn('example');
    }

    public function it_should_get_value()
    {
        $this->getValue()->shouldReturn('Example');
    }
}
