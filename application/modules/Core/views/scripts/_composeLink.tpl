<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: _composeLink.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */
?>

<?php $this->headScript()
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Core/externals/scripts/composer_link.js') ?>

<script type="text/javascript">
  en4.core.runonce.add(function() {
    composeInstance.addPlugin(new Composer.Plugin.Link({
      title: '<?php echo $this->string()->escapeJavascript($this->translate('Add Link')) ?>',
      lang : {
        'cancel' : '<?php echo $this->string()->escapeJavascript($this->translate('cancel')) ?>',
        'Last' : '<?php echo $this->string()->escapeJavascript($this->translate('Last')) ?>',
        'Next' : '<?php echo $this->string()->escapeJavascript($this->translate('Next')) ?>',
        'Attach' : '<?php echo $this->string()->escapeJavascript($this->translate('Attach')) ?>',
        'Loading...' : '<?php echo $this->string()->escapeJavascript($this->translate('Loading...')) ?>',
        'Don\'t show an image' : '<?php echo $this->string()->escapeJavascript($this->translate('Don\'t show an image')) ?>',
        'Choose Image:' : '<?php echo $this->string()->escapeJavascript($this->translate('Choose Image:')) ?>',
        '%d of %d' : '<?php echo $this->string()->escapeJavascript($this->translate('%d of %d')) ?>'
      },
      requestOptions : {
        'url' :en4.core.baseUrl + 'core/link/preview'
      }
    }));
  });
</script>