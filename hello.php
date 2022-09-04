<?php


class CounterDemo{
    private  $timeStamp;
    public  $reqCount=0;
    public  $limit=5;//时间窗口内最大请求数

    public $interval=1000; //时间窗口 ms
    public function __construct()
    {
        $this->timeStamp = time();
    }

    public  function grant(){

        $now=time();

        if($now<$this->timeStamp+$this->interval){
            //时间窗口内
            $this->reqCount++;
            return $this->reqCount<=$this->limit;
        }else{
            // 超时后重置
            $this->timeStamp=time();
            $this->reqCount=1;
            return true;
        }
    }
}
$coun = new CounterDemo();
$coun->grant();  //启动请求限制

$climgurl='?'.$_GET['suo']/*缩放*/.$_GET['cai']/*裁剪*/.
			$_GET['gszh']/*格式转换*/.$_GET['zlbh']/*质量变换*/.
			$_GET['gsmh']/*gaosimohu*/.$_GET['duibi']/*duibidu*/.
			$_GET['imgs']/*tupianshuiyin*/.$_GET['txts']/*wenzishuiyin*/;  //拼接api参数

$APIname='QAC_API';  //API名称
//文件
$filename = 'sinetxt.txt';
if(!file_exists($filename)) {
    die($filename.'数据文件不存在');
} else {
	//读取资源文件
	$giturlArr = file($filename);
}
$giturlData = [];
//将资源文件写入数组
foreach ($giturlArr as $key => $value) {
	$value = trim($value);
	if (!empty($value)) {
		$giturlData[] = trim($value);
	}
}
//id判断
if($_GET['id']==true and $_GET['id'] != 'TRUE'){//定位输出一条
	$randKey = $_GET['id'];
	$id = $_GET['id'];
} elseif($_GET['id'] == 'TRUE'){  //id=TRUE时请求json接口输出id
	$randKey = rand(0, count($giturlData)+$_GET['c']);
	$id = $randKey;
} else {  //随机获取$giturlData键值,彻底的随机
	$randKey = rand(0, count($giturlData)+$_GET['c']);
	shuffle($giturlData);  //打乱数组，实现更彻底的随机
}

//ArrayP:$giturlData为存放ImgUrl的数组
$imgurl = $giturlData[$randKey].$climgurl;
//随机输出十条
$randKeys = array_rand($giturlData, 500);
$imgurls = [];
foreach ($randKeys as $key) {
	$imgurls[] = $giturlData[$key];
}
//json格式
$json = array("API_name"=>"$APIname");
$returnType = $_GET['return'];
switch ($returnType) {
	//直接输出
	case 'img':
		$img = file_get_contents($imgurl, true);
		header("Content-Type: image/jpeg;");
		echo $img;
		break;
	//随机JSON输出10条
	case 'jsonpro':
		header('Content-type:text/json');
		//随机输出十张
		case 'jsonpro':
		$json['imgurls'] = $imgurls;
		echo json_encode($json,JSON_PRETTY_PRINT);
		break;
	//JSON格式输出
	case 'json':
		$json['imgurl'] = $imgurl;

		$imageInfo = getimagesize($imgurl);
		$json['width'] = "$imageInfo[0]";
		$json['height'] = "$imageInfo[1]";
		if($_GET['id'] == 'TRUE' and $_GET['id'] == TRUE){$json['id'] = "{$id}";}  //当id=TRUE时随机图也显示id
		header('Content-type:text/json');  //json文件的HTTP头
		echo json_encode($json,JSON_PRETTY_PRINT);
		break;
    //直接重定向
	default:
		header("Location:" . $imgurl);
		break;
}
?>
