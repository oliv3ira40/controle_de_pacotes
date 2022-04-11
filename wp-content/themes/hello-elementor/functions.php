<?php
/**
 * Theme functions and definitions
 *
 * @package HelloElementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'HELLO_ELEMENTOR_VERSION', '2.5.0' );

if ( ! isset( $content_width ) ) {
	$content_width = 800; // Pixels.
}

if ( ! function_exists( 'hello_elementor_setup' ) ) {
	/**
	 * Set up theme support.
	 *
	 * @return void
	 */
	function hello_elementor_setup() {
		if ( is_admin() ) {
			hello_maybe_update_theme_version_in_db();
		}

		$hook_result = apply_filters_deprecated( 'elementor_hello_theme_load_textdomain', [ true ], '2.0', 'hello_elementor_load_textdomain' );
		if ( apply_filters( 'hello_elementor_load_textdomain', $hook_result ) ) {
			load_theme_textdomain( 'hello-elementor', get_template_directory() . '/languages' );
		}

		$hook_result = apply_filters_deprecated( 'elementor_hello_theme_register_menus', [ true ], '2.0', 'hello_elementor_register_menus' );
		if ( apply_filters( 'hello_elementor_register_menus', $hook_result ) ) {
			register_nav_menus( [ 'menu-1' => __( 'Header', 'hello-elementor' ) ] );
			register_nav_menus( [ 'menu-2' => __( 'Footer', 'hello-elementor' ) ] );
		}

		$hook_result = apply_filters_deprecated( 'elementor_hello_theme_add_theme_support', [ true ], '2.0', 'hello_elementor_add_theme_support' );
		if ( apply_filters( 'hello_elementor_add_theme_support', $hook_result ) ) {
			add_theme_support( 'post-thumbnails' );
			add_theme_support( 'automatic-feed-links' );
			add_theme_support( 'title-tag' );
			add_theme_support(
				'html5',
				[
					'search-form',
					'comment-form',
					'comment-list',
					'gallery',
					'caption',
				]
			);
			add_theme_support(
				'custom-logo',
				[
					'height'      => 100,
					'width'       => 350,
					'flex-height' => true,
					'flex-width'  => true,
				]
			);

			/*
			 * Editor Style.
			 */
			add_editor_style( 'classic-editor.css' );

			/*
			 * Gutenberg wide images.
			 */
			add_theme_support( 'align-wide' );

			/*
			 * WooCommerce.
			 */
			$hook_result = apply_filters_deprecated( 'elementor_hello_theme_add_woocommerce_support', [ true ], '2.0', 'hello_elementor_add_woocommerce_support' );
			if ( apply_filters( 'hello_elementor_add_woocommerce_support', $hook_result ) ) {
				// WooCommerce in general.
				add_theme_support( 'woocommerce' );
				// Enabling WooCommerce product gallery features (are off by default since WC 3.0.0).
				// zoom.
				add_theme_support( 'wc-product-gallery-zoom' );
				// lightbox.
				add_theme_support( 'wc-product-gallery-lightbox' );
				// swipe.
				add_theme_support( 'wc-product-gallery-slider' );
			}
		}
	}
}
add_action( 'after_setup_theme', 'hello_elementor_setup' );

function hello_maybe_update_theme_version_in_db() {
	$theme_version_option_name = 'hello_theme_version';
	// The theme version saved in the database.
	$hello_theme_db_version = get_option( $theme_version_option_name );

	// If the 'hello_theme_version' option does not exist in the DB, or the version needs to be updated, do the update.
	if ( ! $hello_theme_db_version || version_compare( $hello_theme_db_version, HELLO_ELEMENTOR_VERSION, '<' ) ) {
		update_option( $theme_version_option_name, HELLO_ELEMENTOR_VERSION );
	}
}

if ( ! function_exists( 'hello_elementor_scripts_styles' ) ) {
	/**
	 * Theme Scripts & Styles.
	 *
	 * @return void
	 */
	function hello_elementor_scripts_styles() {
		$enqueue_basic_style = apply_filters_deprecated( 'elementor_hello_theme_enqueue_style', [ true ], '2.0', 'hello_elementor_enqueue_style' );
		$min_suffix          = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		if ( apply_filters( 'hello_elementor_enqueue_style', $enqueue_basic_style ) ) {
			wp_enqueue_style(
				'hello-elementor',
				get_template_directory_uri() . '/style' . $min_suffix . '.css',
				[],
				HELLO_ELEMENTOR_VERSION
			);
		}

		if ( apply_filters( 'hello_elementor_enqueue_theme_style', true ) ) {
			wp_enqueue_style(
				'hello-elementor-theme-style',
				get_template_directory_uri() . '/theme' . $min_suffix . '.css',
				[],
				HELLO_ELEMENTOR_VERSION
			);
		}
	}
}
add_action( 'wp_enqueue_scripts', 'hello_elementor_scripts_styles' );

