<?php
/**
 * @author ciogao@gmail.com
 * Date: 17/7/31 下午7:27
 */
error_reporting(1);

require_once dirname(__FILE__).'/../vendor/autoload.php';
define("APPLICATION_PATH", dirname(__FILE__) . "/../app");
define("CONF_PATH", dirname(__FILE__) . "/../conf");

/**
 * UT基类
 * Class PHPUnit_YafTestCase
 */
class PHPUnit_YafTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * @var null|Yaf_Application
     */
    public $application = NULL;

    /**
     * @var PHPUnit_MockYafAction
     */
    public $sMockActoin = NULL;

    /**
     * YAF application只能实例化一次
     *
     * PHPUnit_YafTestCase constructor.
     */
    public function __construct()
    {
        if (!$this->application = Yaf_Registry::get('Application')) {
            $this->application = new Yaf_Application(CONF_PATH . "/app.ini");
            $this->application->bootstrap();

            Yaf_Dispatcher::getInstance()->throwException(TRUE);
            Yaf_Dispatcher::getInstance()->catchException(FALSE);
            Yaf_Dispatcher::getInstance()->returnResponse(TRUE);
            Yaf_Registry::set('Application', $this->application);
        }

        if (!$this->sMockActoin = Yaf_Registry::get('sMockActoin')) {
            $this->sMockActoin = PHPUnit_MockYafAction::getInstance($this->application);
            Yaf_Registry::set('sMockActoin', $this->sMockActoin);
        }
        $arrConfig = Yaf_Application::app()->getConfig();
        Yaf_Registry::set('config', $arrConfig);
        $this->setUseErrorHandler(TRUE);
        set_error_handler(array('PHPUnit_YafErrorHandler', 'cliErrorHandler'));
    }

    public function watchNotice()
    {
        PHPUnit_YafErrorHandler::watchNotice();
    }

    public function watchWarning()
    {
        PHPUnit_YafErrorHandler::watchWarning();
    }

    public function watchError()
    {
        PHPUnit_YafErrorHandler::watchError();
    }
}

/**
 * UnitTest关注的错误级别，默认warning
 *
 * Class PHPUnit_YafErrorHandler
 */
Class PHPUnit_YafErrorHandler
{
    const LEVEL_NOTICE = 1;
    const LEVEL_WARNING = 2;
    const LEVEL_ERROR = 3;

    static private $level = 2;

    /**
     * @param $errno
     * @param $errstr
     * @param $errfile
     * @param $errline
     * @return bool
     * @throws Exception
     */
    static public function cliErrorHandler($errno, $errstr, $errfile, $errline)
    {
        switch ($errno) {
            case YAF_ERR_NOTFOUND_CONTROLLER: //Yaf
            case YAF_ERR_NOTFOUND_MODULE: //Yaf
            case YAF_ERR_NOTFOUND_ACTION:   //Yaf
                if (self::isErrorLevel()) {
                    $msg = "Not Found: [{$errno}] {$errstr} {$errfile} {$errline}";
                    throw new Exception($msg, $errno);
                }
                break;
            case E_ERROR:
            case E_CORE_ERROR:
            case E_COMPILE_ERROR:
                if (self::isErrorLevel()) {
                    $msg = "Core Error: [{$errno}] {$errstr} {$errfile} {$errline}";
                    throw new Exception($msg, $errno);
                }
                break;
            case E_NOTICE:
            case E_USER_NOTICE:
                if (self::isNoticLevel()) {
                    $msg = "Notic Error: [{$errno}] {$errstr} {$errfile} {$errline}";
                    throw new Exception($msg, $errno);
                }
                break;
            case E_CORE_WARNING:
            case E_COMPILE_WARNING:
            case E_USER_WARNING:
                if (self::isWarningLevel()) {
                    $msg = "Warning Error: [{$errno}] {$errstr} {$errfile} {$errline}";
                    throw new Exception($msg, $errno);
                }
                break;
            case 2: //PHPUnit
            case 2048: //PHPUnit
                break;
            default:
                echo "Unknown Error type: [{$errno}] {$errstr} {$errfile} {$errline}\n";
                break;
        }

        return TRUE;
    }

    static public function watchNotice()
    {
        self::$level = self::LEVEL_NOTICE;
    }

    static public function watchWarning()
    {
        self::$level = self::LEVEL_WARNING;
    }

    static public function watchError()
    {
        self::$level = self::LEVEL_ERROR;
    }

    /**
     * @return bool
     */
    static private function isNoticLevel()
    {
        return self::$level <= self::LEVEL_NOTICE;
    }

    /**
     * @return bool
     */
    static private function isWarningLevel()
    {
        return self::$level <= self::LEVEL_WARNING;
    }

    /**
     * @return bool
     */
    static private function isErrorLevel()
    {
        return self::$level <= self::LEVEL_ERROR;
    }
}

/**
 * Class PHPUnit_MockYafAction
 */
