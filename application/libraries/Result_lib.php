<?php
define('SUCCESS', '200');
define('BAD_REQUEST', '400');
define('UNAUTHORIZED', '401');
define('NOT_FOUND', '404');
define('TOKEN_ERROR', '410');
define('SIGN_ERROR', '411');
define('INTERNAL_SERVER_ERROR', '500');

class Result_lib
{

    public $success_result = array(
        'result_code' => SUCCESS
    );

    public $error_result = array(
        'result_code' => BAD_REQUEST
    );

    public function setInfo($info = "", $code = SUCCESS)
    {
        $this->success_result['info'] = $info;
        $this->success_result['result_code'] = $code;
        return $this->success_result;
    }

    public function setErrors($errors, $code = BAD_REQUEST)
    {
        $this->error_result['error_msg'] = $errors;
        $this->error_result['result_code'] = $code;
        return $this->error_result;
    }

    public function setInfoJson($info = "", $code = SUCCESS)
    {
        $this->success_result['info'] = $info;
        $this->success_result['result_code'] = $code;
        return json_encode($this->success_result);
    }

    public function setErrorsJson($errors, $code = BAD_REQUEST)
    {
        $this->error_result['error_msg'] = $errors;
        $this->error_result['result_code'] = $code;
        return json_encode($this->error_result);
    }
}

?>