if ( ! function_exists( 'hello_elementor_register_elementor_locations' ) ) {
	/**
	 * Register Elementor Locations.
	 *
	 * @param ElementorPro\Modules\ThemeBuilder\Classes\Locations_Manager $elementor_theme_manager theme manager.
	 *
	 * @return void
	 */
	function hello_elementor_register_elementor_locations( $elementor_theme_manager ) {
		$hook_result = apply_filters_deprecated( 'elementor_hello_theme_register_elementor_locations', [ true ], '2.0', 'hello_elementor_register_elementor_locations' );
		if ( apply_filters( 'hello_elementor_register_elementor_locations', $hook_result ) ) {
			$elementor_theme_manager->register_all_core_location();
		}
	}
}
add_action( 'elementor/theme/register_locations', 'hello_elementor_register_elementor_locations' );

if ( ! function_exists( 'hello_elementor_content_width' ) ) {
	/**
	 * Set default content width.
	 *
	 * @return void
	 */
	function hello_elementor_content_width() {
		$GLOBALS['content_width'] = apply_filters( 'hello_elementor_content_width', 800 );
	}
}
add_action( 'after_setup_theme', 'hello_elementor_content_width', 0 );

if ( is_admin() ) {
	require get_template_directory() . '/includes/admin-functions.php';
}

/**
 * If Elementor is installed and active, we can load the Elementor-specific Settings & Features
*/

// Allow active/inactive via the Experiments
require get_template_directory() . '/includes/elementor-functions.php';

/**
 * Include customizer registration functions
*/
function hello_register_customizer_functions() {
	if ( hello_header_footer_experiment_active() && is_customize_preview() ) {
		require get_template_directory() . '/includes/customizer-functions.php';
	}
}
add_action( 'init', 'hello_register_customizer_functions' );

if ( ! function_exists( 'hello_elementor_check_hide_title' ) ) {
	/**
	 * Check hide title.
	 *
	 * @param bool $val default value.
	 *
	 * @return bool
	 */
	function hello_elementor_check_hide_title( $val ) {
		if ( defined( 'ELEMENTOR_VERSION' ) ) {
			$current_doc = Elementor\Plugin::instance()->documents->get( get_the_ID() );
			if ( $current_doc && 'yes' === $current_doc->get_settings( 'hide_title' ) ) {
				$val = false;
			}
		}
		return $val;
	}
}
add_filter( 'hello_elementor_page_title', 'hello_elementor_check_hide_title' );

/**
 * Wrapper function to deal with backwards compatibility.
 */
if ( ! function_exists( 'hello_elementor_body_open' ) ) {
	function hello_elementor_body_open() {
		if ( function_exists( 'wp_body_open' ) ) {
			wp_body_open();
		} else {
			do_action( 'wp_body_open' );
		}
	}
}

















function admin_style() {
	wp_enqueue_style('admin-geral', get_template_directory_uri().'/assets/admin.css');
}
add_action('admin_enqueue_scripts', 'admin_style');



/**
 * Debug de valores
 * @return void
 */
function dd(...$valores) {
	array_map(function ($valor) {
		echo '<pre>';
		print_r($valor);
	}, $valores);
	exit;
}


remove_role('contributor');
remove_role('author');
remove_role('editor');
remove_role('subscriber');





// remove_role('autonomous');
// ADICIONA O GRUPO AUTONOMO
function insert_role_autonomous() {
    if (get_option('autonomous') < 1) {
        add_role(
			'autonomous',
			'Autônomo', [
				'read'            => true, // Allows a user to read
				'create_posts'      => true, // Allows user to create new posts
				'edit_posts'        => true, // Allows user to edit their own posts
				'edit_others_posts' => false, // Allows user to edit others posts too
				'publish_posts' => true, // Allows the user to publish posts
				'manage_categories' => true, // Allows user to manage post categories,

				'create_posts_packages' => true,
				'publish_packages' => true,
				'edit_packages' => true,
				'edit_others_packages' => false,
				'delete_packages' => true,
				'delete_others_packages' => false,
				'read_private_packages' => true,
				'edit_package' => true,
				'delete_package' => true,
				'read_package' => true,
			]
		);
        update_option('autonomous', 1);
    }
}
add_action('init', 'insert_role_autonomous');

