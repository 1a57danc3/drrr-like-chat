jQuery(function($)
{
	var postAction = null;
	var getAction  = null;

	var formElement     = null;
	var textareaElement = null;
	var talksElement    = null;
	var membersElement  = null;
	var logoutElement   = null;
	var buttonElement   = null;
	var iconElement     = null;
	var menuElement     = null;
	var roomNameElement = null;
	var settingPannelElement = null;
	var userListElement = null;

	var lastMessage  = '';
	var lastUpdate   = 0;
	var isSubmitting = false;
	var isLoggedOut  = false;
	var isLoading    = false;
	var isShowingSettinPannel = false;

	var isUseAnime   = true;
	var isUseSound   = true;
	var isShowMember = false;

	var userId   = null;
	var userName = null;
	var userIcon = null;

	var messageLimit = 50;

	var construct = function()
	{
		var url = location.href.replace(/#/, '');

		if ( url.replace(/\?/, '') != url )
		{
			postAction = url+"&ajax=1";
		}
		else
		{
			postAction = url+"?ajax=1";
		}

		getAction  = duraUrl+'/ajax.php';

		formElement     = $("#message");
		textareaElement = $("#message textarea");
		talksElement    = $("#talks");
		membersElement  = $("#members");
		logoutElement   = $("input[name=logout]");
		buttonElement   = $("input[name=post]");
		iconElement     = $("dl.talk dt");
		menuElement     = $("ul.menu");
		roomNameElement = $("#room_name");
		settingPannelElement = $("#setting_pannel");
		userListElement = $("#user_list");

		userId   = trim($("#user_id").text());
		userName = trim($("#user_name").text());
		userIcon = trim($("#user_icon").text());

		messageMaxLength = 140;

		if ( typeof(GlobalMessageMaxLength) != 'undefined' )
		{
			messageMaxLength = GlobalMessageMaxLength;
		}

		appendEvents();
		separateMemberList();
		roundBaloons();
		showControllPanel();

		if ( useComet )
		{
			getMessages();
		}
		else
		{
			var timer = setInterval(function(){getMessagesOnce();}, 1500);
		}

		$.each($(".bubble"), addTail);
	}

	var appendEvents = function()
	{
		formElement.submit(submitMessage);
		textareaElement.keyup(enterToSubmit);
		logoutElement.click(logout);
		iconElement.click(addUserNameToTextarea);
		menuElement.find("li.sound").click(toggleSound);
		menuElement.find("li.member").click(toggleMember);
		menuElement.find("li.animation").click(toggleAnimation);
		menuElement.find("li.setting").click(toggleSettingPannel);
		settingPannelElement.find("input[name=save]").click(changeRoomName);
		settingPannelElement.find("input[name=handover]").click(handoverHost);
		settingPannelElement.find("input[name=ban]").click(banUser);
	}

	var submitMessage = function()
	{
		var message = textareaElement.val();
		message.replace(/[\r\n]+/g, "");

		if ( message.replace(/^[ \n]+$/, '') == '' )
		{
			if ( message.replace(/^\n+$/, '') == '' )
			{
				textareaElement.val('');
			}

			return false;
		}

		if ( isSubmitting )
		{
			return false;
		}

		var data = formElement.serialize();

		if ( message == lastMessage )
		{
			if ( confirm(t("Will you stop sending the same message? If you click 'Cancel' you can send it again.")) )
			{
				textareaElement.val('');
				return false;
			}
		}

		textareaElement.val('');
		isSubmitting = true;
		buttonElement.val(t("Sending..."));

		lastMessage  = message;

		if ( message.length - 1 > messageMaxLength )
		{
			message = message.substring(0, messageMaxLength)+"...";
		}

		writeSelfMessage(message);

		$.post(postAction, data,
			function()
			{
				isSubmitting = false;
				buttonElement.val(t("P O S T"));
			}
		);

		return false;
	}

	var getMessagesOnce = function()
	{
		if ( isLoading || isLoggedOut )
		{
			return;
		}

		isLoading = true;

		$.post(getAction+'?fast=1', {}, 
			function(data)
			{
				isLoading = false;
				updateProccess(data);
			}
		, 'xml');
	}


	var getMessages = function()
	{
		$.post(getAction+'?fast=1', {}, 
			function(data)
			{
				loadMessages();
				updateProccess(data);
			}
		, 'xml');
	}

	var loadMessages = function()
	{
		$.post(getAction, {}, 
			function(data)
			{
				loadMessages();
				updateProccess(data);
			}
		, 'xml');
	}

	var updateProccess = function(data)
	{
		var update = $(data).find('room > update').text() * 1;

		if ( lastUpdate == update || settingPannelElement.is(":visible") )
		{
			return;
		}

		lastUpdate = update;

		validateResult(data);
		writeRoomName(data);
		writeMessages(data);
		writeUserList(data);
		markHost(data);
	}

	var writeRoomName = function(data)
	{
		roomNameElement.text($(data).find('room > name').text());
	}

	var writeMessages = function(data)
	{
		$.each($(data).find("talks"), writeMessage);
	}

	var writeMessage = function()
	{
		var id = $(this).find("id").text();

		if ( $("#"+id).length > 0 )
		{
			return;
		}

		var uid     = trim($(this).find("uid").text());
		var name    = trim($(this).find("name").text());
		var message = trim($(this).find("message").text());
		var icon    = trim($(this).find("icon").text());
		var time    = trim($(this).find("time").text());

		name    = escapeHTML(name);
		message = escapeHTML(message);

		if ( uid == 0 || uid == '0' )
		{
			var content = '<div class="talk system" id="'+id+'">'+message+'</div>';
			talksElement.prepend(content);
		}
		else if ( uid != userId )
		{
			var content = '<dl class="talk '+icon+'" id="'+id+'">';
			content += '<dt>'+name+'</dt>';
			content += '<dd><div class="bubble">';
			content += '<p class="body">'+message+'</p>';
			content += '</div></dd></dl>';
			talksElement.prepend(content);
			effectBaloon();
		}

		weepMessages();
	}

	var writeUserList = function(data)
	{
		membersElement.find("li").remove();
		userListElement.find("li").remove();

		var total = $(data).find("users").length;
		membersElement.append('<li>('+total+')</li>');

		var host  = $(data).find("host").text();

		$.each($(data).find("users"), 
			function()
			{
				var name = $(this).find("name").text();
				var id   = $(this).find("id").text();
				var icon = $(this).find("icon").text();
				var hostMark = "";

				if ( host == id ) hostMark = " "+t("(host)");

				membersElement.append('<li>'+name+hostMark+'</li>');

				if ( host == id ) return;

				userListElement.append('<li>'+name+'</li>');
				userListElement.find("li:last").css({
					'background':'transparent url("'+duraUrl+'/css/icon_'+icon+'.png") center top no-repeat'
				}).attr('name', id).click(
					function()
					{
						if ( $(this).hasClass('select') )
						{
							userListElement.find("li").removeClass('select');
							settingPannelElement.find("input[name=handover], input[name=ban]").attr('disabled', 'disabled');
						}
						else
						{
							userListElement.find("li").removeClass('select');
							$(this).addClass('select');
							settingPannelElement.find("input[name=handover], input[name=ban]").removeAttr('disabled');
						}
					}
				);
			}
		);

		separateMemberList();
	}

	var writeSelfMessage = function(message)
	{
		var name    = escapeHTML(userName);
		var message = escapeHTML(message);

		var content = '<dl class="talk '+userIcon+'" id="'+userId+'">';
		content += '<dt>'+name+'</dt>';
		content += '<dd><div class="bubble">';
		content += '<p class="body">'+message+'</p>';
		content += '</div></dd></dl>';
		talksElement.prepend(content);
		effectBaloon();
		weepMessages();
	}

	var validateResult = function(data)
	{
		var error = $(data).find("error").text() * 1;

		if ( error == 0 || isLoggedOut )
		{
			return;
		}
		else if ( error == 1 )
		{
			isLoggedOut = true;
			alert(t("Session time out."));
		}
		else if ( error == 2 )
		{
			isLoggedOut = true;
			alert(t("Room was deleted."));
		}
		else if ( error == 3 )
		{
			isLoggedOut = true;
			alert(t("Login error."));
		}

		location.href = duraUrl;
	}

	var effectBaloon = function()
	{
		var thisBobble = $(".bubble .body:first");
		var thisBobblePrent = thisBobble.parent();
		var oldWidth  = thisBobble.width()+'px';
		var oldHeight = thisBobble.height()+'px';
		var newWidth  = ( 5 + thisBobble.width() ) +'px';
		var newHeight = ( 5 + thisBobble.height() ) +'px';

		ringSound();

		if ( !isUseAnime )
		{
			$.each(thisBobblePrent, addTail);
			$.each(thisBobble, roundBaloon);
			return;
		}

		$("dl.talk:first dt").click(addUserNameToTextarea);

		if ( !isIE() )
		{
			$.each(thisBobblePrent, addTail);

			thisBobblePrent.css({
				'opacity' : '0',
				'width': '0px',
				'height': '0px'
			});
			thisBobblePrent.animate({
				'opacity' : 1,
				'width': '22px',
				'height': '16px'
			}, 200, "easeInQuart");
		}

		thisBobble.css({
			'border-width' : '0px',
			'font-size' : '0px',
			'text-indent' : '-100000px',
			'opacity' : '0',
			'width': '0px',
			'height': '0px'
		});

		thisBobble.animate({ 
			'fontSize': "1em", 
			'borderWidth': "4px",
			'width': newWidth,
			'height': newHeight,
			'opacity': 1,
			'textIndent': 0
		}, 200, "easeInQuart", 
			function()
			{
				$.each(thisBobble, roundBaloon);

				if ( isIE() )
				{
					thisBobblePrent.animate({
						'width': thisBobblePrent.width() - 5 +"px"
					}, 100);
				}

				thisBobble.animate({
					'width': oldWidth,
					'height': oldHeight
				}, 100);
			}
		);
	}

	var ringSound = function()
	{
		if ( !isUseSound )
		{
			return;
		}

		if ( $(".beep_sound").length )
		{
			$(".beep_sound").remove();
		}

		if ( $("a#sound").length )
		{
			var soundUrl = $("a#sound").attr("href");

			try
			{
				$.sound.play(soundUrl);
			}
			catch(e)
			{
			}
		}
	}

	var escapeHTML = function(ch)
	{ 
		ch = ch.replace(/&/g,"&amp;");
		ch = ch.replace(/"/g,"&quot;");
		ch = ch.replace(/'/g,"&#039;");
		ch = ch.replace(/</g,"&lt;");
		ch = ch.replace(/>/g,"&gt;");
		return ch;
	}

	var enterToSubmit = function(e)
	{
		var content = textareaElement.val();
		if ( content != content.replace(/[\r\n]+/g, "") )
		{
			formElement.submit();
			return false;
		}
	}

	var logout = function()
	{
		isLoggedOut = true;

		$.post(postAction, {'logout':'logout'},
			function(result)
			{
				location.href = duraUrl;
			}
		);
	}

	var weepMessages = function()
	{
		if ( $(".talk").length > messageLimit )
		{
			while ( $(".talk").length > messageLimit )
			{
				$(".talk:last").remove();
			}
		}
	}

	var separateMemberList = function()
	{
		membersElement.find('li:not(:last)').each(
			function()
			{
				$(this).append(', ');
			}
		);
	}

	var addUserNameToTextarea = function()
	{
		var name = $(this).text();
		var text = textareaElement.val();
		textareaElement.focus();

		if ( text.length > 0 )
		{
			textareaElement.val(text+' @'+name);
		}
		else
		{
			textareaElement.val(text+'@'+name+' ');
		}
	}

	var trim = function(string)
	{
		string = string.replace(/^\s+|\s+$/g, '');
		return string;
	}

	var roundBaloons = function()
	{
		$("#talks dl.talk dd div.bubble p.body").each(roundBaloon);
	}

	var roundBaloon = function()
	{
		// IE 7 only... orz
		if ( !isIE() || !window.XMLHttpRequest || document.querySelectorAll )
		{
			return;
		}

		var width = $(this).width();
		var borderWidth = $(this).css('border-width');
		var padding = $(this).css('padding-left');
		var color = $(this).css('border-color');
		width = width + padding.replace(/px/, '') * 2;

		$(this).corner("round 10px cc:"+color)
		.parent().css({
				"background" : color,
				"padding" : borderWidth,
				"width" : width
			}).corner("round 13px");
	}

	var addTail = function()
	{
		if ( isIE() )
		{
			return;
		}

		var height = $(this).find(".body").height() + 30 + 8;
		var top = (Math.round((180 - height) / 2) + 23) * -1;
		var bgimg  = $(this).find(".body").css("background-image");
		var rand = Math.floor(Math.random()*2);
		var tailTop = "0px";

		if ( rand == 1 )
		{
			tailTop = "-17px";
		}

		top = top + 1;

		$(this).find(".body").css({"margin": "0 0 0 15px"});

		$(this).prepend('<div><div></div></div>')
		            .css({"margin":"-16px 0 0 0"});
		$(this).children("div").css({
			"position":"relative",
			"float":"left",
			"margin":"0 0 0 0",
			"top": "39px",
			"left": "-3px",
			"width":"24px",
			"height":"16px",
			"background":"transparent "+bgimg+" left "+top+"px repeat-x"
		});
		$(this).children("div").children("div").css({
			"width":"100%",
			"height":"100%",
			"background":"transparent url('"+duraUrl+"/css/tail.png') left "+tailTop+" no-repeat"
		});
	}

	var showControllPanel = function()
	{
		if ( isIE() )
		{
			isUseSound = false;
			isUseAnime = false;
		}

		menuElement.find("li:hidden:not(.setting)").show();
		var soundClass  = ( isUseSound ) ? "sound_on" : "sound_off" ;
		var memberClass = ( isShowMember ) ? "member_on" : "member_off" ;
		var animationClass = ( isUseAnime ) ? "animation_on" : "animation_off" ;
		menuElement.find("li.sound").addClass(soundClass);
		menuElement.find("li.member").addClass(memberClass);
		menuElement.find("li.animation").addClass(animationClass);
	}

	var toggleSound = function()
	{
		if ( isUseSound )
		{
			$(this).removeClass("sound_on");
			$(this).addClass("sound_off");
			isUseSound = false;
		}
		else
		{
			$(this).removeClass("sound_off");
			$(this).addClass("sound_on");
			isUseSound = true;
		}
	}

	var toggleMember = function()
	{
		if ( isShowMember )
		{
			$(this).removeClass("member_on");
			$(this).addClass("member_off");
			membersElement.slideUp("slow");
			isShowMember = false;
		}
		else
		{
			$(this).removeClass("member_off");
			$(this).addClass("member_on");
			membersElement.slideDown("slow");
			isShowMember = true;
		}
	}

	var toggleAnimation = function()
	{
		if ( isUseAnime )
		{
			$(this).removeClass("animation_on");
			$(this).addClass("animation_off");
			isUseAnime = false;
		}
		else
		{
			$(this).removeClass("animation_off");
			$(this).addClass("animation_on");
			isUseAnime = true;
		}
	}

	var toggleSettingPannel = function()
	{
		settingPannelElement.find("input[name=handover], input[name=ban]").attr('disabled', 'disabled');
		buttonElement.slideToggle();
		textareaElement.slideToggle();
		settingPannelElement.slideToggle();
	}

	var markHost = function(data)
	{
		if ( $(data).find('host').text() == userId )
		{
			menuElement.find("li.setting").show();
		}
		else
		{
			menuElement.find("li.setting").hide();
		}
	}

	var changeRoomName = function()
	{
		var roomName = settingPannelElement.find("input[name=room_name]").val();

		$.post(postAction, {'room_name':roomName}, 
			function(result)
			{
				alert(result);
				toggleSettingPannel();
			}
		);
	}

	var handoverHost = function()
	{
		var id = userListElement.find("li.select").attr("name");

		if ( confirm(t("Are you sure to handover host rights?")) )
		{
			$.post(postAction, {'new_host':id}, 
				function(result)
				{
					alert(result);
					toggleSettingPannel();
				}
			);
		}
	}

	var banUser = function()
	{
		var id = userListElement.find("li.select").attr("name");

		if ( confirm(t("Are you sure to ban this user?")) )
		{
			$.post(postAction, {'ban_user':id}, 
				function(result)
				{
					alert(result);
					toggleSettingPannel();
				}
			);
		}
	}

	var isIE = function()
	{
		var isMSIE = /*@cc_on!@*/false;
		return isMSIE;
	}

	var dump = function($val)
	{
		talksElement.prepend($val);
	}

	construct();
});