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
class Panel extends Widget
{

    /**
     * @var string the template used for composing buttons in the panel-footer div.
     *
     * ```
     * footerButtons => '{reload}',
     * ```
     *
     * @see footerButtons
     */
    public $footerTemplate;
    
    /**
     * @var array footer-button rendering callbacks. The array keys are the button names (without curly brackets),
     * and the values are the corresponding button rendering callbacks. The callbacks should use the following
     * signature:
     *
     * ```php
     * function () {
     *     // return the button HTML code
     * }
     * ```
     *
     * @see footerTemplate
     */
    public $footerButtons = [];

    /**
     * @var string a title for the panel
     */
    public $title;

    /**
     * @var boolean either the panel body can be toggled or not
     *
     * @see collapsible
     */
    public $collapsible = true;

    /**
     * @var boolean either to start collapsed or not
     *
     * @see collapsible
     */
    public $collapsed = null;

    /**
     * @var null|number an interval in seconds to auto-reload using the given url
     *
     * @see url
     */
    public $autorefresh = null;

    /**
     * @var boolean either the content can be reloaded or not
     *
     * @see url, autorefresh
     */
    public $refreshable = true;

    /**
     * @var string the uri of the ajax content, if the content should be loaded using xhr
     *
     * @see autorefresh, refreshable
     */
    public $url;

    /**
     * @var string a unique id for this widget
     */
    protected $id;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $view = $this->getView();
        BootstrapUIAsset::register($view);

        if (empty($this->id))
            $this->id = 'panel-'.uniqid();

        $this->initDefaultFooterButtons(); 

        parent::init();

        ob_start();
    }

    /**
     * Initializes the default button rendering callbacks.
     */
    protected function initDefaultFooterButtons()
    {
        if (!empty($this->url) && $this->refreshable && empty($this->footerTemplate))
            $this->footerTemplate = '{reload}';

        if (!isset($this->footerButtons['reload'])) {
            $this->footerButtons['reload'] = function () {
                return Html::a(
                    Html::tag(
                        'span',
                        '',
                        ['class' => 'glyphicon glyphicon-refresh']
                    ), 
                    \Yii::$app->urlManager->createUrl($this->url), 
                    [
                        'title' => \Yii::t('yii', 'Reload'),
                        'class' => 'btn-panel-reload'
                    ]
                );
            };
        }
    }

    public function run()
    {
        $content = ob_get_clean();

        $options = [
                'class' => 'panel panel-default', 
                'id' => Html::encode($this->id)
            ];

        if (!empty($this->url))
        {
            $options['data-url'] = \Yii::$app->urlManager->createUrl($this->url);
            if (is_numeric($this->autorefresh))
                $options['data-autorefresh'] = $this->autorefresh;

            $options['data-refreshable'] = $this->refreshable ? 'true' : 'false';
        }

        if ($this->collapsible)
        {
            $options['data-collapsible'] = 'true';
            if (!is_null($this->collapsed))
                $options['data-collapsed'] = $this->collapsed ? 'true' : 'false';
        }

        $inline = [$this->renderHeader(), $this->renderBody($content)];

        if (!empty($this->url) && ($this->autorefresh || $this->refreshable))
            array_push($inline, Html::tag(
                    'div',
                    '',
                    ['class' => 'panel-idle-icon']
                )
            );

        array_push($inline, $this->renderFooter());

        $panel = Html::tag(
            'div', 
            implode(PHP_EOL, $inline),
            $options
        );

        return $panel;
    }

    /**
     * Render the Panel Header. Returns empty string if title is empty and collapsible is false.
     */
    protected function renderHeader()
    {
        $content = [];

        if (!empty($this->title))
            array_push($content, 
                Html::tag(
                    'h3', 
                    Html::encode($this->title),
                    ['class' => 'panel-title']
                )
            );

        if ($this->collapsible)
            array_push($content, 
                Html::a(
                    Html::tag(
                        'span', 
                        '', 
                        ['class' => 'glyphicon glyphicon-chevron-'.($this->collapsed ? 'down' : 'up').' panel-collapse-icon']
                    ),
                    '#'.Html::encode($this->id),
                    ['class' => 'btn-panel-toggle-collapse pull-right']
                )
            );

        return empty($content) ? '' : Html::tag(
                'div', 
                implode(PHP_EOL, $content),
                ['class' => 'panel-heading']
        );
    }

    /**
     * Render the Panel Body.
     */
    protected function renderBody($content=null)
    {        
        return Html::tag('div', empty($content) ? '' : $content, ['class' => 'panel-body']);
    }

    /**
     * Render the Panel Footer. Returns empty string if no buttons specified.
     */
    protected function renderFooter()
    {        
        $buttons = $this->renderFooterButtons();

        return empty($buttons) ? '' : Html::tag(
            'div', 
            $buttons, 
            ['class' => 'panel-footer']
        );
    }

    /**
     * @inheritdoc
     */
    protected function renderFooterButtons()
    {
        return preg_replace_callback('/\\{([\w\-\/]+)\\}/', function ($matches) {
            $name = $matches[1];
            if (isset($this->footerButtons[$name])) {
                return call_user_func($this->footerButtons[$name]);
            } else {
                return '';
            }
        }, $this->footerTemplate);
    }

}
