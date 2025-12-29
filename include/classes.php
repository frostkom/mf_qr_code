<?php

class mf_qr_code
{
	var $post_type = __CLASS__;

	function __construct(){}

	function cron_base()
	{
		$obj_cron = new mf_cron();
		$obj_cron->start(__CLASS__);

		if($obj_cron->is_running == false)
		{
			// Delete old uploads
			#######################
			list($upload_path, $upload_url) = get_uploads_folder($this->post_type, true, false);

			if($upload_path != '')
			{
				get_file_info(array('path' => $upload_path, 'callback' => 'delete_files_callback', 'time_limit' => WEEK_IN_SECONDS));
				get_file_info(array('path' => $upload_path, 'folder_callback' => 'delete_empty_folder_callback'));
			}
			#######################
		}

		$obj_cron->end();
	}

	function get_html($post_id, $generate_image = true)
	{
		$out = "";

		if(!class_exists('QRcode'))
		{
			include_once("phpqrcode/qrlib.php");
		}

		$post_url = get_permalink($post_id);

		list($upload_path_qr, $upload_url_qr) = get_uploads_folder($this->post_type);

		$qr_file = "qr_code_".md5($post_url).".svg";

		if(file_exists($upload_path_qr.$qr_file))
		{
			$out = "<a href='".$upload_url_qr.$qr_file."'><i class='fas fa-qrcode fa-2x green' title='".__("Created", 'lang_qr_code')."'></i></a>";
		}

		$qr_file = "qr_code_".md5($post_url).".png";

		if(file_exists($upload_path_qr.$qr_file))
		{
			$out = "<a href='".$upload_url_qr.$qr_file."'><i class='fas fa-qrcode fa-2x green' title='".__("Created", 'lang_qr_code')."'></i></a>";
		}

		else if($generate_image == true)
		{
			$qr_file = "qr_code_".md5($post_url).".svg";

			ob_start();

			QRcode::svg($post_url, false, QR_ECLEVEL_H, 5, 7); // L/M/Q/H

			$svg = ob_get_contents();
			ob_end_clean();

			$site_icon = get_option('site_icon');

			if($site_icon > 0)
			{
				list($upload_path, $upload_url) = get_uploads_folder();

				$logo_file = get_post_field('guid', $site_icon);

				$size = 50;
				$position = 113;

				$logoSvg = "<image href='".$logo_file."' x='".$position."' y='".$position."' width='".$size."' height='".$size."'/>";

				$pos = strripos($svg, '</svg>');
				$svg = substr_replace($svg, $logoSvg . '</svg>', $pos, 6);
			}

			$success = set_file_content(array('file' => $upload_path_qr.$qr_file, 'mode' => 'w', 'content' => $svg));

			if($success)
			{
				$out = "<a href='".$upload_url_qr.$qr_file."'><i class='fas fa-qrcode fa-2x green' title='".__("Created", 'lang_qr_code')."'></i></a>";
			}

			/*QRcode::png($post_url, $upload_path_qr.$qr_file, QR_ECLEVEL_H, 5, 7); // L/M/Q/H

			$site_icon = get_option('site_icon');

			if($site_icon > 0)
			{
				list($upload_path, $upload_url) = get_uploads_folder();

				$logo_file = str_replace($upload_url, $upload_path, get_post_field('guid', $site_icon));
				$logo_fraction = 6;
				$logo_padding = 2;

				$qr_image = imagecreatefrompng($upload_path_qr.$qr_file);

				// Create white background for logo
				##########################
				$qr_size = imagesx($qr_image);
				$logo_size = ($qr_size / $logo_fraction - 2 * $logo_padding); // Logo size calculated from QR code size
				$bg_size = ($qr_size / $logo_fraction);

				$white_bg = imagecreatetruecolor($bg_size, $bg_size);
				$white = imagecolorallocate($white_bg, 255, 255, 255);
				imagefill($white_bg, 0, 0, $white);
				##########################

				if(exif_imagetype($logo_file) === IMAGETYPE_PNG)
				{
					// Load & resize logo
					##########################
					$logo = imagecreatefrompng($logo_file);
					$logo_width = imagesx($logo);
					$logo_height = imagesy($logo);

					// Create a new image with white background
					$white_logo_bg = imagecreatetruecolor($logo_width, $logo_height);
					$white_logo = imagecolorallocate($white_logo_bg, 255, 255, 255);
					imagefill($white_logo_bg, 0, 0, $white_logo);

					// Copy the logo onto the white background
					imagecopy($white_logo_bg, $logo, 0, 0, 0, 0, $logo_width, $logo_height);
					$logo = $white_logo_bg;

					$logo_resized = imagecreatetruecolor($logo_size, $logo_size);
					imagecopyresampled($logo_resized, $logo, 0, 0, 0, 0, $logo_size, $logo_size, imagesx($logo), imagesy($logo));
					##########################

					// Center logo on white background
					$logo_pos = $logo_padding;
					imagecopy($white_bg, $logo_resized, $logo_pos, $logo_pos, 0, 0, imagesx($logo_resized), imagesy($logo_resized));

					// Calculate position to center logo on QR code
					$logo_qr_pos = (($qr_size - $bg_size) / 2);

					// Merge logo with white background onto QR code
					imagecopy($qr_image, $white_bg, $logo_qr_pos, $logo_qr_pos, 0, 0, $bg_size, $bg_size);
				}

				// Save the final image
				imagepng($qr_image, $upload_path_qr.$qr_file);

				// Clean up
				imagedestroy($qr_image);

				if(exif_imagetype($logo_file) === IMAGETYPE_PNG)
				{
					imagedestroy($logo);
					imagedestroy($logo_resized);
				}

				imagedestroy($white_bg);
				imagedestroy($white_logo_bg);
			}

			$out = "<a href='".$upload_url_qr.$qr_file."'><i class='fas fa-qrcode fa-2x green' title='".__("Created", 'lang_qr_code')."'></i></a>";*/
		}

		return $out;
	}

