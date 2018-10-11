<?php

class Wechat_oAuth2 extends CI_Controller
{

    public function index()
    {

        header("Content-type: text/html; charset=utf-8");
        
        if (isset($_GET['code']) && isset($_GET['state'])) {
            $code = $_GET['code'];
            $state = $_GET['state'];
            // return ; //fortest
        } else {
            log_message("error", "NO CODE from wechat oAuth2, params: " . json_encode($this->input->get()));
            return;
        }
        
        $this->load->library("wechat");

        $result = $this->wechat->getOAuth2Result($code);

        if (! $result || ! isset($result['openid']) || ! $result['openid']) {
            log_message("error", "Failed to get openID, reason:" . json_encode($result));
            return;
        }
        $openID = $result['openid'];
        // Redirect
        
        if($qc_code = strstr($state,'_') ){
            $qc_code = trim($qc_code,'_');
            header('Location:' . base_url() . 'doctor?qc_code=' . $qc_code . '&test&open_id=' . $openID);
        }else{
            switch ($state) {
                case 'cmef':
                    header('Location:' . base_url() . 'cmef?open_id=' . $openID);
                    break;
                case 'project':
                    header('Location:' . base_url() . 'project/index?open_id=' . $openID);
                    break;
                case 'article':
                    header('Location:' . base_url() . 'article/index?&open_id=' . $openID);
                    //header('Location:http://www.biying.com');
                    break;
                case 'videos':
                    header('Location:' . base_url() . 'videos?open_id=' . $openID);
                    break;
                case 'company':
                    //header('Location:http://mp.weixin.qq.com/s?__biz=MzA3MDkyMDgwOA==&mid=2454270401&idx=1&sn=45da01545d6cd8be7dae78d7fb871b29&chksm=8888eec6bfff67d012b444d0a3ea5aea4848d32483628bb6b8abd6aa3806adde96592b253056&mpshare=1&scene=1&srcid=1019hO03NB0tg1OJ9w1PKYgy#rd');
                    header('Location:https://mp.weixin.qq.com/s/B1ckYGBHt28iOsxFM-J3jw');
                    break;
                case 'about':
                    header('Location:http://mp.weixin.qq.com/s/ElA282w_G_G2HfLc52XENQ');
                    break;
                case 'question':
                    header('Location:http://mp.weixin.qq.com/s?__biz=MzA3MDkyMDgwOA==&mid=2454270401&idx=2&sn=7e431a4fbbaf02076bac8dab66bd21d5&chksm=8888eec6bfff67d032c4b7ded1bf6da645a774e06290ff7e03a22a018d7e5044ef59eb301838&mpshare=1&scene=1&srcid=1019T8m7bcknTUdaLg0SDQ6d#rd');
                    break;
                case 'help':
                    header('Location:http://mp.weixin.qq.com/s?__biz=MzA3MDkyMDgwOA==&mid=2454270401&idx=3&sn=a584bd236edbb8bb4f9ce66ba6a3d00e&chksm=8888eec6bfff67d044c4d681983c45ad74afc631cc84766c168f4a9cf9c360d12c5de8b54c28&mpshare=1&scene=1&srcid=1019ylueqWVw2I26oFjes4B5#rd');
                    break;
                case 'user':
                    header('Location:' . base_url() . 'user?open_id=' . $openID);
                    break;
                case 'cards':
                    header('Location:' . base_url() . 'days_card?open_id=' . $openID);
                    break;
                case 'login':
                    header('Location:' . base_url() . 'user?open_id=' . $openID);
                    break;
                case 'registerProject':
                    header('Location:' . base_url() . 'register_project?open_id=' . $openID);
                    break;
                case 'product':
                    header('Location:' . base_url() . 'product?open_id=' . $openID);
                    break;
                case 'mydiscount':
                    header('Location:' . base_url() . 'mydiscount/getList?open_id=' . $openID);
                    break;
                case 'exlogin':
                    header('Location:' . base_url() . 'ex_account?open_id=' . $openID);
                    break;
                default:
                    break;
            }
        }
    }
}

?>
