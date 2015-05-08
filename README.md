# yii2-bootstrap-ui
============

This extension adds some Widgets for the use of Bootstrap 3 in YII2

## Widgets available in this bundle

- Panel
- Image
- Video

#### Panel

Renders the Bootstrap 3 Panel and adds additional functionality like collapse and xhr content.

```php
<!-- Example #1: Content wrapper with options -->
<?php Panel::begin([
 'collapsible' => true,
 'title' => 'bin ich', //Yii::t('app', 'Sidebar'),
 'footerTemplate' => '{imprint} | {contact} | {user}',
 'footerButtons' => [
 'imprint' => function()
 {
     return HTML::a(Yii::t('app', 'Imprint'), ['site/impressum']);
 },
 'contact' => function()
 {
     return HTML::a(Yii::t('app', 'Contact'), ['site/contact']);
  },
 'user' => function()
 {
     return HTML::tag('span', '', ['class' => 'glyphicon glyphicon-user']);
 }
 ]
]); ?>
 <p><?= Yii::t('app', 'Application') ?></p>
 <p>This panel is collapsible.</p>
<?php Panel::end(); ?>
<!-- Example #2: Simple pannel without content but options -->
<?= Panel::widget([
 'collapsible' => true,
 'title' => 'Panel 2'
]); ?>
<!-- Example #2: Simple pannel without content but options -->
<?= Panel::widget([
 'collapsible' => true,
 'title' => 'Panel 3',
 'url' => ['io/directorylink/index'],
 'footerTemplate' => '{reload} | uff'
]); ?>
```

#### Image
```php
<?php 
<?= Image::widget([
 'shape' => 'rounded',
 'url' => ['fileadmin/serve', 'id'=>'4711'],
 'responsive' => true
]); ?>
```

#### Video

... will follow ...

## License

**yii2-bootstrap-ui** is released under the BSD 3-Clause License. See the bundled `LICENSE.md` for details.
