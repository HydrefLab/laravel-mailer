<?php namespace DeSmart\Mailer;

class Recipient
{
    /** @var string */
    protected $name;
    /** @var string */
    protected $email;
    /** @var RecipientType */
    protected $type;

    /**
     * @param $name
     * @param $email
     * @param RecipientType|null $type
     */
    public function __construct($name, $email, RecipientType $type = null)
    {
        $this->name = $name;
        $this->email = $email;
        $this->type = (null !== $type) ? $type : RecipientType::to();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return RecipientType
     */
    public function getType()
    {
        return $this->type;
    }
}