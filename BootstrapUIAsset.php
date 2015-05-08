<?php

/**
 * @copyright Copyright (c) 2015 K. Feldmaier
 * @link https://github.com/kfeldmaier/yii2-bootstrap-ui
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version 1.0.0
 */

namespace kfe\bootstrap\ui;

use yii\web\AssetBundle;

/**
 * Asset bundle for Bootstrap UI
 *
 * @author Kai Feldmaier <kai.feldmaier@gmail.com>
 * @since 2.0
 */
class BootstrapUIAsset extends AssetBundle
{
	public $sourcePath = '@vendor/kfe/yii2-bootstrap-ui/assets';

	public $depends = [
	   'yii\web\YiiAsset', 
	   'yii\bootstrap\BootstrapAsset',
	];

	public function init()
	{
		$this->css[] = YII_DEBUG ? 'css/bootstrap.ui.css' : 'css/bootstrap.ui.min.css';
		$this->js[] = YII_DEBUG ? 'js/jquery.panel.js?blubb' : 'js/jquery.panel.min.js?blubb';
	}
}
