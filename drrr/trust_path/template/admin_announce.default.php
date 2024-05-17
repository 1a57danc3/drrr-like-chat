</div>
<div id="login" class="header" style="margin-top:30px;">
<h2><?php e(t("Admin Announce")) ?></h2>
<form action="#" method="post" id="message">
<textarea name="message"></textarea>
<div class="submit">
<input type="submit" name="post" value="POST!" />
</div>
</form>
</div>

<div id="talks">
<?php foreach ( $dura['talks'] as $time ) foreach ( $time as $talk ) : ?>
<?php if ( !$talk['uid'] ) : ?>
<div class="talk system" id="<?php e($talk['id']) ?>"><?php e($talk['message']) ?></div>
<?php else: ?>
<dl class="talk <?php e($talk['icon']) ?>" id="<?php e($talk['id']) ?>">
<dt><?php e($talk['name']) ?></dt>
<dd>
	<div class="bubble">
		<p class="body"><?php e($talk['message']) ?></p>
	</div>
</dd>
</dl>
<?php endif ?>
<?php endforeach ?>
</div>
<div>