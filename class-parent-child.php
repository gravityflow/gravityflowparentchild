<?php
/**
 * Gravity Flow Form Connector
 *
 *
 * @package     GravityFlow
 * @subpackage  Classes/Extension
 * @copyright   Copyright (c) 2015, Steven Henty
 * @license     http://opensource.org/licenses/gpl-3.0.php GNU Public License
 * @since       1.0
 */

// Make sure Gravity Forms is active and already loaded.
if ( class_exists( 'GFForms' ) ) {

	class Gravity_Flow_Parent_Child extends Gravity_Flow_Extension {

		private static $_instance = null;

		public $_version = GRAVITY_FLOW_PARENT_CHILD_VERSION;

		public $edd_item_name = GRAVITY_FLOW_PARENT_CHILD_EDD_ITEM_NAME;

		// The Framework will display an appropriate message on the plugins page if necessary
		protected $_min_gravityforms_version = '1.9.10';

		protected $_slug = 'gravityflowparentchild';

		protected $_path = 'gravityflowparentchild/parentchild.php';

		// Title of the plugin to be used on the settings page, form settings and plugins page.
		protected $_title = 'Parent-Child Forms';

		// Short version of the plugin title to be used on menus and other places where a less verbose string is useful.
		protected $_short_title = 'Parent-Child Forms';

		protected $_capabilities = array(
			'gravityflowparentchild_uninstall',
			'gravityflowparentchild_settings',
			'gravityflowparentchild_form_settings',
			'gravityflowparentchild_view_all',
		);

		protected $_capabilities_app_settings = 'gravityflowparentchild_settings';
		protected $_capabilities_form_settings = 'gravityflowparentchild_form_settings';
		protected $_capabilities_uninstall = 'gravityflowparentchild_uninstall';

		public static function get_instance() {
			if ( self::$_instance == null ) {
				self::$_instance = new Gravity_Flow_Parent_Child();
			}

			return self::$_instance;
		}

		private function __clone() {
		} /* do nothing */


		public function init() {
			parent::init();
			add_action( 'gravityflow_workflow_detail_sidebar', array( $this, 'action_gravityflow_entry_detail' ), 10, 2 );
			add_action( 'gform_entry_detail_sidebar_middle', array( $this, 'action_gravityflow_entry_detail' ), 10, 2 );
		}

		public function init_ajax() {
			parent::init_ajax();
			add_action( 'wp_ajax_gravityflowparentchild_get_form', array( $this, 'ajax_get_form' ) );
		}

		public function get_entry_meta( $entry_meta, $form_id ) {

			$parent_form_ids = $this->get_parent_form_ids( $form_id );

			foreach ( $parent_form_ids as $parent_form_id ) {
				$entry_meta[ 'workflow_parent_form_id_' . $parent_form_id . '_entry_id' ] = array(
					'label'                      => sprintf( __( 'Parent Entry ID: Form %d', 'gravityflowformconnector' ), $parent_form_id ),
					'is_numeric'                 => true,
					'update_entry_meta_callback' => array( $this, 'update_entry_meta_callback' ),
					'is_default_column'          => true, // this column will be displayed by default on the entry list
					'filter'                     => array(
						'operators' => array( 'is' ),
					),
				);
			}

			return $entry_meta;
		}

		public function update_entry_meta_callback( $key, $entry, $form ) {

			if ( isset( $_REQUEST['workflow_parent_entry_id'] ) ) {
				return absint( $_REQUEST['workflow_parent_entry_id'] );
			}

			if ( isset( $entry[ $key ] ) ) {
				return  $entry[ $key ];
			}

			return '';
		}

		public function form_settings_fields( $form ) {

			return array(
				array(
					'title'  => esc_html__( 'Parent-Child Forms', 'gravityflow' ),
					'fields' => array(
						array(
							'name' => 'parent_form_' . $form['id'],
							'label' => esc_html__( 'Child Forms', 'gravityflowformconnector' ),
							'type' => 'child_forms',
						),
					),
				),
				array(
					'title'  => esc_html__( 'Parent Forms', 'gravityflow' ),
					'fields' => array(
						array(
							'name' => 'parent_form_' . $form['id'],
							'label' => esc_html__( 'Select Parent Forms', 'gravityflowformconnector' ),
							'type' => 'parent_forms',
						),
					),
				),
			);
		}

		public function action_gravityflow_entry_detail( $form, $entry ) {

			if ( ! GFAPI::current_user_can_any( array( 'gravityflowparentchild_view_all' ) ) ) {
				return;
			}

			$connected_forms = GFAPI::get_forms();

			$child_forms = array();

			$form_settings = $this->get_form_settings( $form );

			$sibling_forms = array();

			foreach ( $connected_forms as $connected_form ) {
				$settings = $this->get_form_settings( $connected_form );
				if ( rgar( $settings, 'parent_form_' . $form['id'] ) ) {
					$child_forms[ $connected_form['id'] ] = array( 'title' => $connected_form['title'] );
				}
				if ( rgar( $form_settings, 'parent_form_' . $connected_form['id'] ) ) {
					$connected_entry_id = $entry[ 'workflow_parent_form_id_' . $connected_form['id'] . '_entry_id' ];
					$sibling_forms[ $connected_form['id'] ] = array( 'title' => $connected_form['title'], 'entry_id' => $connected_entry_id );
				}
			}

			if ( empty( $child_forms ) && empty( $sibling_forms ) ) {
				return;
			}


			foreach ( $child_forms as $child_form_id => $child_form  ) {
				$title = rgar( $child_form, 'title' );
				$this->display_child_entries( $form['id'], $child_form_id, $title, $entry['id'] );
			}

			if ( $this->current_user_can_any( array( 'gform_view_entries', 'gravityflow_view_all' ) ) ) {
				foreach ( $sibling_forms as $sibling_form_id => $sibling_form  ) {
					$title = rgar( $sibling_form, 'title' );
					$entry_url = add_query_arg( array( 'lid' => absint( $sibling_form['entry_id'] ), 'id' => $sibling_form_id ) );
					$parent_link = sprintf( '<a href="%s"><i class="fa fa-external-link"></i></a>',$entry_url, $title );
					$this->display_child_entries( $sibling_form_id, $form['id'], $title, $sibling_form['entry_id'], $parent_link );
				}
			}

			add_thickbox();

		}

		public function display_child_entries( $parent_form_id, $child_form_id, $title, $parent_entry_id, $parent_link = '' ) {

			if ( ! GFAPI::current_user_can_any( array( 'gravityflowparentchild_view_all' ) ) ) {
				return;
			}

			$current_entry_id = absint( rgget( 'lid' ) );

			$page_size = 20;
			$search_criteria = array(
				'status' => 'active',
				'field_filters' => array(
					array( 'key' => 'workflow_parent_form_id_' . $parent_form_id . '_entry_id', 'value' => $parent_entry_id ),
				),
			);
			$sorting = array( 'key' => 'date_created', 'direction' => 'DESC' );
			$paging          = array( 'offset' => 0, 'page_size' => $page_size );

			$total_count = 0;

			$entries = GFAPI::get_entries( $child_form_id, $search_criteria, $sorting, $paging, $total_count );

			$field_id = 'parent_form_' . $child_form_id;
			$view_all_url = admin_url( sprintf( 'admin.php?page=gravityflow-status&form-id=%d&f[0]=%s&o[0]=is&v[0]=%d', $child_form_id, $field_id, $parent_entry_id ) );
			$count_link = $total_count > $page_size ? sprintf( '<a href="%s" />%s (%d)</a>', $view_all_url, esc_html__( 'View all', 'gravityflow' ), $total_count ) : '';

			$form_url = admin_url( 'admin-ajax.php' ) . '?action=gravityflowparentchild_get_form&form_id=' . $child_form_id . '&workflow_parent_entry_id=' . $parent_entry_id;
			if ( is_admin() ) {
				$form_url .= '&is_admin=1';
			}

			$form_link = sprintf( '<a href="%s&TB_iframe=true&width=600&height=550" class="thickbox"><i class="fa fa-plus gravityflowparentchild-form-new-entry"></i></a>', $form_url );

			$form_link = apply_filters( 'gravityflowparentchild_add_child_entry_link', $form_link, $form_url, $parent_form_id, $child_form_id, $parent_entry_id );
			?>

			<div class="postbox">
				<h3 class="hndle" style="cursor:default;">
					<span><?php echo esc_html( $title ); ?> <?php echo $parent_link;?></span>
					<span style="float:right"><?php echo $form_link; ?></span>
				</h3>

				<div id="submitcomment" class="submitbox">
					<div id="minor-publishing" style="padding:10px;">
						<div class="gravityflow-detail-form-count">
							<table>
								<thead>
								<th>
									<?php esc_html_e( 'Date' ) ?>
								</th>
								<th>
									<?php esc_html_e( 'Submitter' ) ?>
								</th>
								</thead>
								<tbody>
								<?php
								foreach ( $entries as $entry ) {
									$entry_url = add_query_arg( array( 'lid' => absint( $entry['id'] ), 'id' => $child_form_id ) );

									$user_id = $entry['created_by'];
									if ( $user_id ) {
										$user         = get_user_by( 'id', $user_id );
										$display_name = $user->display_name;
									} else {
										$display_name = $entry['ip'];
									}
									?>
									<tr>
										<td>
											<?php
											if ( $current_entry_id == $entry['id'] ) {
												echo esc_html( GFCommon::format_date( $entry['date_created'], true, 'Y-m-d'  ) );
											} else {
												echo sprintf( '<a href="%s">%s</a>', esc_url( $entry_url ), GFCommon::format_date( $entry['date_created'],  true, 'Y-m-d' ) );
											}
											?>
										</td>
										<td>
											<?php
											if ( $current_entry_id == $entry['id'] ) {
												echo esc_html( $display_name );
											} else {
												echo sprintf( '<a href="%s">%s</a>', esc_url( $entry_url ), $display_name );
											}
											?>
										</td>
									</tr>
									<?php
								}
								?>
								</tbody>

							</table>
							<?php echo $count_link ?>
						</div>
					</div>
				</div>
			</div>
			<div id="gravityflow-child-form-container" style="display:none;">
				<p>loading...</p>
			</div>
			<?php
		}

		public function ajax_get_form() {
			$form_id = isset( $_GET['form_id'] ) ? absint( $_GET['form_id'] ) : 0;

			$field_id = sanitize_text_field( rgget( 'field_id' ) );

			$entry_id = absint( rgget( 'entry_id' ) );

			$field_values = array( $field_id => $entry_id );

			gravity_form_enqueue_scripts( $form_id, true );

			$is_admin = isset( $_GET['is_admin'] );
			if ( $is_admin ) {
				wp_enqueue_style( 'common', site_url() . '/wp-admin/css/common.css', array(), $this->_version );
			} else {
				wp_enqueue_style( 'common', get_stylesheet_directory_uri() . '/style.css', array(), $this->_version );
			}

			wp_print_styles();
			wp_print_scripts();
			// Render an AJAX-enabled form.
			// https://www.gravityhelp.com/documentation/article/embedding-a-form/#function-call
			$html = gravity_form( $form_id, true, false, false, $field_values, true, 1, false );
			printf( "<div id='gravityflow-child-form-wrapper' style='padding:10px;'>%s</div>", $html );
			die();
		}

		public function get_parent_form_ids( $form_id ) {
			$parent_form_ids = array();

			$form = GFAPI::get_form( $form_id );
			$settings = $this->get_form_settings( $form );

			if ( empty( $settings ) || ! is_array( $settings ) ) {
				return $parent_form_ids;
			}
			foreach ( $settings as $key => $setting ) {
				if ( strstr( $key, 'parent_form_' ) && $setting == 1 ) {
					$form_id = str_replace( 'parent_form_', '', $key );
					$parent_form_ids[] = absint( $form_id );
				}
			}

			return $parent_form_ids;
		}


		public function add_form_settings_menu( $tabs, $form_id ) {

			$tabs[] = array( 'name' => $this->_slug, 'label' => esc_html__( 'Parent Forms', 'gravityflowformconnector' ), 'query' => array( 'fid' => null ) );

			return $tabs;
		}

		public function settings_child_forms() {
			$forms = GFAPI::get_forms();
			$current_form = $this->get_current_form();

			foreach ( $forms  as $form ) {
				$settings = $this->get_form_settings( $form );
				if ( rgar( $settings, 'parent_form_' . $current_form['id'] ) == 1 ) {
					$child_forms[] = $form;
				}
			}

			if ( empty( $child_forms ) ) {
				esc_html_e( 'There are no child forms linked to this form', 'gravityflowformconnector' );
		    } else {
			    echo '<ul>';
			    foreach ( $child_forms as $child_form ) {
				    $form_settings_url = add_query_arg( array( 'id' => $child_form['id'] ) );
				    printf( '<li><a href="%s"><i class="fa fa-link"></i></a> %s</li>', esc_url( $form_settings_url ), $child_form['title'] );
			    }
			    echo '</ul>';
		    }


		}

		public function settings_parent_forms() {
			$parent_forms = GFAPI::get_forms();

			echo '<ul>';
			foreach ( $parent_forms  as $parent_form ) {

				$choice = array( 'label' => $parent_form['title'], 'name' => 'parent_form_' . $parent_form['id'] );
				$field = array(
					'name' => 'parent_form_' . $parent_form['id'],
					'label' => $parent_form['title'],
					'type' => 'checkbox',
					'horizontal' => true,
					'choices' => array( $choice ),
				);
				echo '<li>';
				$this->settings_checkbox( $field );
				$setting = $this->get_setting( 'parent_form_' . $parent_form['id'] );
				if ( $setting ) {
					$form_settings_url = add_query_arg( array( 'id' => $parent_form['id'] ) );
					printf( '<a href="%s"><i class="fa fa-link"></i></a>', esc_url( $form_settings_url ) );
				}
				echo '</li>';
			}
			echo '</ul>';
		}
	}

}
