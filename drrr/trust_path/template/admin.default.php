</div>
<div id="logingo" class="header">
<?php if ( $dura['error'] ) : ?>
<div class="error">
<?php echo $dura['error'] ?>
</div>
<?php endif ?>
<form action="#" method="post">

<div class="field">
<input type="textbox" name="name" value="" size="10" maxlength="10" class="textbox" /><br />
<input type="password" name="pass" value="" size="10" class="textbox" style="margin-top:-10px;" />
<br />
<span class="buttonc">
<input type="submit" name="login" value="<?php e(t("Go!")) ?>" />
</span>
</div>

<input type="hidden" name="token" value="<?php echo $dura['token'] ?>" />

</form>

</div>
<div>