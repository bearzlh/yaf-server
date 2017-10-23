<?php
use Illuminate\Contracts\Events\Dispatcher;
/**
 * @name Bootstrap
 * @author bear
 * @desc 所有在Bootstrap类中, 以_init开头的方法, 都会被Yaf调用,
 * @see http://www.php.net/manual/en/class.yaf-bootstrap-abstract.php
 * 这些方法, 都接受一个参数:Yaf_Dispatcher $dispatcher
 * 调用的次序, 和申明的次序相同
 */
class Bootstrap extends \Yaf\Bootstrap_Abstract {

    public function _initConfig() {
		//把配置保存起来
		$arrConfig = \Yaf\Application::app()->getConfig();
		\Yaf\Registry::set('config', $arrConfig);
	}

	public function _initLoader()
	{
		\Yaf\Loader::import(APPLICATION_PATH . '/vendor/autoload.php');
	}

	public function _initDataBase()
	{
		//初始化 illuminate/database
		$capsule = new \Illuminate\Database\Capsule\Manager;
		$capsule->addConnection(\Yaf\Registry::get('config')->get('yaf')->get('db')->get('master')->toArray());
		$capsule->setEventDispatcher(new \Illuminate\Events\Dispatcher(new \Illuminate\Container\Container));
		$capsule->setAsGlobal();
		//开启Eloquent ORM
		$capsule->bootEloquent();
		class_alias('\Illuminate\Database\Capsule\Manager', 'DB');
	}

	public function _initCache()
	{

	}

	public function _initPlugin(\Yaf\Dispatcher $dispatcher) {
		//注册一个插件
		$objSamplePlugin = new SamplePlugin();
		$dispatcher->registerPlugin($objSamplePlugin);
	}

	public function _initRoute(\Yaf\Dispatcher $dispatcher) {
		//在这里注册自己的路由协议,默认使用简单路由
	}
	
	public function _initView(\Yaf\Dispatcher $dispatcher) {
		//在这里注册自己的view控制器，例如smarty,firekylin
	}
}
