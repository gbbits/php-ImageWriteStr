<?php
/**
 * PHP实现在图片中写入字符串
 * Class ImageWriteStr
 * @package frontend\controllers
 */
class ImageWriteStr {

    /**
     * 默认配置参数
     * @var array
     */
    public $config = [
        // 日期及时间样式  默认白色
        'dateStyle' => [
            'color' => [
                'r' => 255,
                'g' => 255,
                'b' => 255,
            ],
            'timeSize' => 28,
            'tX' => 130, //开始写入字符串的坐标
            'tY' => 530,
            'weekSize' => 38,
            'wX' => 130,
            'wY' => 420,
        ],
        // 标题颜色  默认黑色
        'titleStyle' => [
            'color' => [
                'r' => 0,
                'g' => 0,
                'b' => 0,
            ],
            'size' => 50,
            'x' => 130,
            'y' => 700,
        ],
        // 内容颜色  默认灰色
        'contentStyle' => [
            'color' => [
                'r' => 105,
                'g' => 105,
                'b' => 105,
            ],
            'size' => 30,
            'x' => 130,
            'y' => 800, //默认值 不需要修改
        ]
    ];

    /**
     * 加载自定义配置
     * ImageWriteStr constructor.
     * @param $config
     */
    public function __construct($config)
    {
        if(empty($config['file'])){
            echo '请设置图片路径';exit;
        }
        if(empty($config['strTtf'])){
            echo '请设置字体路径';exit;
        }
        if(empty($config['newFile'])){
            echo '请设置图片保存路径';exit;
        }
        $this->config['newFile'] = $config['newFile'];
        $this->config['imageFile'] = $config['file'];
        $this->config['strTtf'] = $config['strTtf'];
        $imageInfo = getimagesize($config['file']);
//        array(
//            0=>1125,
//            1=>2436,
//            2=>2,
//            3=>"width=1125 height=2436",
//            ["bits"]=>8,
//            ["channels"]=>3,
//            ["mime"]=>"image/jpeg",
//        );


        $imageName = pathinfo($config['file']);
//        array(
//            ["dirname"]=>".",
//            ["basename"]=>"news.jpg",
//            ["extension"]=>"jpg",
//            ["filename"]=>"news"
//        );

        $this->config['imageWidth'] = $imageInfo[0];
        $this->config['imageHeight'] = $imageInfo[1];
        $this->config['imageName'] = $imageName['filename'];
        $this->config['imageExtension'] = $imageName['extension'];
    }

    /**
     * 进行写入字符串
     * @param array $arr week=>星期 time=>日期 title标题 content=>内容
     * @param bool $preview 是否预览 默认为是
     */
    public function writeStr($arr = ['week'=>'','time'=>'','title'=>'','content'=>''],$preview = true)
    {
        //打开指定的图片文件
        $im = imagecreatefromjpeg($this->config['imageFile']);
        //设置星期及日期字体颜色
        $dateColor = imagecolorallocatealpha($im,$this->config['dateStyle']['color']['r'], $this->config['dateStyle']['color']['g'], $this->config['dateStyle']['color']['b'], 0);
        //设置标题字体颜色
        $titleColor = imagecolorallocatealpha($im,$this->config['titleStyle']['color']['r'], $this->config['titleStyle']['color']['g'], $this->config['titleStyle']['color']['b'], 0);
        //设置内容字体颜色
        $contentColor = imagecolorallocatealpha($im,$this->config['contentStyle']['color']['r'], $this->config['contentStyle']['color']['g'], $this->config['contentStyle']['color']['b'], 0);

        //自动换行处理
        $weekArr = $this->str($this->config['dateStyle']['weekSize'] , $arr['week']);
        $timeArr = $this->str($this->config['dateStyle']['timeSize'] , $arr['time']);
        $titleArr = $this->str($this->config['titleStyle']['size'] , $arr['title']);
        $contentArr = $this->str($this->config['contentStyle']['size'] , $arr['content']);

        //星期
        imagettftext($im, $this->config['dateStyle']['weekSize'], 0, $this->config['dateStyle']['wX'], $this->config['dateStyle']['wY'], $dateColor, $this->config['strTtf'], $weekArr['content']);

        //日期
        imagettftext($im, $this->config['dateStyle']['timeSize'], 0, $this->config['dateStyle']['tX'], $this->config['dateStyle']['tY'], $dateColor, $this->config['strTtf'], $timeArr['content']);

        //标题
        imagettftext($im, $this->config['titleStyle']['size'], 0, $this->config['titleStyle']['x'], $this->config['titleStyle']['y'], $titleColor, $this->config['strTtf'], $titleArr['content']);

        //内容
        imagettftext($im, $this->config['contentStyle']['size'], 0, $this->config['contentStyle']['x'], $this->config['contentStyle']['y'] + $titleArr['line'] * 100, $contentColor, $this->config['strTtf'], $contentArr['content']);

        //是否预览
        if($preview){
            header("content-type:image/png");
            imagepng($im);
            imagedestroy($im);
        }else{
            imagepng($im,$this->config['newFile'].$this->config['imageName'].time().'.'.$this->config['imageExtension']);
            imagedestroy($im);
        }

    }

    /**
     * 字符串自动换行
     * @param $size 字符串字体大小
     * @param $str 字符串
     * @return array content=>处理后的字符串 line=>行数
     */
    private function str($size , $str)
    {
        $content = "";
        $line = 0;
        //将字符串拆分成一个个单字
        for ($i=0;$i<mb_strlen($str);$i++) {
            $strArr[] = mb_substr($str, $i, 1);
        }

        foreach ($strArr as $v) {
            $newStr = $content.$v;
            $box = imagettfbbox($size , 0, $this->config['strTtf'], $newStr);
            // 判断拼接后的字符串是否超过预设的宽度  图片宽度-240为一行文字的宽度
            if (($box[2] > $this->config['imageWidth'] - 240) && ($content !== "")) {
                $content .= "\n";
                $line += 1;
            }
            $content .= $v;
        }

        return ['content'=>$content,'line'=>$line];
    }
}