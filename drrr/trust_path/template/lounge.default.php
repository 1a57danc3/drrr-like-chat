</div>
<div id="pro">
<ul id="profile">
<li class="icon"><img src="<?php echo $dura['profile']['icon'] ?>" /></li>
<li class="name"><?php echo $dura['profile']['name'] ?></li>
<li class="logout">
<form action="<?php echo Dura::url('logout') ?>" method="post">

<?php if ( Dura::user()->isAdmin() ) : ?>
<a href="<?php e(Dura::url('admin_announce')) ?>" style="font:12px UbuntuMonoBold,'Microsoft YaHei'; color:#F90; text-decoration:none;"><?php e(t("Announce")) ?></a>
<?php endif ?>
<input type="submit" class="input" value="<?php e(t("LOGOUT")) ?>" />
</form>
</li>
</ul>

<div class="clear"></div>


<div class="header">
<h2><?php e(t("Lounge")) ?></h2>

<div class="right"><?php e(t("{1} users online!", $dura['active_user'])) ?></div>

<div class="clear"></div>


<div id="create_room">
<form action="<?php echo $dura['create_room_url'] ?>" method="post">
<span href="#" class="button"><input type="submit" class="input" value="<?php e(t("CREATE ROOM")) ?>" /></span>
</form>
</div>

<div class="clear"></div>

<?php foreach ( $dura['rooms'] as $rooms ) : ?>
<?php foreach ( $rooms as $room ) : ?>
<ul class="rooms">
<li class="name"><?php e($room['name']) ?></li>
<li class="creater"><?php echo $room['creater'] ?></li>
<li class="member"><?php e($room['total']) ?> / <?php e($room['limit']) ?></li>
<li class="login">
<?php if ( $room['total'] >= $room['limit'] ) : ?>
<?php e(t("full")) ?>
<?php else : ?>
<form action="<?php e($room['url']) ?>" method="post">
<span class="button">
<input type="submit" name="login" value="<?php e(t("LOGIN")) ?>" class="input" />
</span>
<input type="hidden" name="id" value="<?php e($room['id']) ?>" />
</form>
<?php endif ?>
</li>
</ul>
<?php endforeach ?>
<?php endforeach ?>

<div class="clear"></div>

</div>
</div>
</div>