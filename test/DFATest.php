<?php

use PHPUnit\Framework\TestCase;
use Sensitive\DFA\DFA;
use Sensitive\Words\Words;

class DFATest extends TestCase
{
    /**
     * 测试添加铭感词
     * @return DFA
     */
    public function testAddWord()
    {

        $data[] = '白粉';
        $data[] = '白粉人';
        $data[] = '白粉人嫩';
        $data[] = '我是一个白粉人的后代';
        $data[] = '不该大';
        $data[] = 'fuck you';
        $data[] = 'www.baidu.com';
        $this->assertNotEmpty($data, '铭感词库不能为空');
        $words = Words::getInstall();
        $wordObj = DFA::getInstance();
        foreach ($data as $item) {
            $words->addWord($item);
        }
        $wordObj->reloadSensitiveWords($words);

        return $wordObj;
    }

    /**
     * 测试文本中是否匹配到铭感词
     * @depends testAddWord
     */
    public function testTextSearchWord(DFA $wordObj)
    {
        $txt = '白粉啊,白粉人，我不该大啊 fuck you';
        $this->assertNotEmpty($txt, '文本匹配不能为空');
        $result = $wordObj->searchWords($txt);
        var_dump($result->getResult());
    }

    /**
     * @depends testAddWord
     */
    public function testReloadWords(DFA $wordObj)
    {
        $data[] = '不该大';
        $data[] = 'fuck you';
        $words = Words::getInstall();
        $words->reloadWords($data);
        $wordObj->reloadSensitiveWords($words);
        $txt = '白粉啊,白粉人，我不该大啊 fuck you';
        $this->assertNotEmpty($txt, '文本匹配不能为空');
        $result = $wordObj->searchWords($txt);
        var_dump($result->getResult());
    }
}