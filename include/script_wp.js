jQuery(function($)
{
	$(".column-qr_code .api_qr_code_image[rel!='']").on('click', function()
	{
		var dom_obj = $(this);

		dom_obj.html(script_qr_code_wp.loading_animation);

		$.ajax(
		{
			url: script_qr_code_wp.ajax_url,
			type: 'post',
			dataType: 'json',
			data: {
				action: 'api_qr_code_image',
				post_id: dom_obj.attr('rel')
			},
			success: function(data)
			{
				if(data.success)
				{
					dom_obj.parent(".column-qr_code").html(data.html);
				}

				else
				{
					dom_obj.html("<i class='fas fa-qrcode fa-2x red' title='" + data.error + "'></i>");
				}
			}
		});

		return false;
	});
});