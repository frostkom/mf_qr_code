jQuery(function($)
{
	var dom_obj = $(".widget.qr_code");

	$(document).on('click', "button[name='btnQRCodeRun']", function(e)
	{
		dom_obj.find(".api_qr_code_image").html(script_qr_code.loading_animation);

		$.ajax(
		{
			url: script_qr_code.ajax_url,
			type: 'post',
			dataType: 'json',
			data: {
				action: 'api_qr_code_image',
				post_url: dom_obj.find(".form_textfield input").val(),
				output_type: 'image',
			},
			success: function(data)
			{
				if(data.success)
				{
					dom_obj.find(".api_qr_code_image").html(data.html);
				}

				else
				{
					dom_obj.find(".api_qr_code_image").html("<i class='fas fa-qrcode fa-2x red' title='" + data.error + "'></i>");
				}
			}
		});

		return false;
	});
});