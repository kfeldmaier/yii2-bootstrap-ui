<?php

/**
 * @copyright Copyright (c) 2015 K. Feldmaier
 * @link https://github.com/kfeldmaier/yii2-bootstrap-ui
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version 1.0.0
 */

namespace kfe\bootstrap\ui;

use yii\base\Widget;
use yii\helpers\Html;

/**
 * Renders the Bootstrap 3 Panel and adds additional 
 * functionality like collapse and xhr content
 *
 * Example(s):
 * ```php
 * <!-- Example #1: Content wrapper with options -->
 * <?php Panel::begin([
 *  'collapsible' => true,
 *  'title' => 'bin ich', //Yii::t('app', 'Sidebar'),
 *  'footerTemplate' => '{imprint} | {contact} | {user}',
 *  'footerButtons' => [
 *  'imprint' => function()
 *  {
 *      return HTML::a(Yii::t('app', 'Imprint'), ['site/impressum']);
 *  },
 *  'contact' => function()
 *  {
 *      return HTML::a(Yii::t('app', 'Contact'), ['site/contact']);
 *   },
 *  'user' => function()
 *  {
 *      return HTML::tag('span', '', ['class' => 'glyphicon glyphicon-user']);
 *  }
 *  ]
 * ]); ?>
 *  <p><?= Yii::t('app', 'Application') ?></p>
 *  <p>This panel is collapsible.</p>
 * <?php Panel::end(); ?>
 * <!-- Example #2: Simple pannel without content but options -->
 * <?= Panel::widget([
 *  'collapsible' => true,
 *  'title' => 'Panel 2'
 * ]); ?>
 * <!-- Example #2: Simple pannel without content but options -->
 * <?= Panel::widget([
 *  'collapsible' => true,
 *  'title' => 'Panel 3',
 *  'url' => ['io/directorylink/index'],
 *  'footerTemplate' => '{reload} | uff'
 * ]); ?>
 *
 * @author Kai Feldmaier <kai.feldmaier@gmail.com>
 * @since 2.0
 */
class Image extends Widget
{

    const 
        SHAPE_ROUNDED = 'rounded',
        SHAPE_CIRCLE = 'circle',
        SHAPE_THUMB = 'thumbnail';

    /**
     * @var string source for the image
     */
    public $url;

    /**
     * @var string source for the videos
     */
    public $shape;

    /**
     * @var string source for the videos
     */
    public $responsive = true;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $view = $this->getView();
        BootstrapUIAsset::register($view);

        if (!$this->shape)
            $this->shape = self::SHAPE_ROUNDED;

        parent::init();
    }


    public function run()
    {
        $classes = [];
        $options = [
                'src' => \Yii::$app->urlManager->createUrl($this->url)
            ];

        if ($this->responsive)
            array_push($classes, 'img-responsive');

        switch ($this->shape)
        {
            case self::SHAPE_ROUNDED:
                array_push($classes, 'img-rounded');
                break;
            case self::SHAPE_CIRCLE:
                array_push($classes, 'img-circle');
                break;
            case self::SHAPE_THUMB:
                array_push($classes, 'img-thumbnail');
                break;
        }

        $options['class'] = implode(' ', $classes);

        $img = Html::tag(
            'img', 
            '',
            $options
        );

        return $img;
    }

}
