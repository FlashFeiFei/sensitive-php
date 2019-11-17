<?php

namespace Sensitive\DFA;


class Node
{
    public $data; //节点字符
    public $children = []; //存放子节点
    public $isEndingChar = false;


    public function __construct($data)
    {
        $this->data = $data;
    }
}