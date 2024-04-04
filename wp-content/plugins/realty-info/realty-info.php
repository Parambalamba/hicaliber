<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://parambala.com
 * @since             1.0.0
 * @package           Realty_Info
 *
 * @wordpress-plugin
 * Plugin Name:       Realty Info
 * Plugin URI:        https://realty-info.com
 * Description:       Adding a couple of post types to WP
 * Version:           1.0.0
 * Author:            Sergei Konovalov
 * Author URI:        https://parambala.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       realty-info
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'REALTY_INFO_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-realty-info-activator.php
 */
function activate_realty_info() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-realty-info-activator.php';
	require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	if (!is_plugin_active('advanced-custom-fields/acf.php'))
		set_transient( 'acf-active-admin-notice-activation', true, 5 );
	flush_rewrite_rules();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-realty-info-deactivator.php
 */
function deactivate_realty_info() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-realty-info-deactivator.php';
	Realty_Info_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_realty_info' );
register_deactivation_hook( __FILE__, 'deactivate_realty_info' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-realty-info.php';

function add_realty_object() {
	register_taxonomy( 'realty_type', [ 'realty' ], [
		'label'        => '',
		'labels'       => [
			'name'              => 'Типы недвижимости',
			'singular_name'     => 'Тип недвижимости',
			'search_items'      => 'Искать тип',
			'all_items'         => 'Все типы',
			'view_item '        => 'Смотреть тип',
			'parent_item'       => 'Родительский тип',
			'parent_item_colon' => 'Родительский Тип:',
			'edit_item'         => 'Редактировать тип',
			'update_item'       => 'Обновить тип',
			'add_new_item'      => 'Добавить новый тип',
			'new_item_name'     => 'Имя нового типа',
		],
		'description'  => '',
		'public'       => true,
		'hierarchical' => true,
		'rewrite'           => true,
		'capabilities'      => array(),
		'meta_box_cb'       => null,
		'show_admin_column' => true,
		'show_in_rest'      => null,
		'rest_base'         => null,
	] );

	register_post_type( 'realty', [
		'label'         => 'Недвижимость',
		'labels'        => [
			'name'               => 'Недвижимость',
			'singular_name'      => 'Объект',
			'add_new'            => 'Добавить объект',
			'add_new_item'       => 'Добавление объекта',
			'edit_item'          => 'Редактирование объекта',
			'new_item'           => 'Новый объект',
			'view_item'          => 'Смотреть объект',
			'search_items'       => 'Искать объект',
			'not_found'          => 'Не найдено',
			'not_found_in_trash' => 'Не найдено в корзине',
			'parent_item_colon'  => '',
		],
		'description'   => '',
		'public'        => true,
		'show_in_menu'  => null,
		'show_in_rest'  => null,
		'rest_base'     => null,
		'menu_position' => 4,
		'menu_icon'     => null,
		'hierarchical'  => false,
		'supports'      => [ 'title', 'editor', 'thumbnail' ],
		'taxonomies'    => [ 'realty_type' ],
		'has_archive'   => false,
		'rewrite'       => true,
		'query_var'     => true,
	] );

}
add_action( 'init', 'add_realty_object' );

function add_cities_object() {
	register_post_type( 'cities', [
		'label'         => 'Cities',
		'labels'        => [
			'name'               => 'Города',
			'singular_name'      => 'Город',
			'add_new'            => 'Добавить Город',
			'add_new_item'       => 'Добавление Города',
			'edit_item'          => 'Редактирование Города',
			'new_item'           => 'Новый Город',
			'view_item'          => 'Смотреть Город',
			'search_items'       => 'Искать Город',
			'not_found'          => 'Не найдено',
			'not_found_in_trash' => 'Не найдено в корзине',
			'parent_item_colon'  => '',
		],
		'description'   => '',
		'public'        => true,
		'show_in_menu'  => null,
		'show_in_rest'  => null,
		'rest_base'     => null,
		'menu_position' => 5,
		'menu_icon'     => null,
		'hierarchical'  => false,
		'supports'      => [ 'title', 'editor', 'thumbnail' ],
		'taxonomies'    => [],
		'has_archive'   => false,
		'rewrite'       => true,
		'query_var'     => true,
	] );
}
add_action( 'init', 'add_cities_object' );

require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
if ( is_plugin_active( 'advanced-custom-fields/acf.php' ) )
	add_action( 'acf/include_fields', 'realty_add_fields' );

function realty_add_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group( array(
		'key' => 'group_66040a2a45c26',
		'title' => 'Недвижимость',
		'fields' => array(
			array(
				'key' => 'field_66040a2a9fc68',
				'label' => 'Площадь',
				'name' => 'square',
				'aria-label' => '',
				'type' => 'number',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'default_value' => '',
				'min' => '',
				'max' => '',
				'placeholder' => '',
				'step' => '',
				'prepend' => '',
				'append' => '',
			),
			array(
				'key' => 'field_66040a7c9fc69',
				'label' => 'Стоимость',
				'name' => 'price',
				'aria-label' => '',
				'type' => 'number',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'default_value' => '',
				'min' => '',
				'max' => '',
				'placeholder' => '',
				'step' => '',
				'prepend' => '',
				'append' => '',
			),
			array(
				'key' => 'field_66040a919fc6a',
				'label' => 'Адрес',
				'name' => 'address',
				'aria-label' => '',
				'type' => 'textarea',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'default_value' => '',
				'maxlength' => '',
				'rows' => '',
				'placeholder' => '',
				'new_lines' => '',
			),
			array(
				'key' => 'field_66040aa79fc6b',
				'label' => 'Жилая площадь',
				'name' => 'living-square',
				'aria-label' => '',
				'type' => 'number',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'default_value' => '',
				'min' => '',
				'max' => '',
				'placeholder' => '',
				'step' => '',
				'prepend' => '',
				'append' => '',
			),
			array(
				'key' => 'field_66040ae39fc6c',
				'label' => 'Этаж',
				'name' => 'floor',
				'aria-label' => '',
				'type' => 'number',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'default_value' => '',
				'min' => '',
				'max' => '',
				'placeholder' => '',
				'step' => '',
				'prepend' => '',
				'append' => '',
			),
		),
		'location' => array(
			array(
				array(
					'param' => 'post_type',
					'operator' => '==',
					'value' => 'realty',
				),
			),
		),
		'menu_order' => 0,
		'position' => 'normal',
		'style' => 'default',
		'label_placement' => 'top',
		'instruction_placement' => 'label',
		'hide_on_screen' => '',
		'active' => true,
		'description' => '',
		'show_in_rest' => 0,
	) );

}

