<?php

namespace Sensitive\DFA;


use Sensitive\Words\SensitiveWordsResult;
use Sensitive\Words\Words;

class DFA
{
    const REPLACE_SYMBOL = '*';

    /**
     * @var DFA
     */
    private static $instance = null;

    /**
     * 敏感词树
     * @var Node
     */
    private $sensitiveWordTree;

    /**
     *
     * @return DFA
     */
    public static function getInstance(): DFA
    {
        if (!(self::$instance instanceof DFA)) {
            $instance = new self();
            self::$instance = $instance;
        }
        return self::$instance;
    }

    private function __construct()
    {

    }

    public function reloadSensitiveWords(Words $words)
    {
        unset($this->sensitiveWordTree);
        $this->sensitiveWordTree = new Node('');
        $words->foreachWords(function ($word) {
            $this->addSensitiveWords($word);
        });
    }

    private function addSensitiveWords(string $words)
    {
        $len = mb_strlen($words);
        $tree = $this->sensitiveWordTree;
        for ($i = 0; $i < $len; $i++) {
            $word = mb_substr($words, $i, 1);
            if (!isset($tree->children[$word])) {
                $newNode = new Node($word);
                $tree->children[$word] = $newNode;
            }
            $tree = $tree->children[$word];
        }
        $tree->isEndingChar = true;
    }

    /**
     * @param string $txt
     * @param int $index
     * @param int $txtLength
     * @return array
     */
    private function checkWordTree(string $txt, int $index, int $txtLength): array
    {
        $tree = $this->sensitiveWordTree;
        $wordLength = 0; //敏感字符个数
        $wordLengthArray = [];
        $flag = false;
        for ($i = $index; $i < $txtLength; $i++) {
            $txtWord = mb_substr($txt, $i, 1); //截取需要检测的文本，和词库进行比对

            //如果搜索字不存在词库中直接停止循环。
            if (!isset($tree->children[$txtWord])) {
                break;
            }
            $wordLength++;
            /**
             * @var Node $tree
             */
            $tree = $tree->children[$txtWord];
            if ($tree->isEndingChar == true) {
                //匹配到了铭感词
                $flag = true;
                $wordLengthArray[] = $wordLength;
            }
        }
        //没有检测到敏感词，初始化字符长度
        $flag || $wordLength = 0;
        return $wordLengthArray;
    }

    /**
     * @param string $txt
     * @return SensitiveWordsResult
     */
    public function searchWords(string $txt): SensitiveWordsResult
    {
        $txt = trim($txt);
        $txtLength = mb_strlen($txt);
        $wordList = [];
        for ($i = 0; $i < $txtLength; $i++) {
            //检查字符是否存在敏感词树内,传入检查文本、搜索开始位置、文本长度
            $lenList = $this->checkWordTree($txt, $i, $txtLength);
            foreach ($lenList as $key => $len) {
                //搜索出来的敏感词
                $word = mb_substr($txt, $i, $len);
//                $wordList[$word] = str_repeat(self::REPLACE_SYMBOL, $len);   //存在敏感词，进行字符替换。
                $wordList[] = $word;
                if (($key + 1) == count($lenList)) {
                    $i += $len;
                }
            }
        }
        $sensitiveWordsResult = new SensitiveWordsResult($wordList);
        return $sensitiveWordsResult;
    }

}