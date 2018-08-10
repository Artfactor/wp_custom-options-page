<?php
/*
Plugin Name: Custom Options Page
Description: Создает страницу настроек по вашим пожеланиям
Version: 0.1
Author: Aslanator
Author URI: vk.com/aslanator
Plugin URI: 
License: GPL2
*/

/* Copyright 2018 ASLAN ALUNKACHEV (email : aslanator@mail.ru)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA

*/

define('CUSTOM_OPTIONS_PAGE_DIR', plugin_dir_path(__FILE__));
define('CUSTOM_OPTIONS_PAGE_URL', plugin_dir_url(__FILE__)); 

add_action('admin_enqueue_scripts', 'custom_options_page_include');
function custom_options_page_include() {
    wp_register_style( 'basic_style', CUSTOM_OPTIONS_PAGE_URL.'css/style.css' );
    wp_enqueue_style( 'basic_style' );
    wp_enqueue_script( 'basic_script', CUSTOM_OPTIONS_PAGE_URL.'js/script.js', array( 'jquery' ) );
}

require(CUSTOM_OPTIONS_PAGE_DIR . '/include/options-page.php');


function add_options_menu_item() {
	add_options_page( 'Опции', 'Опции', 'manage_options', 'options-page', 'my_plugin_options');
}

function register_mysettings() {
  $options = getAllOptions();
  foreach($options as $key => $option){;
		$option = json_decode($option->option_value);
    register_setting( 'myoption-group', 'options_field_' . $option->name );
  }
  register_setting( 'myoption-group_new', 'new_option' );
}


function showOtionImg($optionName){
	$option = json_decode(get_option($optionName));
	$src = $noimage = get_template_directory_uri() . '/assets/images/noimage.jpg';
	if($option->value > 0)
		$src = wp_get_attachment_image_src($option->value)[0];	
	?>
		<table>
			<tr>
				<td><?echo '<img class="' . $optionName . '_img" src="' . $src . '" alt="' . $optionName . '">';?></td>
				<td>
					<input class="json_value" type="hidden" name="<?=$optionName?>" value="<?php echo esc_attr( get_option($optionName) ); ?>" />
					<input type="hidden" value="<?=$option->value?>" class="<?=$optionName?>_url text_value" />
				</td>
				<td><a href="#" class="<?=$optionName?>_upload">Загрузить</a></td>
				<td><a href="#" class="<?=$optionName?>_remove">Удалить</a></td>
			</tr>
		</table>
		
		
	  <script>
			jQuery(document).ready(function($) {
				$('.<?=$optionName?>_upload').click(function(e) {
					e.preventDefault();

					var custom_uploader = wp.media({
						title: '<?=$optionName?>',
						button: {
								text: 'Upload Image'
						},
						multiple: false  // Set this to true to allow multiple files to be selected
					})
					.on('select', function() {
						var attachment = custom_uploader.state().get('selection').first().toJSON();
						$('.<?=$optionName?>_img').attr('src', attachment.url);
						$('.<?=$optionName?>_url').val(attachment.id);
					})
					.open();
				});

				$('.<?=$optionName?>_remove').click(function(e) {
					e.preventDefault();

					$('.<?=$optionName?>_img').attr('src', '<?=$noimage?>');
					$('.<?=$optionName?>_url').val('');
				});

			});
		</script>
	<?
}


if ( is_admin() ){
	add_action( 'admin_init', 'register_mysettings' );
	add_action( 'admin_menu', 'add_options_menu_item' );
}

function getAllOptions(){
  return getOptionsStartWith('options_field_');
}

add_filter( 'pre_update_option_new_option', 'filter_pre_update_option_new_option', 10, 3 );
function filter_pre_update_option_new_option( $pre_option, $option, $default ){
	$value = json_decode($pre_option);
	if(!$value->name){
		return false;
	}
  update_option('options_field_'.$value->name, $pre_option);
	return $pre_option;
}


add_action( 'wp_ajax_remove_option', 'remove_option_callback' );
function remove_option_callback() {
	echo delete_option($_POST['name']);
	wp_die();
}

function getOptionsStartWith($start, $limit = false){
	global $wpdb;
	$sql = "SELECT * FROM wp_options WHERE option_name LIKE '$start%'";
	if($limit){
		$limit = (int) $limit;
		$sql .= " LIMIT $limit";
	}
	return $wpdb->get_results(  $sql );
}


function getOption($name){
	$options = getOptionsStartWith('options_field_'.$name, 1);
	if($options){
		try{
			$option = json_decode($option->option_value);
			return $option->value;
		}
		catch(Exception $e){
			return false;
		}
	}
	return false;
}


?>