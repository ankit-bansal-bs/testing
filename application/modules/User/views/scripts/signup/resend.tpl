<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: resend.tpl 9747 2012-07-26 02:08:08Z john $
 * @author		 Alex
 */
?>

<h2>
  <?php echo $this->translate("Verification Email") ?>
</h2>

<?php if( $this->error ): ?>
  <p>
    <?php echo $this->translate($this->error) ?>
  </p>

  <br />

  <h3>
    <?php echo $this->htmlLink(array('route' => 'default'), $this->translate('Back')) ?>
  </h3>
<?php else: ?>
  <p>
    <?php echo $this->translate('A verification message has been sent resent to ' .
      'your email address with instructions for activating your account. Once ' .
      'you have activated your account, you will be able to sign in.'); ?>
  </p>

  <br />

  <h3>
    <?php echo $this->htmlLink(array('route' => 'default'), $this->translate('OK, thanks!')) ?>
  </h3>
<?php endif; ?>