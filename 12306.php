<?php 

/*
* Author By Peanode
* https://github.com/peanode/12306
*/

    require_once('./PHPMailer-5.2.9/PHPMailerAutoload.php');

/**
 * Class Huijia
 */
class Huijia{
        public $date_arr;
        public $train_arr;
        public $type_arr;
        public $url_arr;
        public $start_station;
        public $end_station;
        public $appkey = "8d3027ea2ffcdfbbd3c71e3fe62fb4de";

    /**
     * @param $date_arr
     * @param $start_station
     * @param $end_station
     * @param $train_arr
     * @param $type_arr
     */
    public function __construct($date_arr, $start_station, $end_station, $train_arr, $type_arr){
            $this->setDateArr($date_arr);
            $this->setStartStation($start_station);
            $this->setEndStation($end_station);
            $this->setTrainArr($train_arr);
            $this->setTypeArr($type_arr);
            $this->setUrlArr();
        }

    /**
     * @param Array $data_arr
     */
    public function setDateArr($date_arr)
    {
        if(empty($date_arr) || !is_array($date_arr)){
            exit('Date is NULL');
        }
        $this->date_arr = $date_arr;
    }

    /**
     * @param Array $start_station
     */
    public function setStartStation($start_station)
    {
        if(empty($start_station) || !is_array($start_station)){
            exit('START station is NULL');
        }
        $this->start_station = $start_station;
    }

    /**
     * @param mixed $end_station
     */
    public function setEndStation($end_station)
    {
        if(empty($end_station) || !is_array($end_station)){
            exit('END station is NULL');
        }
        $this->end_station = $end_station;
    }

    /**
     * @param Array $train_arr
     */
    public function setTrainArr($train_arr)
    {
        $this->train_arr = $train_arr;
    }

    /**
     * @param Array $type_arr
     */
    public function setTypeArr($type_arr)
    {
        if(empty($type_arr) || !is_array($type_arr)){
            exit('Type is NULL');
        }
        $this->type_arr = $type_arr;
    }

    /**
     * @param Array $url_arr
     */
    public function setUrlArr()
    {
        //https://kyfw.12306.cn/otn/lcxxcx/query?purpose_codes=ADULT&queryDate=2015-02-15&from_station=HZH&to_station=WHN
        foreach($this->start_station as $start){
            foreach($this->end_station as $end){
                foreach($this->date_arr as $date){
                    $this->url_arr[] = 'https://kyfw.12306.cn/otn/leftTicket/queryX?leftTicketDTO.train_date='.$date.'&leftTicketDTO.from_station='.$start.'&leftTicketDTO.to_station='.$end.'&purpose_codes=ADULT';

                }
            }
        }
    }

