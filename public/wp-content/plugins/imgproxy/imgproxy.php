<?php

/*
Plugin Name: imgproxy
Description: Dynamic image resizing
Author: manGoweb / Mikulas Dite
Version: 1.0
Author URI: https://www.mangoweb.cz
*/

add_action( 'plugins_loaded', 'imgproxy_init' );

function imgproxy_init() {
	$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : NULL;
	if (in_array($action, ['imgedit-preview', 'image-editor'], TRUE)) {
		return;
	}

//	add_filter('intermediate_image_sizes_advanced', 'imgproxy_hijack_sizes', 50, 2);
	add_filter('wp_image_editors', 'imgproxy_noop_editor', 50, 1);
//	add_filter('upload_dir', 'imgproxy_upload_dir');
//	add_filter('wp_get_attachment_url', 'improxy_get_attachment_url', 99, 2 );
	add_filter('image_downsize', 'imgproxy_image_downsize', 99, 3 );
}

///**
// * Filters the image sizes automatically generated when uploading an image.
// *
// * @since 2.9.0
// * @since 4.4.0 Added the `$metadata` argument.
// *
// * @param array $sizes    An associative array of image sizes.
// * @param array $metadata An associative array of image metadata: width, height, file.
// */
//function imgproxy_hijack_sizes($sizes, $metadata) {
//	// disable multi_resize call in wp-admin/includes/image.php:143
//	return [];
//}

function imgproxy_image_downsize($param, $id, $size = 'medium') {
	if ($size === 'full') {
		return false;
	}

	// url, width, height, ?
	flog(__FUNCTION__, func_get_args());

	// get dimensions for requested size
	$width = get_option( "${size}_size_w" );
	$height = get_option( "${size}_size_h" );

	// get original url
	$url = wp_get_attachment_image_url($id, 'full', false);
	flog($url);

	return [improxy_url($url, $width, $height), $width, $height, true];
}

function improxy_url($url, $width, $height) {
	$key = '0aa4c34cb6636fb8d4deacd150b1c7b4';
	$salt = '20030cf6ef8fc43168d77a7c05f4cd31';
	$keyBin = pack("H*" , $key);
	if(empty($keyBin)) {
		die('Key expected to be hex-encoded string');
	}
	$saltBin = pack("H*" , $salt);
	if(empty($saltBin)) {
		die('Salt expected to be hex-encoded string');
	}
	$resize = 'fill';
	$gravity = 'no';
	$enlarge = 1;
	$extension = 'jpg';
	$encodedUrl = rtrim(strtr(base64_encode($url), '+/', '-_'), '=');
	$path = sprintf("/%s/%d/%d/%s/%d/%s.%s", $resize, $width, $height, $gravity, $enlarge, $encodedUrl, $extension);
	$signature = rtrim(strtr(base64_encode(hash_hmac('sha256', $saltBin.$path, $keyBin, true)), '+/', '-_'), '=');
	return 'https://imgproxy.mangoweb.org' . sprintf("/%s%s", $signature, $path);
}

function improxy_get_attachment_url($url, $post_id) {
	flog(__FUNCTION__, func_get_args());
	return 'https://example.com/foo/image.jpg';
}

function imgproxy_upload_dir($dirs) {
	flog(__FUNCTION__, $dirs, xdebug_get_function_stack());
	return $dirs;
}

function imgproxy_noop_editor($editors) {
	flog(__FUNCTION__);
	return ['WP_Image_Editor_Noop'];
}

function flog() {
	$raw = json_encode(func_get_args());
	file_put_contents('/dev/ttys008', date('Y-m-d H:i:s') . ": $raw\n", FILE_APPEND);
}

require_once ABSPATH . WPINC . '/class-wp-image-editor.php';

class WP_Image_Editor_Noop extends WP_Image_Editor {

	public static function test($args = array())
	{
		return true;
	}

	public static function supports_mime_type($mime_type)
	{
		return true;
	}


	/**
	 * Loads image from $this->file into editor.
	 *
	 * @since 3.5.0
	 *
	 * @return bool|WP_Error True if loaded; WP_Error on failure.
	 */
	public function load()
	{
		flog('loaded');
		// TODO: Implement load() method.
	}

	/**
	 * Saves current image to file.
	 *
	 * @since 3.5.0
	 *
	 * @param string $destfilename
	 * @param string $mime_type
	 * @return array|WP_Error {'path'=>string, 'file'=>string, 'width'=>int, 'height'=>int, 'mime-type'=>string}
	 */
	public function save($destfilename = null, $mime_type = null)
	{
		// TODO: Implement save() method.
	}

	/**
	 * Resizes current image.
	 *
	 * At minimum, either a height or width must be provided.
	 * If one of the two is set to null, the resize will
	 * maintain aspect ratio according to the provided dimension.
	 *
	 * @since 3.5.0
	 *
	 * @param  int|null $max_w Image width.
	 * @param  int|null $max_h Image height.
	 * @param  bool $crop
	 * @return bool|WP_Error
	 */
	public function resize($max_w, $max_h, $crop = false)
	{
		flog(__METHOD__, $max_w, $max_h, $crop);
		// TODO: Implement resize() method.
	}

	/**
	 * Resize multiple images from a single source.
	 *
	 * @since 3.5.0
	 *
	 * @param array $sizes {
	 *     An array of image size arrays. Default sizes are 'small', 'medium', 'large'.
	 *
	 * @type array $size {
	 * @type int $width Image width.
	 * @type int $height Image height.
	 * @type bool $crop Optional. Whether to crop the image. Default false.
	 *     }
	 * }
	 * @return array An array of resized images metadata by size.
	 */
	public function multi_resize($sizes)
	{
		flog(__METHOD__, $sizes);

		$return = [];
		foreach ($sizes as $size => $info) {
			$return[$size] = [
				'path' => 'http://path' . $this->file,
				'file' => 'http://file' . $this->file,
				'width' => $info['width'],
				'height' => $info['height'],
				'mime-type' => $this->mime_type,
			];
		}
		return $return;
	}

	/**
	 * Crops Image.
	 *
	 * @since 3.5.0
	 *
	 * @param int $src_x The start x position to crop from.
	 * @param int $src_y The start y position to crop from.
	 * @param int $src_w The width to crop.
	 * @param int $src_h The height to crop.
	 * @param int $dst_w Optional. The destination width.
	 * @param int $dst_h Optional. The destination height.
	 * @param bool $src_abs Optional. If the source crop points are absolute.
	 * @return bool|WP_Error
	 */
	public function crop($src_x, $src_y, $src_w, $src_h, $dst_w = null, $dst_h = null, $src_abs = false)
	{
		// TODO: Implement crop() method.
	}

	/**
	 * Rotates current image counter-clockwise by $angle.
	 *
	 * @since 3.5.0
	 *
	 * @param float $angle
	 * @return bool|WP_Error
	 */
	public function rotate($angle)
	{
		// TODO: Implement rotate() method.
	}

	/**
	 * Flips current image.
	 *
	 * @since 3.5.0
	 *
	 * @param bool $horz Flip along Horizontal Axis
	 * @param bool $vert Flip along Vertical Axis
	 * @return bool|WP_Error
	 */
	public function flip($horz, $vert)
	{
		// TODO: Implement flip() method.
	}

	/**
	 * Streams current image to browser.
	 *
	 * @since 3.5.0
	 *
	 * @param string $mime_type The mime type of the image.
	 * @return bool|WP_Error True on success, WP_Error object or false on failure.
	 */
	public function stream($mime_type = null)
	{
		// TODO: Implement stream() method.
	}
}
