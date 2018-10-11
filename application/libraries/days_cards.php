<?php
require_once ('application/libraries/Base.php');

/*
 * 用户公共信息
 * @Version: 0.0.1 alpha
 * @Created: 11:06:48 2010/11/23
 */
class days_cards extends Base
{

    public $start_time = '';

    public $end_time = '';

    public $time = '';

    public function __construct()
    {
        parent::__construct();
        $this->time = date('Y-m-d');
        $this->model('days_card_model');
        $this->model('days_detail_model');
        $this->ci = &get_instance();
    }

    /*
     * 21天卡信息获取
     * liting
     * 2016/05/09 16:00:00
     * 传入参数：
     *
     *
     */
    public function getCardsList($user_id)
    {
        return $this->days_card_model->getListByUser($user_id);
    }

    /*
     * 21天卡详情信息获取
     * liting
     * 2016/05/09 16:00:00
     * 传入参数：
     *
     *
     */
    public function getCardsDataList($user_id,$mole)
    {
        return $this->days_detail_model->getCardsDataList($user_id, $this->input->get('id'),$mole);
    }

    /*
     * 21天卡创建操作
     * liting
     * 2016/05/09 10:30:00
     * 传入参数：
     *
     *
     */
    public function insertCards($user_id,$mole)
    {
        $this->start_time = $this->input->post('start_time');
        $this->end_time = $this->input->post('end_time');
        
        if ($this->start_time < $this->time) {
            return false;
        }
        
        return $this->doInsertCards($user_id);
    }

    /*
     * 21天卡添加执行
     * liting
     * 2016/05/09 10:30:00
     * 传入参数：
     * $this->open_id；（用户微信id）
     *
     */
    public function doInsertCards($user_id)
    {
        $cards = array(
            'user_id' => $user_id,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'add_time' => $this->time,
            'status' => '0',
            'num' => '0'
        );
        return $this->days_card_model->insert($cards);
    }

    /*
     * 21天卡数据录入
     * liting
     * 2016/05/09 10:30:00
     * 传入参数：
     *
     *
     */
    public function insertDataCards($user_id,$mole)
    {
        $card_detail = $this->handleData($this->input->post());
        $result = $this->days_detail_model->checkDataCards(date('Y-m-d'), $user_id, $this->input->get('id'),$mole);
        if (! empty($result)) {
            $re = $this->days_detail_model->update($result['id'], $card_detail,$mole);
        } else {
            $card_detail += array(
                'days_time' => $this->time,
                'user_id' => $user_id,
                'days_id' => $this->input->get('id')
            );
            $re = $this->days_detail_model->insert($card_detail,$mole);
        }
        if ($re) {
            return $this->days_card_model->updateByNum($user_id, $this->input->get('id'));
        }
    }

    /*
     * 数据处理
     * liting
     * 2016/05/09 10:30:00
     * 传入参数：
     *
     *
     */
    public function handleData($array)
    {
        $data = '';
        if (is_array($array)) {
            foreach ($array as $k => $v) {
                $data[$k] = htmlspecialchars($v);
            }
        } else {
            $data = htmlspecialchars($array);
        }
        return $data;
    }

    /*
     * 21天卡删除
     * liting
     * 2016/05/09 11:30:00
     * 传入参数：
     *
     */
    public function delete($user_id,$mole)
    {
        $re = $this->days_card_model->delete($user_id, $this->input->get('id'));
        if ($re) {
            $re = $this->days_detail_model->delete($user_id, $this->input->get('id'),$mole);
        }
        return $re;
    }

    /*
     * 早中晚判断
     * liting
     * 2016/05/09 15:30:00
     * 传入参数：
     *
     *
     */
    public function checkTime($type_id)
    {
        switch ($type_id) {
            case 1:
                return 'morning';
                break;
            case 2:
                return 'noon';
                break;
            case 3:
                return 'night';
                break;
            default:
                return 'morning';
                break;
        }
    }

    /*
     * 查询21天卡是否完成
     * liting
     * 2016/05/09 10:30:00
     * 传入参数：
     * $this->open_id；（用户微信id）
     *
     */
    public function checkCardsStatus()
    {
        return $this->days_card_model->findOneByCards($this->input->get('id'));
    }
}