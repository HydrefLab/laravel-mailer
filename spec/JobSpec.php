<?php  namespace spec\DeSmart\Mailer;

use DeSmart\Mailer\MailerInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class JobSpec extends ObjectBehavior
{
    protected $data = ['some data array'];

    public function it_is_initializable()
    {
        $this->shouldHaveType(\DeSmart\Mailer\Job::class);
    }

    public function let()
    {
        $this->beConstructedWith($this->data);
    }

    public function it_handles_queue_job(MailerInterface $mailer
    )
    {
        $mailer->setData($this->data)->shouldBeCalled();
        $mailer->send()->shouldBeCalled();

        $this->handle($mailer);
    }
}
