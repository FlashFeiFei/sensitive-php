<?php

namespace Sensitive\Strpos;


use Sensitive\Words\SensitiveWordsResult;
use Sensitive\Words\Words;

class SensitiveWordFileter
{
    /**
     * @var SensitiveWordFileter
     */
    private static $install = null;

    private static $flag_arr = [
        '？', '！', '￥', '（', '）', '：',
        '‘', '’', '“', '”', '《', '》',
        '，', '…', '。', '、', 'nbsp', '】',
        '【', '～', '#', '$', '^', '%', '@', '!',
        '*', '-' . '_', '+', '='
    ];

    /**
     * @var Words
     */
    private $words;

    /**
     * @return SensitiveWordFileter
     */
    public static function getInstall(): SensitiveWordFileter
    {
        if (!(self::$install instanceof SensitiveWordFileter)) {
            self::$install = new self;
        }
        return self::$install;
    }

    public function setWords(Words $words)
    {
        $this->words = $words;
    }

    private function __construct()
    {
    }

    /**
     * 检测是否有铭感词
     * @param string $txt
     * @return SensitiveWordsResult
     */
    public function sensitiveWordFilter(string $txt): SensitiveWordsResult
    {
        // 提取中文部分，防止其中夹杂英语等
        preg_match_all("/[\x{4e00}-\x{9fa5}]+/u", $txt, $match);
        $chineseStr = implode('', $match[0]);
        $englishStr = strtolower(preg_replace("/[^A-Za-z0-9\.\-]/", " ", $txt));

        $contentFilter = preg_replace('/\s/', '', preg_replace("/[[:punct:]]/", '',
            strip_tags(html_entity_decode(str_replace(self::$flag_arr, '', $txt), ENT_QUOTES, 'UTF-8'))));

        $resultArray = [];
        // 全匹配过滤,去除特殊字符后过滤中文及提取中文部分
        $this->words->foreachWords(function ($word) use ($txt, $contentFilter, $chineseStr, $englishStr, &$resultArray) {

            if (strpos($txt, $word) !== false || strpos($contentFilter, $word) !== false || strpos($chineseStr, $word) !== false
                || strpos($englishStr, $word) !== false) {

                $resultArray[] = $word;
            }
        });
        $sensitiveWord = new SensitiveWordsResult($resultArray);
        return $sensitiveWord;
    }
}