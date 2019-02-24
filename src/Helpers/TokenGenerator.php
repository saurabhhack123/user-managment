<?php

namespace App\Helpers;

class TokenGenerator
{
    /** @var string */
    private $appKey;

    /**
     * @param string $appKey
     */
    public function __construct(string $appKey)
    {
        $this->appKey = $appKey;
    }

    /**
     * To generate the random alphanumeric string of 250 characters
     *
     * @return string
     */
    public function generate()
    {
        $characters       = $this->appKey;
        $charactersLength = strlen($characters);
        $randomString     = '';
        for ($i = 0; $i < 250; ++$i) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }
}
