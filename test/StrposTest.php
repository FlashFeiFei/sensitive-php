<?php

use PHPUnit\Framework\TestCase;
use Sensitive\Words\Words;
use Sensitive\Strpos\SensitiveWordFileter;

class StrposTest extends TestCase
{
    /**
     * 测试添加铭感词
     *
     */
    public function testStrPos()
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
        $swf = SensitiveWordFileter::getInstall();
        foreach ($data as $item) {
            $words->addWord($item);
        }

        $swf->setWords($words);
        $result = $swf->sensitiveWordFilter('白@@粉@@人@白@粉@,危险www.baidu.com');
        var_dump($result->getResult());
    }
}