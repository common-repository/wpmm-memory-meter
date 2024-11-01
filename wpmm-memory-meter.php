<?php

/**
 * Plugin name: Memory Meter
 * Plugin URI: https://wordpress.org/plugins/wpmm-memory-meter/
 * Description: WordPress plugin by 99w.
 * Author: 99w
 * Author URI: https://99w.co.uk
 * Developer: 99w
 * Developer URI: https://99w.co.uk
 * Version: 2.1.0
 * Requires at least: 6.4.0
 * Requires PHP: 7.4.0
 * Requires plugins:
 * Domain path: /languages
 * Text domain: wpmm-memory-meter
 */
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
if ( function_exists( 'wpmm_memory_meter_freemius' ) ) {
    wpmm_memory_meter_freemius()->set_basename( false, __FILE__ );
} else {
    if ( !function_exists( 'wpmm_memory_meter_freemius' ) ) {
        function wpmm_memory_meter_freemius() {
            global $wpmm_memory_meter_freemius;
            if ( !isset( $wpmm_memory_meter_freemius ) ) {
                require_once dirname( __FILE__ ) . '/freemius/start.php';
                $wpmm_memory_meter_freemius = fs_dynamic_init( array(
                    'id'             => '10005',
                    'slug'           => 'wpmm-memory-meter',
                    'type'           => 'plugin',
                    'public_key'     => 'pk_ec37e24b3146b9a69195e72a38674',
                    'is_premium'     => false,
                    'premium_suffix' => 'Premium',
                    'has_addons'     => false,
                    'has_paid_plans' => true,
                    'menu'           => array(
                        'slug'       => 'wpmm-memory-meter',
                        'first-path' => 'admin.php?page=wpmm-memory-meter',
                    ),
                    'is_live'        => true,
                ) );
            }
            return $wpmm_memory_meter_freemius;
        }

        wpmm_memory_meter_freemius();
        do_action( 'wpmm_memory_meter_freemius_loaded' );
    }
    if ( !class_exists( 'WPMM_Memory_Meter' ) ) {
        define( 'WPMM_MEMORY_METER_VERSION', '2.1.0' );
        class WPMM_Memory_Meter {
            public $site_url;

            public $dashboard_url;

            public $upgrade_url;

            public $file_size_format;

            public $php_memory_limit_bytes;

            public $php_memory_limit;

            public $php_memory_limit_display;

            public $php_memory_limit_url;

            public $php_max_execution_time;

            public $php_max_execution_time_display;

            public $php_max_execution_time_url;

            public $wp_memory_limit_bytes;

            public $wp_memory_limit;

            public $wp_memory_limit_display;

            public $wp_memory_limit_url;

            public $wp_max_memory_limit_bytes;

            public $wp_max_memory_limit;

            public $wp_max_memory_limit_display;

            public $wp_max_memory_limit_url;

            public function __construct() {
                $this->site_url = untrailingslashit( get_site_url() );
                $this->dashboard_url = get_admin_url() . 'admin.php?page=wpmm-memory-meter';
                $this->upgrade_url = get_admin_url() . 'admin.php?page=wpmm-memory-meter-pricing';
                $this->file_size_format = apply_filters( 'wpmm_memory_meter_file_size_format', 'MB' );
                $this->php_memory_limit_bytes = $this->php_ini_notation_to_bytes( ini_get( 'memory_limit' ) );
                $this->php_memory_limit = $this->convert_file_size( $this->php_memory_limit_bytes, $this->file_size_format );
                // translators: PHP memory limit
                $this->php_memory_limit_display = sprintf( __( 'PHP Memory Limit: %s', 'wpmm-memory-meter' ), $this->php_memory_limit );
                $this->php_memory_limit_url = 'https://www.php.net/manual/en/ini.core.php#ini.memory-limit';
                $this->php_max_execution_time = ini_get( 'max_execution_time' );
                // translators: PHP max execution time
                $this->php_max_execution_time_display = sprintf( __( 'PHP Max Execution Time: %s', 'wpmm-memory-meter' ), $this->php_max_execution_time . ' ' . __( 'seconds', 'wpmm-memory-meter' ) );
                $this->php_max_execution_time_url = 'https://www.php.net/manual/en/info.configuration.php#ini.max-execution-time';
                $this->wp_memory_limit_bytes = $this->php_ini_notation_to_bytes( WP_MEMORY_LIMIT );
                $this->wp_memory_limit = $this->convert_file_size( $this->wp_memory_limit_bytes, $this->file_size_format );
                // translators: WP memory limit
                $this->wp_memory_limit_display = sprintf( __( 'WP Memory Limit: %s', 'wpmm-memory-meter' ), $this->wp_memory_limit );
                $this->wp_memory_limit_url = 'https://developer.wordpress.org/apis/wp-config-php/#increasing-memory-allocated-to-php';
                $this->wp_max_memory_limit_bytes = $this->php_ini_notation_to_bytes( WP_MAX_MEMORY_LIMIT );
                $this->wp_max_memory_limit = $this->convert_file_size( $this->wp_max_memory_limit_bytes, $this->file_size_format );
                // translators: WP max memory limit
                $this->wp_max_memory_limit_display = sprintf( __( 'WP Max Memory Limit: %s', 'wpmm-memory-meter' ), $this->wp_max_memory_limit );
                $this->wp_max_memory_limit_url = 'https://developer.wordpress.org/apis/wp-config-php/#increasing-memory-allocated-to-php';
                add_action( 'init', array($this, 'translation') );
                add_action( 'admin_enqueue_scripts', array($this, 'enqueues_admin') );
                add_action( 'wp_enqueue_scripts', array($this, 'enqueues_both') );
                add_action( 'admin_enqueue_scripts', array($this, 'enqueues_both') );
                add_action( 'admin_bar_menu', array($this, 'admin_bar_items'), 100 );
                add_action( 'admin_menu', array($this, 'menu_pages') );
                add_action( 'wp_footer', array($this, 'memory_usage') );
                add_action( 'admin_footer', array($this, 'memory_usage') );
            }

            public function translation() {
                load_plugin_textdomain( 'wpmm-memory-meter', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
            }

            public function enqueues_admin() {
                wp_enqueue_script(
                    'wpmm-memory-meter-admin',
                    plugins_url( 'assets/js/admin.min.js', __FILE__ ),
                    array('jquery', 'wp-i18n'),
                    WPMM_MEMORY_METER_VERSION,
                    true
                );
                wp_set_script_translations( 'wpmm-memory-meter-admin', 'wpmm-memory-meter', plugin_dir_path( __FILE__ ) . 'languages' );
                wp_enqueue_style(
                    'wpmm-memory-meter-admin',
                    plugins_url( 'assets/css/admin.min.css', __FILE__ ),
                    array(),
                    WPMM_MEMORY_METER_VERSION,
                    'all'
                );
            }

            public function enqueues_both() {
                wp_enqueue_script( 'jquery' );
                wp_enqueue_script(
                    'wpmm-memory-meter-both',
                    plugins_url( 'assets/js/both.min.js', __FILE__ ),
                    array('jquery', 'wp-i18n'),
                    WPMM_MEMORY_METER_VERSION,
                    true
                );
                wp_set_script_translations( 'wpmm-memory-meter-both', 'wpmm-memory-meter', plugin_dir_path( __FILE__ ) . 'languages' );
                wp_enqueue_style(
                    'wpmm-memory-meter-both',
                    plugins_url( 'assets/css/both.min.css', __FILE__ ),
                    array(),
                    WPMM_MEMORY_METER_VERSION,
                    'all'
                );
            }

            public function admin_bar_items( $admin_bar ) {
                // Addition of admin bar items
                if ( current_user_can( apply_filters( 'wpmm_memory_meter_capability', 'manage_options' ) ) ) {
                    $admin_bar->add_menu( array(
                        'id'    => 'wpmm-memory-meter-admin-bar-menu',
                        'title' => esc_html__( 'Memory: Calculating', 'wpmm-memory-meter' ),
                        'href'  => esc_url( $this->dashboard_url ),
                    ) );
                    $admin_bar->add_menu( array(
                        'id'     => 'wpmm-memory-meter-admin-bar-menu-php-memory-limit',
                        'parent' => 'wpmm-memory-meter-admin-bar-menu',
                        'title'  => esc_html( $this->php_memory_limit_display ),
                        'href'   => esc_url( $this->php_memory_limit_url ),
                        'meta'   => array(
                            'target' => '_blank',
                        ),
                    ) );
                    $admin_bar->add_menu( array(
                        'id'     => 'wpmm-memory-meter-admin-bar-menu-php-max-execution-time',
                        'parent' => 'wpmm-memory-meter-admin-bar-menu',
                        'title'  => esc_html( $this->php_max_execution_time_display ),
                        'href'   => esc_url( $this->php_max_execution_time_url ),
                        'meta'   => array(
                            'target' => '_blank',
                        ),
                    ) );
                    $admin_bar->add_menu( array(
                        'id'     => 'wpmm-memory-meter-admin-bar-menu-wp-memory-limit',
                        'parent' => 'wpmm-memory-meter-admin-bar-menu',
                        'title'  => esc_html( $this->wp_memory_limit_display ),
                        'href'   => esc_url( $this->wp_memory_limit_url ),
                        'meta'   => array(
                            'target' => '_blank',
                        ),
                    ) );
                    $admin_bar->add_menu( array(
                        'id'     => 'wpmm-memory-meter-admin-bar-menu-wp-max-memory-limit',
                        'parent' => 'wpmm-memory-meter-admin-bar-menu',
                        'title'  => esc_html( $this->wp_max_memory_limit_display ),
                        'href'   => esc_url( $this->wp_max_memory_limit_url ),
                        'meta'   => array(
                            'target' => '_blank',
                        ),
                    ) );
                    $admin_bar->add_menu( array(
                        'id'     => 'wpmm-memory-meter-admin-bar-menu-logs',
                        'parent' => 'wpmm-memory-meter-admin-bar-menu',
                        'title'  => esc_html__( 'Logs', 'wpmm-memory-meter' ),
                        'href'   => esc_url( $this->dashboard_url ),
                    ) );
                    $admin_bar->add_menu( array(
                        'id'     => 'wpmm-memory-meter-admin-bar-menu-configuration',
                        'parent' => 'wpmm-memory-meter-admin-bar-menu',
                        'title'  => esc_html__( 'Configuration', 'wpmm-memory-meter' ),
                        'href'   => esc_url( get_admin_url() ) . 'admin.php?page=wpmm-memory-meter&tab=configuration',
                    ) );
                    $admin_bar->add_menu( array(
                        'id'     => 'wpmm-memory-meter-admin-bar-menu-upgrade',
                        'parent' => 'wpmm-memory-meter-admin-bar-menu',
                        'title'  => esc_html__( 'Upgrade', 'wpmm-memory-meter' ),
                        'href'   => esc_url( $this->upgrade_url ),
                    ) );
                }
            }

            public function menu_pages() {
                add_menu_page(
                    __( 'Memory Meter', 'wpmm-memory-meter' ),
                    __( 'Memory Meter', 'wpmm-memory-meter' ),
                    apply_filters( 'wpmm_memory_meter_capability', 'manage_options' ),
                    'wpmm-memory-meter',
                    array($this, 'page'),
                    'dashicons-performance',
                    '100'
                );
            }

            public function page() {
                if ( isset( $_POST['wpmm_memory_meter_clear_logs_submit_nonce'] ) ) {
                    if ( wp_verify_nonce( sanitize_key( $_POST['wpmm_memory_meter_clear_logs_submit_nonce'] ), 'wpmm_memory_meter_clear_logs_submit' ) ) {
                        if ( isset( $_POST['wpmm_memory_meter_clear_logs'] ) ) {
                            delete_option( 'wpmm_memory_meter_logs_latest' );
                            delete_option( 'wpmm_memory_meter_logs_flagged' );
                        }
                    }
                }
                $tab = ( isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : '' );
                $logs_latest = get_option( 'wpmm_memory_meter_logs_latest' );
                $logs_flagged = get_option( 'wpmm_memory_meter_logs_flagged' );
                ?>

				<div class="wrap">
					<h1 class="wp-heading-inline"><?php 
                esc_html_e( 'Memory Meter', 'wpmm-memory-meter' );
                ?></h1>
					<h2 class="wpmm-memory-meter-page-tabs nav-tab-wrapper">
						<a href="<?php 
                echo esc_url( $this->dashboard_url );
                ?>" class="nav-tab<?php 
                echo ( '' == $tab ? ' nav-tab-active' : '' );
                ?>"><?php 
                esc_html_e( 'Logs', 'wpmm-memory-meter' );
                ?></a>
						<a href="<?php 
                echo esc_url( add_query_arg( 'tab', 'configuration' ) );
                ?>" class="nav-tab<?php 
                echo ( 'configuration' == $tab ? ' nav-tab-active' : '' );
                ?>"><?php 
                esc_html_e( 'Configuration', 'wpmm-memory-meter' );
                ?></a>
						<?php 
                if ( '' == $tab ) {
                    ?>
							<div class="wpmm-memory-meter-page-tabs-buttons">
								<a href="<?php 
                    echo esc_url( $this->dashboard_url );
                    ?>" class="button button-primary button-small"><span class="dashicons dashicons-update"></span> <?php 
                    esc_html_e( 'Refresh logs', 'wpmm-memory-meter' );
                    ?></a>
								<form method="post">
									<?php 
                    wp_nonce_field( 'wpmm_memory_meter_clear_logs_submit', 'wpmm_memory_meter_clear_logs_submit_nonce' );
                    ?>
									<button name="wpmm_memory_meter_clear_logs" id="wpmm-memory-meter-clear-logs" class="button button-small"><span class="dashicons dashicons-dismiss"></span> <?php 
                    esc_html_e( 'Clear logs', 'wpmm-memory-meter' );
                    ?></button>
								</form>
							</div>
						<?php 
                }
                ?>
					</h2>

					<?php 
                if ( false == apply_filters( 'wpmm_memory_meter_logs', true ) ) {
                    ?>

						<div class="wpmm-memory-meter-page-notice-logs-disabled wpmm-memory-meter-page-notice notice notice-error inline">
							<p><strong><?php 
                    esc_html_e( 'Logs are currently disabled in configuration so no further logging will occur.', 'wpmm-memory-meter' );
                    ?></strong></p>
						</div>

						<?php 
                }
                if ( '' == $tab ) {
                    ?>

						<div class="wpmm-memory-meter-page-columns">
							<div class="wpmm-memory-meter-page-column">
								<h2><?php 
                    esc_html_e( 'Latest Logs', 'wpmm-memory-meter' );
                    ?></h2>
								<p><?php 
                    esc_html_e( 'Latest page requests memory usage:', 'wpmm-memory-meter' );
                    ?></p>
								<?php 
                    $this->logs_table( $logs_latest );
                    ?>
							</div>
							<div class="wpmm-memory-meter-page-column">
								<h2><?php 
                    esc_html_e( 'Flagged Logs', 'wpmm-memory-meter' );
                    ?></h2>
								<?php 
                    // translators: warning and bad percentages
                    ?>
								<p><?php 
                    echo wp_kses_post( sprintf( __( 'Flagged page requests with memory usage over %1$s and %2$s:', 'wpmm-memory-meter' ), '<span ' . (( apply_filters( 'wpmm_memory_meter_memory_usage_colors', true ) ? 'class="wpmm-memory-meter-memory-usage-color-key wpmm-memory-meter-memory-usage-color-warning"' : '' )) . ' title="' . esc_html__( 'Status:', 'wpmm-memory-meter' ) . ' ' . esc_html__( 'Warning', 'wpmm-memory-meter' ) . '">' . apply_filters( 'wpmm_memory_meter_memory_usage_percentage_warning', 75 ) . esc_html__( '%', 'wpmm-memory-meter' ) . '</span>', '<span ' . (( apply_filters( 'wpmm_memory_meter_memory_usage_colors', true ) ? 'class="wpmm-memory-meter-memory-usage-color-key wpmm-memory-meter-memory-usage-color-bad"' : '' )) . ' title="' . esc_html__( 'Status:', 'wpmm-memory-meter' ) . ' ' . esc_html__( 'Bad', 'wpmm-memory-meter' ) . '">' . apply_filters( 'wpmm_memory_meter_memory_usage_percentage_bad', 90 ) . esc_html__( '%', 'wpmm-memory-meter' ) . '</span>' ) );
                    ?></p>
								<?php 
                    $this->logs_table( $logs_flagged );
                    ?>
							</div>
						</div>

						<?php 
                } elseif ( 'configuration' == $tab ) {
                    ?>

						<h2><?php 
                    esc_html_e( 'PHP and WordPress Configuration', 'wpmm-memory-meter' );
                    ?></h2>
						<p><?php 
                    esc_html_e( 'Current PHP and WordPress memory related configuration is displayed below:', 'wpmm-memory-meter' );
                    ?></p>
						<?php 
                    $this->php_wordpress_configuration();
                    ?>
						<hr>
						<h2><?php 
                    esc_html_e( 'Filter Hooks', 'wpmm-memory-meter' );
                    ?></h2>
						<p><?php 
                    esc_html_e( 'Configure memory meter using these filter hooks:', 'wpmm-memory-meter' );
                    ?></p>
						<?php 
                    $this->filter_hooks_table();
                }
                ?>

				</div>

				<?php 
            }

            public function logs_table( $logs ) {
                ?>

				<table class="wpmm-memory-meter-page-table widefat striped fixed">
					<thead>
						<tr>
							<th><?php 
                esc_html_e( 'Date', 'wpmm-memory-meter' );
                ?></th>
							<th><?php 
                esc_html_e( 'Memory usage', 'wpmm-memory-meter' );
                ?></th>
							<th><?php 
                esc_html_e( 'Page request', 'wpmm-memory-meter' );
                ?></th>
						</tr>
					</thead>
					<tbody>

						<?php 
                if ( !empty( $logs ) && is_array( $logs ) ) {
                    $logs = array_reverse( $logs );
                    foreach ( $logs as $log ) {
                        ?>

								<tr>
									<td>
										<?php 
                        echo esc_html( get_date_from_gmt( gmdate( 'Y-m-d H:i:s', $log['date'] ), get_option( 'date_format' ) ) );
                        ?><br>
										<small><?php 
                        echo esc_html( get_date_from_gmt( gmdate( 'Y-m-d H:i:s', $log['date'] ), get_option( 'time_format' ) ) );
                        ?></small>
									</td>
									<?php 
                        // translators: status
                        ?>
									<td title="<?php 
                        echo esc_attr( sprintf( esc_html__( 'Status: %s', 'wpmm-memory-meter' ), ucfirst( $log['status'] ) ) );
                        ?>">
										<?php 
                        if ( true == apply_filters( 'wpmm_memory_meter_memory_usage_colors', true ) ) {
                            ?>
											<span class="wpmm-memory-meter-memory-usage-dot wpmm-memory-meter-memory-usage-color-<?php 
                            echo esc_html( $log['status'] );
                            ?>"></span>
										<?php 
                        }
                        ?>
										<?php 
                        echo wp_kses_post( $log['usage'] );
                        ?>
									</td>
									<td>
										<?php 
                        if ( isset( $log['page'] ) ) {
                            ?>
											<small><a href="<?php 
                            echo esc_url( $this->site_url . $log['page'] );
                            ?>" target="_blank"><?php 
                            echo esc_url( $log['page'] );
                            ?></a></small>
											<?php 
                        } else {
                            ?>
												<small>
													<strong><?php 
                            esc_html_e( 'Unavailable - not logged for user', 'wpmm-memory-meter' );
                            ?></strong><br>
													<a href="<?php 
                            echo esc_url( $this->upgrade_url );
                            ?>">
														<?php 
                            // translators: %s user capability
                            echo sprintf( esc_html__( 'Upgrade to log for users without %s capability', 'wpmm-memory-meter' ), 'manage_options' );
                            // Specifically not the wpmm_memory_meter_capability filter hook, see information in configuration section related to this filter hook why
                            ?>
													</a>
												</small>
												<?php 
                        }
                        ?>
									</td>
								</tr>

								<?php 
                    }
                } else {
                    ?>

							<tr>
								<td colspan="3"><?php 
                    esc_html_e( 'No logs yet.', 'wpmm-memory-meter' );
                    ?></td>
							</tr>

							<?php 
                }
                ?>

					</tbody>
				</table>

				<?php 
            }

            public function php_wordpress_configuration() {
                ?>

				<div class="wpmm-memory-meter-page-php-wordpress-configuration">
					<div class="wpmm-memory-meter-page-php-wordpress-configuration-box wpmm-memory-meter-page-php-wordpress-configuration-box-php">
						<?php 
                $this->php_logo();
                ?>
						<strong><?php 
                echo esc_html( $this->php_memory_limit_display );
                ?></strong>
						<a href="<?php 
                echo esc_url( $this->php_memory_limit_url );
                ?>" target="_blank" class="button"><?php 
                esc_html_e( 'More info', 'wpmm-memory-meter' );
                ?></a>
					</div>
					<div class="wpmm-memory-meter-page-php-wordpress-configuration-box wpmm-memory-meter-page-php-wordpress-configuration-box-php">
						<?php 
                $this->php_logo();
                ?>
						<strong><?php 
                echo esc_html( $this->php_max_execution_time_display );
                ?></strong>
						<a href="<?php 
                echo esc_url( $this->php_max_execution_time_url );
                ?>" target="_blank" class="button"><?php 
                esc_html_e( 'More info', 'wpmm-memory-meter' );
                ?></a>
					</div>
					<div class="wpmm-memory-meter-page-php-wordpress-configuration-box wpmm-memory-meter-page-php-wordpress-configuration-box-wordpress">
						<span class="dashicons dashicons-wordpress"></span>
						<strong><?php 
                echo esc_html( $this->wp_memory_limit_display );
                ?></strong>
						<a href="<?php 
                echo esc_url( $this->wp_memory_limit_url );
                ?>" target="_blank" class="button"><?php 
                esc_html_e( 'More info', 'wpmm-memory-meter' );
                ?></a>
					</div>
					<div class="wpmm-memory-meter-page-php-wordpress-configuration-box wpmm-memory-meter-page-php-wordpress-configuration-box-wordpress">
						<span class="dashicons dashicons-wordpress"></span>
						<strong><?php 
                echo esc_html( $this->wp_max_memory_limit_display );
                ?></strong>
						<a href="<?php 
                echo esc_url( $this->wp_max_memory_limit_url );
                ?>" target="_blank" class="button"><?php 
                esc_html_e( 'More info', 'wpmm-memory-meter' );
                ?></a>
					</div>
				</div>

				<?php 
            }

            public function filter_hooks_table() {
                ?>

				<table class="wpmm-memory-meter-page-table widefat fixed striped">
					<thead>
						<tr>
							<th><?php 
                esc_html_e( 'Hook name', 'wpmm-memory-meter' );
                ?></th>
							<th><?php 
                esc_html_e( 'Arguments', 'wpmm-memory-meter' );
                ?></th>
							<th><?php 
                esc_html_e( 'Description', 'wpmm-memory-meter' );
                ?></th>
							<th><?php 
                esc_html_e( 'Default value', 'wpmm-memory-meter' );
                ?></th>
							<th><?php 
                esc_html_e( 'Current value', 'wpmm-memory-meter' );
                ?></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td><code>wpmm_memory_meter_capability</code></td>
							<td>
							<code>$capability</code><br>
								<small><?php 
                esc_html_e( '(string)', 'wpmm-memory-meter' );
                ?> <?php 
                esc_html_e( 'WordPress capability', 'wpmm-memory-meter' );
                ?></small>
							</td>
							<td>
								<?php 
                // translators: %s: user capability
                echo wp_kses_post( sprintf( __( 'Set the capability a user must have to view the memory meter in the admin bar and access the Memory Meter dashboard. This is not used to determine if the page request information should be logged in addition to the memory usage for a user, when using the free version of this plugin, the page request information is only logged if the user has the %s capability, generally administrators.', 'wpmm-memory-meter' ), '<code>manage_options</code>' ) );
                ?>
							</td>
							<td><code>manage_options</code></td>
							<td><code><?php 
                echo esc_html( apply_filters( 'wpmm_memory_meter_capability', 'manage_options' ) );
                ?></code></td>
						</tr>
						<tr>
							<td><code>wpmm_memory_meter_file_size_format</code></td>
							<td>
								<code>$file_size_format</code><br>
								<?php 
                // translators: file size formats
                ?>
								<small><?php 
                esc_html_e( '(string)', 'wpmm-memory-meter' );
                ?> <?php 
                echo sprintf(
                    esc_html__( 'File size format as %1$s, %2$s or %3$s', 'wpmm-memory-meter' ),
                    'KB',
                    'MB',
                    'GB'
                );
                ?></small>
							</td>
							<td><?php 
                esc_html_e( 'Set the file size format for the memory meter.', 'wpmm-memory-meter' );
                ?></td>
							<td><code>MB</code></td>
							<td><code><?php 
                echo esc_html( apply_filters( 'wpmm_memory_meter_file_size_format', 'MB' ) );
                ?></code></td>
						</tr>
						<tr>
							<td><code>wpmm_memory_meter_logs</code></td>
							<td>
								<code>$enabled</code><br>
								<small><?php 
                esc_html_e( '(bool)', 'wpmm-memory-meter' );
                ?> <?php 
                esc_html_e( 'true or false', 'wpmm-memory-meter' );
                ?></small>
							</td>
							<td><?php 
                esc_html_e( 'Enable or disable memory usage logs.', 'wpmm-memory-meter' );
                ?></td>
							<td><code>true</td>
							<td><code><?php 
                echo esc_html( ( apply_filters( 'wpmm_memory_meter_logs', true ) ? 'true' : 'false' ) );
                ?></code></td>
						</tr>
						<tr>
							<td><code>wpmm_memory_meter_logs_dashboard_page_requests</code></td>
							<td>
								<code>$enabled</code><br>
								<small><?php 
                esc_html_e( '(bool)', 'wpmm-memory-meter' );
                ?> <?php 
                esc_html_e( 'true or false', 'wpmm-memory-meter' );
                ?></small>
							</td>
							<td><?php 
                esc_html_e( 'Enable or disable logging of Memory Meter dashboard based page requests.', 'wpmm-memory-meter' );
                ?></td>
							<td><code>false</td>
							<td><code><?php 
                echo esc_html( ( apply_filters( 'wpmm_memory_meter_logs_dashboard_page_requests', false ) ? 'true' : 'false' ) );
                ?></code></td>
						</tr>
						<tr>
							<td><code>wpmm_memory_meter_logs_max</code></td>
							<td>
								<code>$max</code><br>
								<small><?php 
                esc_html_e( '(int)', 'wpmm-memory-meter' );
                ?> <?php 
                esc_html_e( 'Max logs number', 'wpmm-memory-meter' );
                ?></small>
							</td>
							<td><?php 
                esc_html_e( 'Set the maximum number of logs.', 'wpmm-memory-meter' );
                ?></td>
							<td><code>20</code></td>
							<td><code><?php 
                echo esc_html( apply_filters( 'wpmm_memory_meter_logs_max', 20 ) );
                ?></code></td>
						</tr>
						<tr>
							<td><code>wpmm_memory_meter_memory_usage_colors</code></td>
							<td>
								<code>$enable</code><br>
								<small><?php 
                esc_html_e( '(bool)', 'wpmm-memory-meter' );
                ?> <?php 
                esc_html_e( 'true or false', 'wpmm-memory-meter' );
                ?></small>
							</td>
							<td><?php 
                esc_html_e( 'Set whether memory usage colors should be applied.', 'wpmm-memory-meter' );
                ?></td>
							<td><code>true</code></td>
							<td><code><?php 
                echo esc_html( ( apply_filters( 'wpmm_memory_meter_memory_usage_colors', true ) ? 'true' : 'false' ) );
                ?></code></td>
						</tr>
						<tr>
							<td><code>wpmm_memory_meter_memory_usage_peak_real</code></td>
							<td>
								<code>$real_usage</code><br>
								<small><?php 
                esc_html_e( '(bool)', 'wpmm-memory-meter' );
                ?> <?php 
                esc_html_e( 'true or false', 'wpmm-memory-meter' );
                ?></small>
							</td>
							<?php 
                // translators: PHP's memory_get_peak_usage() and $real_usage argument
                ?>
							<td><?php 
                echo wp_kses_post( sprintf( __( 'Set if memory usage got from PHP\'s %1$s enables %2$s.', 'wpmm-memory-meter' ), '<a href="' . esc_url( 'https://www.php.net/manual/en/function.memory-get-peak-usage.php' ) . '" target="_blank">memory_get_peak_usage()</a>', '<code>$real_usage</code>' ) );
                ?></td>
							<td><code>true</code></td>
							<td><code><?php 
                echo ( apply_filters( 'wpmm_memory_meter_memory_usage_peak_real', true ) ? 'true' : 'false' );
                ?></code></td>
						</tr>
						<tr>
							<td><code>wpmm_memory_meter_memory_usage_percentage_bad</code></td>
							<td>
								<code>$percentage</code><br>
								<small><?php 
                esc_html_e( '(int)', 'wpmm-memory-meter' );
                ?> <?php 
                esc_html_e( 'Percentage number', 'wpmm-memory-meter' );
                ?></small>
							</td>
							<td><?php 
                esc_html_e( 'Set the bad memory usage percentage.', 'wpmm-memory-meter' );
                ?></td>
							<td><code>90</code></td>
							<td><code><?php 
                echo esc_html( apply_filters( 'wpmm_memory_meter_memory_usage_percentage_bad', 90 ) );
                ?></code></td>
						</tr>
						<tr>
							<td><code>wpmm_memory_meter_memory_usage_percentage_warning</code></td>
							<td>
								<code>$percentage</code><br>
								<small><?php 
                esc_html_e( '(int)', 'wpmm-memory-meter' );
                ?> <?php 
                esc_html_e( 'Percentage number', 'wpmm-memory-meter' );
                ?></small>
							</td>
							<td><?php 
                esc_html_e( 'Set the warning memory usage percentage.', 'wpmm-memory-meter' );
                ?></td>
							<td><code>75</code></td>
							<td><code><?php 
                echo esc_html( apply_filters( 'wpmm_memory_meter_memory_usage_percentage_warning', 75 ) );
                ?></code></td>
						</tr>
					</tbody>
				</table>

				<?php 
            }

            public function memory_usage() {
                // Calculate memory usage
                $memory_usage_bytes = $this->php_ini_notation_to_bytes( memory_get_peak_usage( apply_filters( 'wpmm_memory_meter_memory_usage_peak_real', true ) ) );
                $memory_usage = $this->convert_file_size( $memory_usage_bytes, $this->file_size_format );
                $memory_usage_percent = round( $memory_usage_bytes / $this->php_memory_limit_bytes * 100, 2 );
                // Set memory usage status
                if ( $memory_usage_percent > (int) apply_filters( 'wpmm_memory_meter_memory_usage_percentage_bad', 90 ) ) {
                    $memory_usage_status = 'bad';
                    $memory_usage_status_name = esc_html__( 'Bad', 'wpmm-memory-meter' );
                    $memory_usage_flagged = true;
                } elseif ( $memory_usage_percent > (int) apply_filters( 'wpmm_memory_meter_memory_usage_percentage_warning', 75 ) ) {
                    $memory_usage_status = 'warning';
                    $memory_usage_status_name = esc_html__( 'Warning', 'wpmm-memory-meter' );
                    $memory_usage_flagged = true;
                } else {
                    $memory_usage_status = 'okay';
                    $memory_usage_status_name = esc_html__( 'Okay', 'wpmm-memory-meter' );
                    $memory_usage_flagged = false;
                }
                // Set memory usage class
                $memory_usage_class = ( true == apply_filters( 'wpmm_memory_meter_memory_usage_colors', true ) ? 'wpmm-memory-meter-memory-usage-color-' . $memory_usage_status : '' );
                // Set memory usage string
                // translators: status, memory usage
                $memory_usage_string = sprintf(
                    esc_html__( 'Memory: %1$s (%2$s of %3$s)', 'wpmm-memory-meter' ),
                    $memory_usage,
                    $memory_usage_percent . '%',
                    esc_html( $this->php_memory_limit )
                );
                // Output memory usage HTML (near end of page code due to hook this function uses, this is then dynamically added to the admin bar)
                // translators: %s: memory usage status name
                echo '<div id="wpmm-memory-meter-memory-usage" class="' . esc_attr( $memory_usage_class ) . '" title="' . esc_attr( sprintf( esc_html__( 'Status: %s', 'wpmm-memory-meter' ), $memory_usage_status_name ) ) . '" style="display: none !important;" data-memory-usage="' . esc_html( $memory_usage_string ) . '"></div>';
                // Add memory usage to logs
                if ( true == apply_filters( 'wpmm_memory_meter_logs', true ) ) {
                    if ( isset( $_SERVER['REQUEST_URI'] ) ) {
                        $page_request = esc_url_raw( $_SERVER['REQUEST_URI'] );
                    } else {
                        $page_request = '';
                    }
                    if ( (int) apply_filters( 'wpmm_memory_meter_logs_max', 20 ) <= 0 ) {
                        delete_option( 'wpmm_memory_meter_logs_latest' );
                        delete_option( 'wpmm_memory_meter_logs_flagged' );
                        return;
                    }
                    if ( false == apply_filters( 'wpmm_memory_meter_logs_dashboard_page_requests', false ) ) {
                        if ( strpos( $page_request, 'page=wpmm-memory-meter' ) !== false ) {
                            return;
                        }
                    }
                    $logs_latest = get_option( 'wpmm_memory_meter_logs_latest' );
                    $logs_latest = ( !empty( $logs_latest ) ? $logs_latest : array() );
                    if ( true == $memory_usage_flagged ) {
                        $logs_flagged = get_option( 'wpmm_memory_meter_logs_flagged' );
                        $logs_flagged = ( !empty( $logs_flagged ) ? $logs_flagged : array() );
                    }
                    if ( count( $logs_latest ) >= (int) apply_filters( 'wpmm_memory_meter_logs_max', 20 ) ) {
                        $logs_latest = array_slice( $logs_latest, count( $logs_latest ) - (int) apply_filters( 'wpmm_memory_meter_logs_max', 20 ) + 1 );
                        // Ensures entries do not exceed the max set (this also means if wpmm_memory_meter_logs_max was a high number and then changed to a lower number the log entries are reduced accordingly)
                    }
                    if ( true == $memory_usage_flagged ) {
                        if ( count( $logs_flagged ) >= (int) apply_filters( 'wpmm_memory_meter_logs_max', 20 ) ) {
                            $logs_flagged = array_slice( $logs_flagged, count( $logs_flagged ) - (int) apply_filters( 'wpmm_memory_meter_logs_max', 20 ) + 1 );
                            // Ensures entries do not exceed the max set (this also means if wpmm_memory_meter_logs_max was a high number and then changed to a lower number the log entries are reduced accordingly)
                        }
                    }
                    $log_new = array(
                        'date'   => time(),
                        'usage'  => sprintf(
                            esc_html__( '%1$s (%2$s of %3$s)', 'wpmm-memory-meter' ),
                            $memory_usage,
                            $memory_usage_percent . '%',
                            esc_html( $this->php_memory_limit )
                        ),
                        'status' => $memory_usage_status,
                    );
                    if ( current_user_can( 'manage_options' ) ) {
                        // Specifically not the wpmm_memory_meter_capability filter hook, see information in configuration section related to this filter hook why
                        $log_new['page'] = $page_request;
                    }
                    array_push( $logs_latest, $log_new );
                    update_option( 'wpmm_memory_meter_logs_latest', $logs_latest );
                    if ( true == $memory_usage_flagged ) {
                        array_push( $logs_flagged, $log_new );
                        update_option( 'wpmm_memory_meter_logs_flagged', $logs_flagged );
                    }
                }
            }

            public function convert_file_size( $bytes, $unit ) {
                // Converts given bytes value to another file size
                $bytes = (int) $bytes;
                if ( !empty( $bytes ) && !empty( $unit ) ) {
                    if ( 'KB' == $unit ) {
                        return round( $bytes / 1024, 4 ) . 'KB';
                    } elseif ( 'MB' == $unit ) {
                        return round( $bytes / 1024 / 1024, 4 ) . 'MB';
                    } elseif ( 'GB' == $unit ) {
                        return round( $bytes / 1024 / 1024 / 1024, 4 ) . 'GB';
                    }
                } else {
                    return false;
                }
            }

            public function php_ini_notation_to_bytes( $value ) {
                // Converts given PHP ini notation value to bytes, if not in notation format returns bytes
                $value = trim( $value );
                if ( is_numeric( $value ) ) {
                    return $value;
                } else {
                    $last = strtolower( $value[strlen( $value ) - 1] );
                    $value = substr( $value, 0, -1 );
                    switch ( $last ) {
                        case 'g':
                            $value *= 1024;
                        // no break
                        case 'm':
                            $value *= 1024;
                        // no break
                        case 'k':
                            $value *= 1024;
                    }
                    return $value;
                }
            }

            public function php_logo() {
                ?>

				<img src="data:image/png;base64,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">

				<?php 
            }

        }

        new WPMM_Memory_Meter();
    }
}