function disp_admin_notices() {
	if ( get_transient( 'acf-active-admin-notice-activation' ) ) {
		$html = '<div class="error">';
		$html .= '<p>';
		$html .= __('Для работы плагина, пожалуйста, установите или активируйте плагин Advanced Custom Fields');
		$html .= '</p>';
		$html .= '</div>';
		delete_transient( 'acf-active-admin-notice-activation' );
		echo $html;
	}
}
add_action( 'admin_notices', 'disp_admin_notices' );

add_action( 'add_meta_boxes', function () {
	add_meta_box( 'realty_object_city', 'Город', 'realty_object_city_metabox', 'realty', 'side', 'low');
}, 1 );

function realty_object_city_metabox( $post ) {
	$cities = get_posts( [ 'post_type' => 'cities', 'posts_per_page' => -1, 'order_by' => 'post_title', 'order' => 'ASC' ] );

	if ( $cities ) {
		echo '
		<div style="max-heigt: 200px; overflow-y: auto">
			<ul>	
		';

		foreach ( $cities as $city ) {
			echo '
				<li><label><input type="radio" name="post_parent" value="' . $city->ID . '" ' . checked( $city->ID, $post->post_parent, 0 ) . '> ' .  esc_html( $city->post_title ) . '
				</label></li>
			';
		}

		echo '
			</ul>
		</div>
		';
	} else
		echo 'Городов нет';
}

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_realty_info() {

	$plugin = new Realty_Info();
	$plugin->run();

}
run_realty_info();
