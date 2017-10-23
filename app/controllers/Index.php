<?php
use App\models\App\DimApp;

/**
 * @name IndexController
 * @author bear
 * @desc 默认控制器
 * @see http://www.php.net/manual/en/class.yaf-controller-abstract.php
 */
class IndexController extends BaseController {
	/** 
     * 默认动作
     * Yaf支持直接把Yaf_Request_Abstract::getParam()得到的同名参数作为Action的形参
     * 对于如下的例子, 当访问http://yourhost/Sample/index/index/index/name/bear 的时候, 你就会发现不同
     */
	public function indexAction() {
		$model = new DimApp();
		$app = $model->fetchRow(2);

		$this->rest->success($app);
	}
}