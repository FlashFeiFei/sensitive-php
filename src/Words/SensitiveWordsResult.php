<?php

namespace Sensitive\Words;


class SensitiveWordsResult
{
    /**
     * @var array
     */
    private $sensitiveWords;

    public function __construct(array $msglist = [])
    {
        $this->sensitiveWords = [];

        foreach ($msglist as $item) {
            if (mb_strlen($item) > 0) {
                array_push($this->sensitiveWords, $item);
            }
        }
    }
    

    /**
     * @return array
     */
    public function getResult(): array
    {
        return $this->sensitiveWords;
    }
}