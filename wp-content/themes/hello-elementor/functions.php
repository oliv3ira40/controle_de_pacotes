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


/**
 * Inclui arquivo css ao tema
 * @return mixed
 */
function admin_style() {
	wp_enqueue_style('admin-geral', get_template_directory_uri().'/assets/admin.css');
}
add_action('admin_enqueue_scripts', 'admin_style');


/**
 * Inclui arquivo js ao tema
 * @return mixed
 */
function admin_script() {
	wp_enqueue_script('admin-geral', get_template_directory_uri().'/assets/admin.js');
}
add_action('admin_enqueue_scripts', 'admin_script');


/**
 * Função para debugar valores
 * @return mixed
 */
function dd(...$valores) {
	array_map(function ($valor) {
		echo '<pre>';
		print_r($valor);
	}, $valores);
	exit;
}


/**
 * Remove alguns grupos de usuários nátivos do wordpress
 * @return void
 */
remove_role('contributor');
remove_role('author');
remove_role('editor');
remove_role('subscriber');


// remove_role('autonomous');
/**
 * Registra um novo grupo de usuários "Autonomo"
 * @return void
 */
function insert_role_autonomous() {
    if (get_option('autonomous') < 1) {
        add_role(
			'autonomous',
			'Autônomo', [
				'read'            => true,
				'create_posts'      => true,
				'edit_posts'        => true,
				'edit_others_posts' => false,
				'publish_posts' => true,
				'manage_categories' => true,

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
/**
 * Registra um novo grupo de usuários "Cliente"
 * @return void
 */
function insert_role_clients() {
    if (get_option('clients') < 1) {
        add_role(
			'clients',
			'Cliente', [
				'read'            => true,
				'create_posts'      => true,
				'edit_posts'        => true,
				'edit_others_posts' => true,
				'publish_posts' => true,
				'manage_categories' => true,

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
 * Remove a funcionalidade de editar usando a opção de ação em massa dos pacotes
 * @return void
 */
function remove_bulk_editing_of_packages() {
	if (!current_user_can('bulk_packages')) {
		add_filter('bulk_actions-edit-packages', '__return_empty_array');
		add_filter('bulk_actions-upload', '__return_empty_array');
	}
}
add_action('wp_loaded', 'remove_bulk_editing_of_packages');


/**
 * Remove a checkbox para selecionar mútiplos posts da lista pacotes
 * @return array
 */
function removes_multiple_post_checkbox_from_packages($columns) {
	unset($columns['cb']);
	return $columns;
}
add_filter('manage_packages_posts_columns', 'removes_multiple_post_checkbox_from_packages');


/**
 * Remove campo de edição rápida da lista de pacotes
 * @return array
 */
function remove_quick_edit_from_package_list($actions, $post) { 
	if (get_post_type(get_the_ID()) == 'packages') {
		unset($actions['inline hide-if-no-js']);
	}
	return $actions;
}
add_filter('post_row_actions','remove_quick_edit_from_package_list',10,2);


/**
 * Adiciona permissões ao grupo "Administrador"
 * @return void
 */
function add_permissions_to_administrators() {
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
add_action('admin_init', 'add_permissions_to_administrators');


/**
 * Inicia o Carbon Fields - Campos personalizados
 */
require get_template_directory() . '/campos-personalizados/campos-personalizados.php';



/**
 * Retornar os usuários no grupo "Cliente"
 * @return object
 */
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
 * Filtrar posts da lista de pacotes de acordo com as permissões do usuários logado
 * @return object
 */
function filter_posts_from_package_list($query) {
	if (
		!(isset($_REQUEST['post_type']) AND $_REQUEST['post_type'] == 'packages') AND
		!(isset($_REQUEST['post']) AND get_post($_REQUEST['post'])->post_type == 'packages')
	) { return; }

	$current_user = wp_get_current_user();
	$role = $current_user->roles[0];

	$ordenar_por = $query->get('orderby');
	if (!$ordenar_por) {
		$query->set('meta_key', '_close_package');
		$query->set('orderby', '_close_package');
		$query->set('order', 'ASC');
	}

	if ($role === 'clients') {
		// CASO TENTER ACESSAR UM PACOTE VIA LINK E NAO ESTEJA INCLUIDO NO PACOTE
		if (isset($_REQUEST['action']) AND $_REQUEST['action'] == 'edit') {
			global $wpdb;
			$post_id = $_REQUEST['post'];
			$query_post = "
				SELECT meta_value as client_id FROM wp_postmeta
				WHERE post_id = $post_id
				AND meta_key LIKE '_clients%'
			";
			$clients = $wpdb->get_results($query_post);
			$clients = array_column($clients, 'client_id');
			
			if (!in_array($current_user->ID, $clients)) {
				wp_redirect('/wp-admin/edit.php?post_type=packages');
				exit;
			}
		}

		// FILTRO A LISTA DE PACOTES, EXIBINDO SOMENTO OS PACOTES QUE O USUÁRIO ESTA INCLUIDO
		if (!isset($_REQUEST['action'])) {
			$query->set('meta_query', [
				[
					'key' => 'clients',
					'compare' => 'EXISTS',
					'value' => $current_user->ID,
				]
			]);
		}
	} elseif ($role === 'autonomous') {
		// CASO TENTER ACESSAR UM PACOTE VIA LINK E NAO ESTEJA INCLUIDO NO PACOTE
		if (isset($_REQUEST['action']) AND $_REQUEST['action'] == 'edit') {
			$post_id = $_REQUEST['post'];
			if (get_post($post_id)->post_author != $current_user->ID) {
				wp_redirect('/wp-admin/edit.php?post_type=packages');
				exit;
			}
		}

		// FILTRO A LISTA DE PACOTES, EXIBINDO SOMENTO OS PACOTES QUE O USUÁRIO CRIOU
		if (!isset($_REQUEST['action'])) {
			$query->set('author', $current_user->ID);
		}
	} else { return; }
}
add_action('pre_get_posts', 'filter_posts_from_package_list', 10);


/**
 * Remove menus nátivos e adiciona novo menu "Todos" a página de lista de pacotes
 * @return void
 */
function update_package_post_list_menus($menu) {
	$current_user = wp_get_current_user();
	$role = $current_user->roles[0];

	if ($role != 'administrator') {
		unset($menu['all']);
		unset($menu['publish']);
		unset($menu['trash']);
	
		$args = [
			'numberposts'   => -1,
			'post_type'     => 'packages',
		];	
		$n_posts = count(get_posts($args));
	
		$menu['all'] = '
			<a href="edit.php?post_type=packages">
				Todos
				<span class="txt-dark">('.$n_posts.')</span>
			</a>
		';
	}
	return $menu;
}
add_filter('views_edit-packages', 'update_package_post_list_menus', 10, 1);


/**
 * Verifica se um cliente finalizou uma sessão e salva a data de termino
 * @return void
 */
add_action('pre_get_posts', 'checks_if_a_client_has_ended_a_session');
function checks_if_a_client_has_ended_a_session($query) {
	if (!isset($_REQUEST['post'])) {
		return;
	}

	$current_user = wp_get_current_user();
	$role = $current_user->roles[0];
	if (!(get_post($_REQUEST['post']) AND $role == 'clients')) {
		return;
	}
	
	$post_id = $_REQUEST['post'];
	$data_carbon = carbon_get_post_meta($post_id, 'sections_packages');
	foreach ($data_carbon as $key => $section) {
		if (
			(isset($section['confirm_client_termination']) AND $section['confirm_client_termination']) AND
			empty($section['closing_date_client'])
		) {
			$key_field = "_sections_packages|closing_date_client|$key|0|value";
			update_post_meta($post_id, $key_field, current_time('d/m/Y H:i'));
		}
	}
}


/**
 * Atualiza os titulos(nátivo e carbon fields) do pacote sempre que ele for atualizado
 * @return void
 */
function update_package_titles($value) {
	if (isset($_REQUEST['carbon_fields_compact_input']['_name'])) {
		return $_REQUEST['carbon_fields_compact_input']['_name'];
	}
}
add_filter('pre_post_title', 'update_package_titles');


/**
 * Verifica se um autonomo finalizou uma sessão e envia uma notificação para os clientes envolvidos no pacote
 * @return void
 */
add_action('pre_get_posts', 'checks_if_a_autonomous_ends_a_session');
function checks_if_a_autonomous_ends_a_session($query) {
	if (!isset($_REQUEST['post'])) {
		return;
	}

	$current_user = wp_get_current_user();
	$role = $current_user->roles[0];
	if (!(get_post($_REQUEST['post']) AND $role == 'autonomous')) {
		return;
	}

	$post_id = $_REQUEST['post'];
	$data_carbon = carbon_get_post_meta($post_id, 'sections_packages');
	$status = array_count_values(array_column($data_carbon, 'status'));
	$data_send = [
		'package_name' => carbon_get_post_meta($post_id, 'name'),
		'autonomous_name' => get_the_author_meta('display_name', get_post($post_id)->post_author),
		'package_link' => get_edit_post_link($post_id),
		'n_remaining_sessions' => $status['Não finalizada'] ?? 0,
	];

	foreach ($data_carbon as $key => $data_session) {
		if ($data_session['status'] == 'Finalizada') {
			if (
				(isset($data_session['confirm_autonomous_termination']) AND $data_session['confirm_autonomous_termination']) AND
				!empty($data_session['closing_date_autonomous']) AND
				empty($data_session['termination_notice_via_email'])
			) {
				$data_send['n_current_session'] = $key + 1;
				$data_send['closing_date_session'] = date_i18n('j \d\e F \d\e Y \á\s H:s\h',
					strtotime($data_session['closing_date_autonomous']));

				$clients = carbon_get_post_meta($post_id, 'clients');
				$result_send = false;
				foreach ($clients as $client_id) {
					$client = get_user_by('ID', $client_id);
					$client_email = $client->data->user_email;

					$data_send['client_email'] = $client_email;
					$result_send = notifies_client_end_of_session($data_send);
				};
				if ($result_send) {
					$key_field = "_sections_packages|termination_notice_via_email|$key|0|value";
					update_post_meta($post_id, $key_field, current_time('d/m/Y H:i'));
				}
			}
		}
	}
}


/**
 * Carrega o template do email em uma variável
 * @return string
 */
function load_template_part($template_name, $part_name=null, $args = null) {
    ob_start();
    get_template_part($template_name, $part_name, $args);
    $var = ob_get_contents();
    ob_end_clean();
    return $var;
}


/**
 * Envia um e-mail de notificação de termino de sessão para o cliente
 * @return integer
 */
function notifies_client_end_of_session($data_send) {
	$to = $data_send['client_email'];
	$subject = ucfirst($data_send['autonomous_name']).' - Sessão finalizada';
	$body = load_template_part('template-parts/end-of-session', 'teste', $data_send);
	$headers = ['Content-Type: text/html; charset=UTF-8'];

	return wp_mail($to, $subject, $body, $headers);
}


/**
 * Após o login redireciona qualquer usuário não "Administrador" para a página de pacotes
 * @return void
 */
function login_redirect_no_administrators($redirect_to, $request, $user) {
	if(isset($user->roles) AND $user->roles[0] != 'administrator') {
        return "/wp-admin/edit.php?post_type=packages";
    }
	return admin_url();
}
add_filter("login_redirect", "login_redirect_no_administrators", 10, 3);


/**
 * Exibi as colunas "Clientes", "Sessões finalizadas", "Sessões não finalizadas" e "Status" na lista de pacotes
 * @return void
 */
add_filter('manage_packages_posts_columns', function($columns) {
	$offset = array_search('date', array_keys($columns));
	return array_merge(
		array_slice($columns, 0, $offset),
		['_clients' => __('Clientes', 'textdomain')],
		['_finished_sessions' => __('Sessões finalizadas', 'textdomain')],
		['_not_finished_sessions' => __('Sessões não finalizadas', 'textdomain')],
		['_close_package' => __('Status', 'textdomain')],
		array_slice($columns, $offset, null)
	);
});


/**
 * Retorna os nomes dos clientes vinculados ao pacote
 * @return string
 */
function obter_nomes_clientes_do_pacotes($post_id) {
	$post = get_post($post_id);
	global $wpdb;
	$query_post = "
		SELECT meta_value as client_id FROM wp_postmeta
		WHERE post_id = $post->ID
		AND meta_key LIKE '_clients%'
	";
	$clients = $wpdb->get_results($query_post);
	$clients = array_column($clients, 'client_id');
	$clients_names = get_users(['include'=>$clients, 'role'=>'clients']);
	$clients_names = implode(', ', array_column($clients_names, 'display_name'));

	return $clients_names;
}


/**
 * Trata o resultado exibido na coluna "Clientes"
 * @return void
 */
add_action('manage_packages_posts_custom_column', function($column_key, $post_id) {
	if ($column_key != '_clients') return;

	$clients_names = obter_nomes_clientes_do_pacotes($post_id);
	if ($clients_names) {
		echo "<span class='txt-muted txt-bold'>$clients_names</span>";
	} else { echo '<span>—--</span>'; }
}, 10, 2);


/**
 * Trata o resultado exibido na coluna "Sessões finalizadas" e "Sessões não finalizadas"
 * @return void
 */
add_action('manage_packages_posts_custom_column', function($column_key, $post_id) {
	if ($column_key == '_finished_sessions' OR $column_key == '_not_finished_sessions') {
		$data_carbon = carbon_get_post_meta($post_id, 'sections_packages');
		$n_status_sessions = array_count_values(array_column($data_carbon, 'status'));

		if ($column_key == '_finished_sessions') {
			$n_status_session = $n_status_sessions['Finalizada'] ?? '---';
			echo "<span class='txt-muted txt-bold'>$n_status_session</span>";
		} else {
			$n_status_session = $n_status_sessions['Não finalizada'] ?? '---';
			echo "<span class='txt-muted txt-bold'>$n_status_session</span>";
		}
	};
}, 10, 2);


/**
 * Trata o resultado exibido na coluna "Status do pacote"
 * @return void
 */
add_action('manage_packages_posts_custom_column', function($column_key, $post_id) {
	if ($column_key != '_close_package') return;

	$status_package = carbon_get_post_meta($post_id, 'close_package');
	$data_carbon = carbon_get_post_meta($post_id, 'sections_packages');
	$n_status_sessions = array_count_values(array_column($data_carbon, 'status'));

	if (
		(isset($n_status_sessions['Finalizada']) AND $n_status_sessions['Finalizada'] > 0) AND
		(!isset($n_status_sessions['Não finalizada']) OR $n_status_sessions['Não finalizada'] == 0)
	) {
		if ($status_package) {
			echo "<span class='txt-muted txt-bold txt-success'>Pacote finalizado</span>";
		} else {
			echo "<span class='txt-muted txt-bold txt-info'>Aguardando finalização</span>";
		}
	} else {
		echo '<span class="txt-bold txt-warning">Pacote em andamento</span>';
	}
}, 10, 2);


/**
 * Adiciona opção de filtro na coluna "Clientes"
 * @return void
 */
add_filter('manage_edit-packages_sortable_columns', function($colunas) {
	$colunas['_close_package'] = '_close_package';
	return $colunas;
});


/**
 * Adiciona regra de ordenação para a coluna "Clientes"
 * @return void
 */
add_action('pre_get_posts', function($query) {
	if (!is_admin()) { return; }

	$ordenar_por = $query->get('orderby');
	if ($ordenar_por == '_close_package') {
		$query->set('meta_key', '_close_package');
		$query->set('orderby', '_close_package');
	}
}, 9);
