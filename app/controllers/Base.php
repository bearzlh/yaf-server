<?php
use App\library\Service\RESTService;

/**
 * Created by PhpStorm.
 * User: bear
 * Date: 17/10/23
 * Time: 下午4:59
 */

class BaseController extends \Yaf\Controller_Abstract
{
	/**
	 * @var App\library\Service\RESTService
	 */
	protected $rest = NULL;

	public function init()
	{
		$this->rest = RESTService::instance();
		return $this;
	}
}