Final Class PHPUnit_MockYafAction
{
    public $method = 'get';
    public $module = 'index';
    public $controller = 'index';
    public $action = 'index';
    public $responseBody = NULL;

    /**
     * @var Yaf_Application
     */
    public $application;

    /**
     * @var PHPUnit_MockYafAction
     */
    static public $self = NULL;

    /**
     * @param $application
     * @return PHPUnit_MockYafAction
     */
    static public function getInstance($application)
    {
        if (is_null(self::$self)) {
            self::$self = new self($application);
        }

        return self::$self;
    }

    public function __construct($application)
    {
        $this->application = $application;
    }

    /**
     * @param $name
     * @return PHPUnit_MockYafAction
     */
    public function setModuleName($name)
    {
        $this->module = $name;
        return $this;
    }

    /**
     * @param $name
     * @return PHPUnit_MockYafAction
     */
    public function setControllerName($name)
    {
        $this->controller = $name;
        return $this;
    }

    /**
     * @param $name
     * @return PHPUnit_MockYafAction
     */
    public function setActionName($name)
    {
        $this->action = $name;
        return $this;
    }

    /**
     * @param $name
     * @return PHPUnit_MockYafAction
     */
    public function setMethod($name)
    {
        $this->method = $name;
        return $this;
    }

    /**
     * @param $name
     * @param $value
     * @return PHPUnit_MockYafAction
     */
    public function setPost($name, $value)
    {
        $_POST[$name] = $value;
        return $this;
    }

    /**
     * @param $name
     * @param $value
     * @return PHPUnit_MockYafAction
     */
    public function setGet($name, $value)
    {
        $_GET[$name] = $value;
        return $this;
    }

    /**
     * @param $name
     * @param $value
     * @return PHPUnit_MockYafAction
     */
    public function setCookie($name, $value)
    {
        $_COOKIE[$name] = $value;
        return $this;
    }

    /**
     * @param $name
     * @param $value
     * @return PHPUnit_MockYafAction
     */
    public function setSession($name, $value)
    {
        $_SESSION[$name] = $value;
        return $this;
    }

    /**
     * @return PHPUnit_MockYafAction
     * @throws Exception
     */
    public function doMock()
    {

        try {
            $request = new Yaf_Request_Simple($this->module, $this->controller, $this->action, $this->method);

            $request->setModuleName($this->module);
            $request->setControllerName($this->controller);
            $request->setActionName($this->action);
            $request->method = $this->method;

            $this->application->getDispatcher()->getInstance()->dispatch($request);

            unset($request);
        } catch (CWOPException $e) {
            CliStorage::instance()->setBody($e->getMessage());
        } catch (Exception $e) {
            throw $e;
        }

        return $this;
    }

    public function refresh()
    {
        $this->method = 'get';
        $this->module = $this->controller = $this->action = $this->responseBody = NULL;
        $_GET         = $_POST = $_COOKIE = $_SESSION = array();
    }

    /**
     * @return PHPUnit_ResponseBody
     */
    public function getBody()
    {
        $this->responseBody = new PHPUnit_ResponseBody(CliStorage::instance()->getBody());
        return $this->responseBody;
    }

    public function __destruct()
    {
        unset($this);
    }
}

/**
 * Class PHPUnit_ResponseBody
 */
Class PHPUnit_ResponseBody
{
    public function __construct($body)
    {
        $this->body = $body;
    }

    public function isReal()
    {
        return isset($this->body);
    }

    public function isNull()
    {
        return is_null($this->body);
    }

    public function isString()
    {
        return is_string($this->body);
    }

    public function isInt()
    {
        return is_numeric($this->body);
    }

    public function isArray()
    {
        return is_array($this->body);
    }

    public function toRaw()
    {
        return $this->body;
    }

    public function toString()
    {
        return $this->isString() ? $this->body : $this->isArray() ? json_encode($this->body) : (string)$this->body;
    }

    public function toJson()
    {
        return json_decode($this->body, TRUE);
    }

    public function __destruct()
    {
        unset($this->body);
    }
}

/**
 * Class PHPUnit_MockYafView
 */
Final Class PHPUnit_MockYafView implements Yaf_View_Interface
{
    private $_view_directory = NULL;
    private $_tpl_vars = array();
    private static $_instance = NULL;

    /**
     * @return null|PHPUnit_MockYafView
     */
    public static function getInstance()
    {
        if (!(self::$_instance instanceof self)) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }

    /**
     * @param $view_directory
     */
    public function setScriptPath($view_directory)
    {
        $this->_view_directory = $view_directory;
    }

    /**
     * @return null
     */
    public function getScriptPath()
    {
        return $this->_view_directory;
    }

    /**
     * @param null $key
     * @return array|null
     */
    public function get($key = NULL)
    {
        if (is_null($key)) {
            return $this->_tpl_vars;
        }

        return isset($this->_tpl_vars[$key]) ? $this->_tpl_vars[$key] : NULL;
    }

    /**
     * @param $spec
     * @param null $value
     */
    public function assign($spec, $value = NULL)
    {
        if (!is_array($spec)) {
            $this->_tpl_vars[$spec] = $value;
            return;
        }
        foreach ($spec as $key => $value) {
            $this->_tpl_vars[$key] = $value;
        }
    }

    /**
     * @param $view_path
     * @param null $tpl_vars
     */
    public function render($view_path, $tpl_vars = NULL)
    {
        return;
    }

    /**
     * @param $view_path
     * @param null $tpl_vars
     */
    public function display($view_path, $tpl_vars = NULL)
    {
        return;
    }

    /**
     * @param $key
     * @return array|null
     */
    public function __get($key)
    {
        return $this->get($key);
    }

    public function clear()
    {
        $this->_tpl_vars = array();
    }
}