	function init()
	{
		load_plugin_textdomain('lang_qr_code', false, str_replace("/include", "", dirname(plugin_basename(__FILE__)))."/lang/");
	}

	function admin_init()
	{
		global $pagenow;

		if($pagenow == 'edit.php' && check_var('post_type') == 'page')
		{
			do_action('load_font_awesome');

			$plugin_include_url = plugin_dir_url(__FILE__);

			mf_enqueue_script('script_qr_code_wp', $plugin_include_url."script_wp.js", array(
				'ajax_url' => admin_url('admin-ajax.php'),
				'loading_animation' => apply_filters('get_loading_animation', ''),
			));
		}
	}

	function column_header($columns)
	{
		if(check_var('post_status') != 'trash')
		{
			do_action('load_font_awesome');

			$columns['qr_code'] = __("QR", 'lang_theme_core');
		}

		return $columns;
	}

	function column_cell($column, $post_id)
	{
		global $post;

		switch($post->post_type)
		{
			case $this->post_type:
				switch($column)
				{
					case 'qr_code':
						if(get_post_status($post_id) == 'publish')
						{
							$qr_html = $this->get_html($post_id, false);

							if($qr_html != '')
							{
								echo $qr_html;
							}

							else
							{
								echo "<a href='#' class='api_qr_code_image' rel='".$post_id."'>
									<i class='fas fa-qrcode fa-2x blue' title='".__("Create", 'lang_qr_code')."'></i>
								</a>";
							}
						}
					break;
				}
			break;
		}
	}

	function api_qr_code_image()
	{
		$json_output = array(
			'success' => false,
		);

		$post_id = check_var('post_id');

		if($post_id > 0)
		{
			$json_output['success'] = true;
			$json_output['html'] = $this->get_html($post_id);
		}

		else
		{
			$json_output['error'] = __("I could not create the QR code for you", 'lang_qr_code');
		}

		header("Content-Type: application/json");
		echo json_encode($json_output);
		die();
	}
}