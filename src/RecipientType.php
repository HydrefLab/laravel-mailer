<?php namespace DeSmart\Mailer;

class RecipientType
{
    const TO_TYPE = 'to';
    const CC_TYPE = 'cc';
    const BCC_TYPE = 'bcc';

    /** @var string */
    protected $type;

    /**
     * @param string $type
     */
    private function __construct($type)
    {
        $this->type = $type;
    }

    /**
     * @return RecipientType
     */
    public static function to()
    {
        return new self(self::TO_TYPE);
    }

    /**
     * @return RecipientType
     */
    public static function cc()
    {
        return new self(self::CC_TYPE);
    }

    /**
     * @return RecipientType
     */
    public static function bcc()
    {
        return new self(self::BCC_TYPE);
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param RecipientType $type
     * @return bool
     */
    public function equals(RecipientType $type)
    {
        return $this->getType() === $type->getType();
    }
}