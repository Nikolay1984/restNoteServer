<?php
/**
 * This class neutralizes the potential hazard to BD at the request of the user.
 */

namespace http;


class Gatekeeper
{
    /**
     * Neutralizes potential hazard in the request URN.
     * @param string $str
     * @return string
     */
    public function secureString($str)
    {

        return htmlspecialchars(stripslashes(trim($str)));
    }

    /**
     * Neutralizes potential hazard in the request body.
     * @param array $body
     * @return mixed
     */
    public function secureBody($body){

        foreach ($body->records as $record){

           $record->text = $this->secureString($record->text);
        }

        $body->name = $this->secureString( $body->name);

        return $body;

    }
}