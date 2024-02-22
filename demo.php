<?php
/**
 * Created by PhpStorm.
 * User: day
 * Date: 2018/9/27
 * Time: 12:31
 */
require './ImageWriteStr.php';

$data = [
    'id'=>1,
    'title'=>'行情 | 某账户今日转移',
    'content'=>'据Searchain.io监测，0x97打头的ETH地址在9月24日收到49999ETH后，今日发生一万枚ETH的转移，通过其在币安的0xc0打头的入金地址归集进入币安0x87打头的地址。该笔资金来源于此前监测到的ETH创始交易巨鲸地址0x7d04d2edc058a1afc761d9c99ae4fc5c85d4c8a6。目前，该巨鲸地址还剩5.4万4ETH结余。',
    'time'=>1537947503
];

$weeks = array('星期日','星期一','星期二','星期三','星期四','星期五','星期六');

$week = $weeks[date('w', $data['time'])];

$date = date('Y-m-d  H:i', $data['time']);

//        echo $week; echo $date; exit;

$ImageWriteStr = new ImageWriteStr([
    'file' => './news.jpg',
    'strTtf' => './weiruanyahei.ttf', //相对路径报错就用绝对路径
    'newFile' => './',
]);

//字符串加入\n可以强制换行
$ImageWriteStr->writeStr([
    'week' => $week,
    'time' => $date,
    'title' => $data['title'],
    'content' => $data['content'],
],true);