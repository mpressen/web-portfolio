<?php

namespace App\Mail;

/**
 *
 */
class Mail
{
    private $to = null;
    private $subject = null;
    private $message = null;
    private $headers = null;

    function __construct(array $kargs = null)
    {
        if (!empty($kargs))
        {
            if (array_key_exists("to", $kargs))
                $this->to = $kargs["to"];
            if (array_key_exists("subject", $kargs))
                $this->subject = $kargs["subject"];
            if (array_key_exists("message", $kargs))
                $this->message = $kargs["message"];
            if (array_key_exists("headers", $kargs))
                $this->headers = $kargs["headers"];
            else
                $this->headers = "Content-Type: text/plain; charset=UTF-8";
            return($this);
        }
    }

    function send()
    {
        if (mail($this->to, $this->subject, $this->message, $this->headers))
            return (true);
        return(false);
    }
    /**
     * Get the value of To
     *
     * @return mixed
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * Set the value of To
     *
     * @param mixed to
     *
     * @return self
     */
    public function setTo($to)
    {
        $this->to = $to;

        return $this;
    }

    /**
     * Get the value of Subject
     *
     * @return mixed
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set the value of Subject
     *
     * @param mixed subject
     *
     * @return self
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Get the value of Message
     *
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set the value of Message
     *
     * @param mixed message
     *
     * @return self
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get the value of Headers
     *
     * @return mixed
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Set the value of Headers
     *
     * @param mixed headers
     *
     * @return self
     */
    public function setHeaders($headers)
    {
        $this->headers = $headers;

        return $this;
    }

}
