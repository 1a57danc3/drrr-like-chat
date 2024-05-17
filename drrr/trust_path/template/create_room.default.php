</div>
<div id="cra">
<div class="header">
<h2><?php e(t("Create Room")) ?></h2>
<center>
<?php if ( $dura['error'] ) : ?>
<div>
<?php echo $dura['error'] ?>
</div>
<?php endif ?>

<form action="#" method="post">
<table>
<tr>
<td><input class="x" type="textbox" name="name" value="<?php echo $dura['input']['name'] ?>" size="20" maxlength="10" /></td>

<td>
<select class="x0" name="limit">
<?php for ( $i = $dura['user_min']; $i <= $dura['user_max']; $i++ ): ?>
<option value="<?php echo $i ?>"<?php if ($dura['input']['limit'] == $i ) : ?> selected="selected"<?php endif ?>><?php echo $i ?></option>
<?php endfor ?>
</select>
</td>
</tr>

<tr style="position:fixed; top:99999%;">
<td>
<select name="language">
<?php foreach ( $dura['languages'] as $langcode => $language ): ?>
<option value="<?php e($langcode) ?>"<?php if ($langcode == Dura::user()->getLanguage() ) : ?> selected="selected"<?php endif ?>><?php e($language) ?></option>
<?php endforeach ?>
</select>
</td>
</tr>

<tr>
<td>
<span class="buttonc" style="margin: 15px 28px 0;">
<input type="submit" name="submit" value="创立！" class="input" />
</span>
</td>
</tr>
</table>

</form>
<center>
</div>
<div>