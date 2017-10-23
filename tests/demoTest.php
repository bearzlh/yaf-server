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
	}

	public function mysql()
	{
		$this->db = new DB;
		$this->db->addConnection(
			[
				'driver'    => 'mysql',
				'host'      => '10.0.3.161',
				'database'  => 'db_toushibao_main',
				'username'  => 'root',
				'password'  => '123456',
				'charset'   => 'utf8',
				'collation' => 'utf8_unicode_ci',
				'prefix'    => '',
			]
		);
		$this->db->setAsGlobal();
		$this->db->bootEloquent();
		var_dump($this->db->table('user_info')->first());
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
		var_dump(Yaf_Registry::get('config'));
	}

	public function service()
	{
		$rest = \App\library\Service\RESTService::instance();
		$rest->success(array());
	}
}