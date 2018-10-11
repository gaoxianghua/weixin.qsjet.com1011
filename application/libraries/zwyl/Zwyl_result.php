<?php
define("ZWYL_RESULT_SUCCESS", '200');
define('ZWYL_RESULT_BAD_REQUEST', '400');
define('ZWYL_RESULT_UNAUTHORIZED', '401');
define('ZWYL_RESULT_NOT_FOUND', '404');
define('ZWYL_RESULT_TOKEN_ERROR', '410');
define('ZWYL_RESULT_SIGN_ERROR', '411');
define('ZWYL_RESULT_INTERNAL_SERVER_ERROR', '500');

define('ZWYL_RESULT_FILE_NOT_UPLOAD', '420');

class Zwyl_result
{

    public $code = ZWYL_RESULT_SUCCESS;

    public $error = '';

    public $info = '';

    /**
     *
     * @return the $code
     *         @auth zr
     *         @date 2015-6-3 上午12:02:41
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     *
     * @return the $info
     *         @auth zr
     *         @date 2015-6-3 上午12:02:41
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     *
     * @param field_type $code
     *            @auth zr
     *            @date 2015-6-3 上午12:02:41
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     *
     * @param field_type $error
     *            @auth zr
     *            @date 2015-6-3 上午12:02:41
     */
    public function setError($error, $code = ZWYL_RESULT_BAD_REQUEST)
    {
        $this->error = $error;
        $this->code = $code;
        return $this;
    }

    /**
     *
     * @param field_type $info
     *            @auth zr
     *            @date 2015-6-3 上午12:02:41
     */
    public function setInfo($info, $code = ZWYL_RESULT_SUCCESS)
    {
        $this->info = $info;
        $this->code = $code;
        return $this;
    }

    public function __toString()
    {
        echo json_encode($this);
    }
}

?>