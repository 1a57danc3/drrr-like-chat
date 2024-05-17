<!DOCTYPE html>
<html dir="ltr" lang="zh-CN">
<head>
<meta charset="UTF-8">
<meta http-equiv="refresh" content="3;URL=<?php echo $url ?>"> 
<title><?php e(t(DURA_SUBTITLE)) ?></title>
<link href="<?php echo DURA_URL; ?>/css/style.css" rel="stylesheet" type="text/css" media="screen" />
</head>
<body>
<div id="body">
<div class="header">
<p><?php echo $message ?></p>
<p><?php e(t('If auto reload doesn\'t work,  please click <a href="{1}">here</a>.', $url)) ?></p>
</div>
</div>
</body>
</html>