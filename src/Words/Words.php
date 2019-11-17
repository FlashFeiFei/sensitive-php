<?php

namespace Sensitive\Words;


class Words
{
    /**
     * @var Words
     */
    private static $install = null;
    /**
     * @var array
     */
    private $words;

    public static function getInstall(): Words
    {
        if (!(self::$install instanceof Words)) {
            self::$install = new self;
        }
        return self::$install;
    }

    private function __construct()
    {
        $this->words = array();
    }

    public function reloadWords(array $wordlist)
    {
        unset($this->words);
        $this->words = array();
        foreach ($wordlist as $item) {
            $this->addWord($item);
        }
    }

    public function addWord(string $word)
    {
        array_push($this->words, $word);
    }

    public function foreachWords(\Closure $closure)
    {
        foreach ($this->words as $word) {
            $closure($word);
        }
    }
}