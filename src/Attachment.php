<?php namespace DeSmart\Mailer;

class Attachment
{
    /** @var string */
    protected $type;
    /** @var string */
    protected $name;
    /** @var string */
    protected $content;

    /**
     * @param string $type
     * @param string $name
     * @param string $content
     */
    public function __construct($type, $name, $content)
    {
        $this->type = $type;
        $this->name = $name;
        $this->content = $content;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
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
    public function getContent()
    {
        return $this->content;
    }
}
