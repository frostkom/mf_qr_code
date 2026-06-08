(function()
{
	var el = wp.element.createElement,
		registerBlockType = wp.blocks.registerBlockType,
		InspectorControls = wp.blockEditor.InspectorControls;

	registerBlockType('mf/qrcode',
	{
		title: script_qr_code_block_wp.block_title,
		description: script_qr_code_block_wp.block_description,
		icon: 'superhero',
		category: 'widgets',
		'attributes':
		{
			'align':
			{
				'type': 'string',
				'default': ''
			}
		},
		'supports':
		{
			'html': false,
			'multiple': false,
			'align': true,
			'spacing':
			{
				'margin': true,
				'padding': true
			},
			'color':
			{
				'background': true,
				'gradients': false,
				'text': true
			},
			'defaultStylePicker': true,
			'typography':
			{
				'fontSize': true,
				'lineHeight': true
			},
			"__experimentalBorder":
			{
				"radius": true
			}
		},
		edit: function(props)
		{
			return el(
				'div',
				{className: 'wp_mf_block_container'},
				[
					el(
						'strong',
						{className: props.className},
						script_qr_code_block_wp.block_title
					)
				]
			);
		},
		save: function()
		{
			return null;
		}
	});
})();