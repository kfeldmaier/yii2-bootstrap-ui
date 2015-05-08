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
class Video extends Widget
{

    const 
        ERR_UNSUPPORTED = 'Your browser does not support the video tag!',
        ERR_UNAVAILABLE = 'Sorry! The video you´re looking for is currently not available!',
        SUPPORTED_FILES = ['mp4', 'm4v', 'webm', 'weba', 'ogm', 'ogv', 'ogg'];

    /**
     * @var boolean das Video wird sofort gestartet, wenn ein Besucher die HTML-Seite aufruft
     */
    public $autoplay = false;

    /**
     * @var boolean es werden für die Steuerung Steuerelemente (controls) angezeigt, dass das Video gestoppt, gestartet und an eine andere Stelle gesprungen werden kann.
     */
    public $controls = true;

    /**
     * @var string source for the videos
     */
    public $sources;
     
    /**
     * @inheritdoc
     */
    public function init()
    {
        $view = $this->getView();
        BootstrapUIAsset::register($view);

        parent::init();
    }

    private function getMimetype($file)
    {
        $extension = pathinfo($file, PATHINFO_EXTENSION);
        return 'video/'.$extension;
    }

    public function run()
    {
        $inline = [];

        foreach ($this->sources as $source)
        {
            $url = \Yii::$app->urlManager->createUrl($source);
            // if (file_exists($url))
                array_push($inline, 
                    Html::tag(
                            'source', 
                            '',
                            [
                                'src' => $url,
                                'type' => $this->getMimetype($url)
                            ]
                    )
                );
        }
        
        if (!$inline)
            return Html::tag(
                'div', 
                self::ERR_UNAVAILABLE,
                [
                    'class' => 'alert alert-warning',
                    'role' => 'alert'
                ]
            );

        array_push($inline, Html::tag(
                    'div', 
                    self::ERR_UNSUPPORTED,
                    [
                        'class' => 'alert alert-warning',
                        'role' => 'alert'
                    ]
                ));

        $options = [];

        if($this->autoplay)
            $options['autoplay'] = true;

        if($this->controls)
            $options['controls'] = true;

        $options['width'] = 320;
        $options['height'] = 240;

        $video = Html::tag(
            'video', 
            implode(PHP_EOL, $inline),
            $options
        );

        return $video;
    }

}
