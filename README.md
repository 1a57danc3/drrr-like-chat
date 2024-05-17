drrr-like-chat
==============

无头骑士异闻录  聊天室


以下是原作者的说明       本项目由@FireAwayH要努力 修改

Revise: Crow
Revise URL: http://particularly.me/
2012.11

版本修改说明：
功能上没动，反而我去掉了一些东西，其中有一点是保留了房主提示。
修改了默认的一些数字参数，例如人数房数。
大体上是在做美工。移动设备分辨率自适应。总体分为大，中，小屏幕。
CN访问谷歌的问题JS库里需要的文件已搬到本地，不然加载飞慢且掉线。
但是房主功能被我擦掉了（你可以在原中文版里找到并按照你的方式修改进去）。
我试了下，但是没做到关闭浏览器窗口等于logout动作，因此非正常关闭窗口依旧需要等待断线。
如果你修改了退出动作，教我吧！

必须修改：
1.根目录setting.php文件，第12行http://localhost修改为您的地址，勿在结尾斜杠，否则地址多出1/。

其它修改：
1.trust_path/language/zh-CN.php文件第50和51行为标题。（语言文件）
2.trust_path/template/theme.php文件第6和7行为关键字与描述，第8行为ico文件URL。

其它：
声音文件位于js/sound.mp3
管理员地址位于登入页面左下角，它为10px大小的.隐藏存在，或者http://localhost/index.php?controller=admin

setting.php文件的一些描述：
define('DURA_USER_MIN', 3); 房间最小人数
define('DURA_USER_MAX', 15); 房间最大人数
define('DURA_ROOM_LIMIT', 10); 最大房间数
define('DURA_SITE_USER_CAPACITY', 150); 最大总人数
等。

不能创建房间的 trust_path下新建一个名为xml的文件夹 权限777 （bae sae可以省略权限）即可

生活愉快~

Crow
2012.11.01