// remove_role('clients');
// ADICIONA O GRUPO Cliente
function insert_role_clients() {
    if (get_option('clients') < 1) {
        add_role(
			'clients',
			'Cliente', [
				'read'            => true, // Allows a user to read
				'create_posts'      => true, // Allows user to create new posts
				'edit_posts'        => true, // Allows user to edit their own posts
				'edit_others_posts' => true, // Allows user to edit others posts too
				'publish_posts' => true, // Allows the user to publish posts
				'manage_categories' => true, // Allows user to manage post categories,

				'create_posts_packages' => false,
				'publish_packages' => true,
				'edit_packages' => true,
				'edit_others_packages' => true,
				'delete_packages' => false,
				'delete_others_packages' => false,
				'read_private_packages' => true,
				'edit_package' => true,
				'delete_package' => false,
				'read_package' => true,
			]
		);
        update_option('clients', 1);
    }
}
add_action('init', 'insert_role_clients');









/**
 * Registra o custom post type "Pacotes"
 * @return void
 */
function register_packages_post_type() {
	register_post_type('packages', array(
		'description' => 'packages-post-type',
		'exclude_from_search' => false,
		'public' => false,
		'show_ui' => true,
		'hierarchical' => false,
		'rewrite' => array('slug' => 'packages', 'with_front' => false),
		'supports' => [''],
		'has_archive' => 'packages',
		'menu_icon' => 'dashicons-admin-page',
		'capabilities' => [
			'create_posts' => 'create_posts_packages',
			'publish_posts' => 'publish_packages',
			'edit_posts' => 'edit_packages',
			'edit_others_posts' => 'edit_others_packages',
			'delete_posts' => 'delete_packages',
			'delete_others_posts' => 'delete_others_packages',
			'read_private_posts' => 'read_private_packages',
			'edit_post' => 'edit_package',
			'delete_post' => 'delete_package',
			'read_post' => 'read_package',
			'bulk_packages' => 'do_not_allow',
		],
		'labels' => array(
			'name' => 'Pacotes',
			'singular_name' => 'Pacotes',
			'search_items' => 'Pesquisar em Pacotes',
			'all_items' => 'Lista',
			'edit_item' => 'Editar',
			'upload_item' => 'Atualizar',
			'add_new' => 'Novo pacote',
			'add_new_item' => 'Adicionar novo pacote',
		),
		'rewrite' => array('slug' => 'packages'),
	));  
}
add_action('init', 'register_packages_post_type');

/**
 * Remove a funcionalidade de editar usando a opção de ação em massa
 * @return void
 */
function remove_bulk_actions_ar() {
	if (!current_user_can('bulk_packages')) {
		add_filter('bulk_actions-edit-packages', '__return_empty_array');
		add_filter('bulk_actions-upload', '__return_empty_array');
	}
}
add_action('wp_loaded', 'remove_bulk_actions_ar');

/**
 * Remove a checkbox para selecionar mútiplos posts
 * @return void
 */
function remove_checkbox_ar ($columns) {
	unset($columns['cb']);
	return $columns;
}
add_filter('manage_packages_posts_columns', 'remove_checkbox_ar');

/**
 * Remove campo de edição rápida
 * @return void
 */
function remove_edicao_rapida_ar($actions, $post) { 
	if (get_post_type(get_the_ID()) == 'packages') {
		unset($actions['inline hide-if-no-js']);
	}
	return $actions;
}
add_filter('post_row_actions','remove_edicao_rapida_ar',10,2);

/**
 * Adiciona as permissões de usuário
 * @return void
 */
function add_permissoes_packages() {
	$admins = get_role('administrator');
	$admins->add_cap('create_posts_packages');
	$admins->add_cap('publish_packages');
	$admins->add_cap('edit_packages');
	$admins->add_cap('edit_others_packages');
	$admins->add_cap('delete_packages');
	$admins->add_cap('delete_others_packages');
	$admins->add_cap('read_private_packages');
	$admins->add_cap('edit_package');
	$admins->add_cap('delete_package');
	$admins->add_cap('read_package');
}
add_action('admin_init', 'add_permissoes_packages');





/**
 * Campos personalizados
 */
require get_template_directory() . '/campos-personalizados/campos-personalizados.php';





function get_clients() {
	$args = [
		'role'    => 'clients',
		'orderby' => 'user_nicename',
		'order'   => 'ASC'
	];
	$users = get_users($args);
	return $users;
}









/**
 * Campos personalizados
 */
require get_template_directory() . '/campos-personalizados/campos-personalizados.php';