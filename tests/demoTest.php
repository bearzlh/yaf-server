<?php

/**
 * Created by PhpStorm.
 * User: bear
 * Date: 17/10/20
 * Time: 下午5:15
 */
use Illuminate\Database\Capsule\Manager as DB;
use Elasticsearch\Client as ES;
use \Doctrine\Common\Cache\RedisCache as Cache;


Class DemoTest extends PHPUnit_YafTestCase
{
	/**
	 * @var Illuminate\Database\Capsule\Manager
	 */
	public $db;

	public $es;

	public $cache;

	public function testInit()
	{
//		$this->mysql();
//		$this->es();
//		$this->cache();
		$this->getConfig();
//		$this->service();
//		$this->appModel();
	}

	public function appModel()
	{
		$app = new \App\models\App\DimApp();
		var_dump($app->fetchAll(array('id'=>2)));
		var_dump($app->fetchRow(array('id'=>2)));
		var_dump($app->fetchRow(2));
		var_dump($app->exists(2));
	}

	public function mysql()
	{
		$this->db = new DB;
		$config = Yaf_Registry::get('config')->get('yaf')->get('db')->get('master')->toArray();
		$this->db->addConnection(
			$config
		);
		$this->db->setAsGlobal();
		$this->db->bootEloquent();
		var_dump($this->db->table('user_info')->select('user_id')->limit(1)->get());
	}

	public function es()
	{
		$this->es = new ES(
			[
				'hosts' => array(
					'10.0.1.68:9200',
					'10.0.1.69:9200',
				),
			]
		);

		$params = array(
			'index'=>'mobilesdk_20171020',
			'body'=>[
				"size"=>1
			]
		);
		var_dump($this->es->search($params));
	}

	public function cache()
	{
		$redis = new Redis();
		$redis->connect('10.0.3.161', 6379);

		$cacheDriver = new Cache();
		$cacheDriver->setRedis($redis);
		$cacheDriver->save('cache_id', 'my_data');
		echo $cacheDriver->fetch('cache_id');
	}

	public function getConfig()
	{
		var_dump((array)Yaf_Registry::get('config')->get('yaf')->get('db')->get('master')->toArray());
	}

	public function service()
	{
		$rest = \App\library\Service\RESTService::instance();
		$rest->success(array());
	}
}