    /**
     * @param $url
     * @return string
     */
    public function getSslPage($url) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_REFERER, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            $result = curl_exec($ch);
            curl_close($ch);
            return $result;
        }

    /**
     * @param $json
     * @return array
     */
    public function checkAvailable($json) {
            $data_arr = json_decode($json, TRUE);
            $return = array();
            if(!isset($data_arr['data'])){
                $return['available'] = 0;
                return $return;
            }
            foreach($data_arr['data'] as $train){
                if(empty($this->train_arr) || in_array($train['queryLeftNewDTO']['station_train_code'], $this->train_arr)){
                    if ($train['queryLeftNewDTO']['canWebBuy'] == 'N') {
                        continue;
                    }
                    foreach($this->type_arr as $type=>$flag){
                        if($flag == 1){
                            if($train['queryLeftNewDTO'][$type] > 0){
                                $return['data'][$train['queryLeftNewDTO']['start_train_date']][$train['queryLeftNewDTO']['station_train_code']] = $train;
                            }
                        }
                    }
                }else{
                    continue;
                }
            }
            if(empty($return)){
                $return['available'] = 0;
            }else{
                $return['available'] = 1;
            }
            return $return;
        }

    /**
     * @return bool|string
     */
    public  function checkTicket(){
            if(!is_array($this->url_arr) || empty($this->url_arr)){
                exit('URL list is NULL');
            }
            $result = array();
            foreach($this->url_arr as $url){
                $json_str = $this->getSslPage($url);
                $return = $this->checkAvailable($json_str);
                if($return['available'] == 1){
                    $result[] = $return['data'];
                }else{
                    continue;
                }
            }
            if(!empty($result)){
                return $this->getHtmlContent($result);
            }else{
                return false;
            }
        }

    /**
     * @param $result
     * @return string
     */
    public function getHtmlContent($result){
            $html = '<style type="text/css">table,thead,thead,th,td{font-family: Tahoma,"宋体";font-size: 12px;margin: 0;padding: 0;text-align: center;border: 0;}table{border-collapse:collapse;}thead th{margin: 0;padding-top: 5px;padding-bottom: 5px;color: #fff;line-height: 18px;background: #3295D3;border: 1px #B0CEDD solid;}tbody th{margin: 0;padding-top: 5px;padding-bottom: 5px;font-size: 10px;font-weight: normal;line-height: 20px;border: 1px #B0CEDD solid;}</style><table><thead><th width="70px">日期</th><th width="50px">车次</th><th width="60px">出发/<br />到达</th><th width="60px">出发时间/<br />到达时间</th><th width="50px">历时</th><th width="40px">商务座</th><th width="40px">特等座</th><th width="40px">一等座</th><th width="40px">二等座</th><th width="50px">高级软卧</th><th width="40px">软卧</th><th width="40px">硬卧</th><th width="40px">软座</th><th width="40px">硬座</th><th width="40px">无座</th><th width="40px">其他</th></thead><tbody>';
            foreach ($result as $num) {
                foreach ($num as $date => $train) {
                    foreach ($train as $key => $value) {
                        $html .= '<tr><th>'.$value['queryLeftNewDTO']['start_train_date'].'</th><th>'.$key.'</th><th>';
                        $html .= $value['queryLeftNewDTO']['from_station_name'].'/'.$value['queryLeftNewDTO']['to_station_name'].'</th><th>'.$value['queryLeftNewDTO']['start_time'].'/'.$value['queryLeftNewDTO']['arrive_time'].'</th><th>'.$value['queryLeftNewDTO']['lishi'];
                        $html .= '</th><th>'.$value['queryLeftNewDTO']['swz_num'].'</th><th>'.$value['queryLeftNewDTO']['tz_num'].'</th><th>'.$value['queryLeftNewDTO']['zy_num'].'</th><th>'.$value['queryLeftNewDTO']['ze_num'];
                        $html .= '</th><th>'.$value['queryLeftNewDTO']['gr_num'].'</th><th>'.$value['queryLeftNewDTO']['rw_num'].'</th><th>'.$value['queryLeftNewDTO']['yw_num'];
                        $html .= '</th><th>'.$value['queryLeftNewDTO']['rz_num'].'</th><th>'.$value['queryLeftNewDTO']['yz_num'].'</th><th>'.$value['queryLeftNewDTO']['wz_num'].'</th><th>'.$value['queryLeftNewDTO']['qt_num'].'</th></tr>';
                    }
                }
            }
            return $html.'</tbody></table>';
        }


    /**
     * @param $content
     */
    public function sendMail($content,$to){

        $mail = new PHPMailer();//实例化PHPMailer核心类
        $mail->SMTPDebug = 0;//是否启用smtp的debug进行调试 开发环境建议开启 生产环境注释掉即可 默认关闭debug调试模式
        $mail->isSMTP();//使用smtp鉴权方式发送邮件
        $mail->SMTPAuth=true;//smtp需要鉴权 这个必须是true
        $mail->Host = 'smtp.qq.com';//链接qq域名邮箱的服务器地址
        $mail->SMTPSecure = 'ssl';//设置使用ssl加密方式登录鉴权
        $mail->Port = 465;//设置ssl连接smtp服务器的远程服务器端口号，以前的默认是25，但是现在新的好像已经不可用了 可选465或587
        $mail->CharSet = 'UTF-8';//设置发送的邮件的编码 可选GB2312 我喜欢utf-8 据说utf8在某些客户端收信下会乱码
        $mail->FromName = '不一样的烟火';//设置发件人姓名（昵称） 任意内容，显示在收件人邮件的发件人邮箱地址前的发件人姓名
        $mail->Username ='340562435';//smtp登录的账号 这里填入字符串格式的qq号即可
        $mail->Password = 'orzjydlcjguecabg';//smtp登录的密码 使用生成的授权码（就刚才叫你保存的最新的授权码）【非常重要：在网页上登陆邮箱后在设置中去获取此授权码】
        $mail->From = '340562435@qq.com';//设置发件人邮箱地址 这里填入上述提到的“发件人邮箱”
        $mail->isHTML(true);//邮件正文是否为html编码 注意此处是一个方法 不再是属性 true或false
        $mail->addAddress($to);//设置收件人邮箱地址
        $mail->Subject = '火车票';//添加该邮件的主题
        $mail->Body = $content;//添加邮件正文 上方将isHTML设置成了true，则可以是完整的html字符串 如：使用file_get_contents函数读取本地的html文件
        //简单的判断与提示信息
        if($mail->send()) {
            return true;
        }else{
            return false;
        }
        }
    }

    //================================================================================================================
    // 分割线
    //================================================================================================================
$data=array(
                'start'=>$_POST['start'],
                'end'=>$_POST['end'],
                'email'=>$_POST['email'],
                'date'=>$_POST['date']

);

    // 日期
    $date_arr = array(
           $data['date'],

        );
    $start=preg_match('/[A-Z]{3}/',$data['start'],$matches);
    $end=preg_match('/[A-Z]{3}/',$data['end'],$matche);

    // 请查找 station.txt，可以多个
    $start_station = array($matches['0']);
   $end_station = array($matche['0']);

    //指定车次,可以留空
    // $train_arr = array()
    $train_arr = array(

        );

    /*
    swz_num		商务座
    tz_num		特等座
    zy_num		一等座
    ze_num		二等座
    gr_num		高级软卧
    rw_num		软卧
    rz_num		软座
    yw_num		硬卧
    yz_num		硬座
    wz_num		无座
    qt_num		其他
    yb_num		?
    gg_num		?
    */
    $type_arr = array(
            'swz_num'=>'1',
            'tz_num'=>'1',
            'zy_num'=>'1',
            'ze_num'=>'1',
            'gr_num'=>'1',
            'rw_num'=>'1',
            'rz_num'=>'1',
            'yw_num'=>'1',
            'yz_num'=>'1',
            'wz_num'=>'1',
            'qt_num'=>'1'
        );


    $guonian = new Huijia($date_arr, $start_station, $end_station, $train_arr, $type_arr);
    $result = $guonian->checkTicket();
    if($result){
		
        if($guonian->sendMail($result,$data['email'])){
			exit ('有票啦，请查看邮件');
			
		}
            
       
    }

?>
