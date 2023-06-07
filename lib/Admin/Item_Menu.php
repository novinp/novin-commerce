<?php

namespace MobinDev\Novin_Commerce\Admin;

use MobinDev\Novin_Commerce\Plugin;

class Item_Menu {
	/**
	 * The plugin's instance.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    Plugin $plugin This plugin's instance.
	 */
	protected $plugin;

	/**
	 * @var List_Table
	 */
	protected $item_list_table;

	protected $name;
	protected $plural;
	protected $singular;
	protected $search = true;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param Plugin $plugin This plugin's instance.
	 *
	 * @since 1.0.0
	 *
	 */
	public function __construct( Plugin $plugin ) {
		$this->plugin = $plugin;
	}


	public function load() {
		$arguments = array(
			'label'   => __( 'تعداد در هر صفحه', $this->plugin->get_plugin_name() ),
			'default' => 20,
			'option'  => 'per_page'
		);
		add_screen_option( 'per_page', $arguments );

		$class_name            = __NAMESPACE__ . '\\' . ucfirst( $this->name ) . '_List_Table';
		$this->item_list_table = new $class_name( [
			'_plural'   => __( $this->_plural, $this->plugin->get_plugin_name() ),
			'_singular' => __( $this->_singular, $this->plugin->get_plugin_name() ),
			'plural'   =>  $this->plural,
			'singular' =>  $this->singular, $this->plugin->get_plugin_name(),
		] );

	}

	public function enqueueAssets() {
		wp_enqueue_style(
			$this->plugin->get_plugin_name() . '-admin-table',
			$this->plugin->getAdminStyleUrl() . 'table.min.css',
			[],
			$this->plugin->get_version(),
		);
	}

	public function output() {
		$this->enqueueAssets();
		$this->item_list_table->prepare_items();
		?>
		<div class="wrap">
			<h2><?php _e( $this->plural, $this->plugin->get_plugin_name() ); ?></h2>
			<div id="product-list-table">
				<div id="post-body">
					<form id="list-form" method="get">
						<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
						<?php
						if ( $this->search ) {
							$this->item_list_table->search_box( __( "جستجوی {$this->singular}", $this->plugin->get_plugin_name() ), $this->name );
						}

						$this->item_list_table->display();
						?>
					</form>
				</div>
			</div>
		</div>
		<?php
	}
}
