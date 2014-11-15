$(document).ready(function(){

	if($.cookie('username') =='' || typeof($.cookie("username")) == 'undefined')
	{
		// login_succ();
	}
	else
	{
		login_succ();
		my_navigation_url();
	}

	$(".login-a").click(function(){
		$('.reg-form-div').css('display','none');
		$('.login-form-div').css('display','');
		$(".reg-a").css('display','');
		$(this).css('display','none');
	});

	$(".reg-a").click(function(){
		$('.login-form-div').css('display','none');
		$('.reg-form-div').css('display','');
		$(".login-a").css('display','');
		$(this).css('display','none');
	});


	$("#login").click(function(){
		var username = $("#username").val();
		var password = $("#password").val();
		if(username == '' || typeof(username) == 'undefined')
		{
			$(".username-div").addClass('has-warning');
			$(".user-login-msg").removeClass('hide');
			$(".user-login-msg h6").html('用户名或密码错误');
			return false;
		}
		else
		{
			$(".username-div").removeClass('has-warning');
			$(".user-login-msg").addClass('hide');
		}
		
		if(password == '' || typeof(password) == 'undefined')
		{
			$(".password-div").addClass('has-warning');
			$(".user-login-msg").removeClass('hide');
			$(".user-login-msg h6").html('用户名或密码错误');
			return false;
		}
		else
		{
			$(".password-div").removeClass('has-warning');
			$(".user-login-msg").addClass('hide');
		}
	
		$.ajax({
			type: "POST",
			dataType:"json",
			url: "http://cikewang.com/index.php?p=navigation&c=default&a=user_login",
			data: {"username":username,"password":password},
			success: function(data){
				if (data.code < 0) 
				{
					$(".user-login-msg").removeClass('hide');
					$(".user-login-msg h6").html(data.msg);
				}
				else
				{
					$(".user-login-msg h6").removeClass('text-danger');
					$(".user-login-msg h6").addClass('text-primary');
					$(".user-login-msg").removeClass('hide');
					$(".user-login-msg h6").html(data.msg.username+',欢迎回来');
					$.cookie('username',data.msg.username);
					$.cookie("uid",data.msg._id);	
					my_navigation_url();
				
					setTimeout(function(i){
						login_succ();
						my_navigation_url();
						location.href = "http://cikewang.com/";
					},500);
					
				}
			}
		});		
	});

	$("#register-reg").click(function(){
		var reg_username = $("#reg_username").val();
		var reg_password = $("#reg_password").val();
		var auth_code = $("#auth_code").val();

		if (reg_username == '' || typeof(reg_username) == 'undefined') 
		{
			$(".username-reg-div").addClass('has-warning');
			$(".user-reg-msg").removeClass('hide');
			$(".user-reg-msg h6").html('请正确填写注册信息');
			return false;
		}
		else
		{
			$(".username-reg-div").removeClass('has-warning');
			$(".user-reg-msg").addClass('hide');
		}
		if (reg_password == '' || typeof(reg_password) == 'undefined') 
		{
			$(".password-reg-div").addClass('has-warning');
			$(".user-reg-msg").removeClass('hide');
			$(".user-reg-msg h6").html('请正确填写注册信息');
			return false;
		}
		else
		{
			$(".password-reg-div").removeClass('has-warning');
			$(".user-reg-msg").addClass('hide');
		}

		$.ajax({
			type: "POST",
			dataType:"json",
			url: "http://cikewang.com/index.php?p=navigation&c=default&a=user_reg",
			data: {"username":reg_username,"password":reg_password,"auth_code":auth_code},
			success: function(data){
				if(data.code < 0)
				{
					$(".user-reg-msg").removeClass('hide');
					$(".user-reg-msg h6").html(data.msg);
				}
				else
				{	
					$(".user-reg-msg h6").removeClass('text-danger');
					$(".user-reg-msg h6").addClass('text-primary');
					$(".user-reg-msg").removeClass('hide');
					$(".user-reg-msg h6").html(data.msg);

					setTimeout(function(i){
						$('.reg-form-div').css('display','none');
						$('.login-form-div').css('display','');
						$(".reg-a").css('display','');
						$(".login-a").css('display','none');
						$("#username").val("");
						$("#password").val("");
					},1500);

				}
			}
		});
	});

	$("#loginOut").click(function(){
		$.removeCookie('username');
		$.removeCookie('uid');
		$("#username").val('');
		$("#password").val('');
		$(".user-info-div").css("display","none");
		$(".user-login-reg-div").css("display","");
		$(".user-login-msg").addClass('hide');
		$(".user-login-msg h6").html('');
		$('.souchang-div').css('display','none');
	});

<!--////////////////////////////////////////////////////////////////////////////////////////////-->

	$("#souchang").click(function(){
		var category = $("#category").val();
		var web_name = $("#web_name").val();
		var web_url = $("#web_url").val();
		var web_icon_url = $("#web_icon_url").val();


		if ($.cookie("uid") == '' || typeof($.cookie("uid")) == 'undefined') 
		{
			$(".sc-msg").removeClass('hide');
			$(".sc-msg h6").html('用户没有登录');
			return false;
		};

		$.ajax({
			type: "POST",
			dataType:"json",
			url: "http://cikewang.com/index.php?p=navigation&c=default&a=add",
			data: {"uid":$.cookie("uid"),"web_url":web_url,"web_name":web_name,'web_icon_url':web_icon_url,"cate_name":category},
			success: function(data){
				if (data.code < 0) 
				{
					$(".sc-msg").removeClass('hide');
					$(".sc-msg h6").html(data.msg);
				}
				else
				{
					$(".sc-msg h6").removeClass('text-danger');
					$(".sc-msg h6").addClass('text-primary');
					$(".sc-msg").removeClass('hide');
					$(".sc-msg h6").html(data.msg);
				}
				setTimeout(function(i){
					$(".sc-msg").addClass('hide');
				},3000);
			}
		});
	});


});

function login_succ()
{
	$('.user-info-div').css('display','');
	$(".user-login-reg-div").css('display','none');
	$('.user-info-div .u').html($.cookie("username")+",欢迎回来");
	$('.souchang-div').css('display','');
}

function my_navigation_url()
{
	url = "http://cikewang.com/"+$.cookie("uid");
	$("#mynavigation").attr("href",url);
}
