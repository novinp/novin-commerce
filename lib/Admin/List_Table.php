<?php

namespace MobinDev\Novin_Commerce\Admin;

use As247\WpEloquent\Database\Eloquent\Collection;
use MobinDev\Novin_Commerce\Models\Sync;
use Morilog\Jalali\Jalalian;

abstract class List_Table extends WP_List_Table {
	protected $name;
	protected $syncs;


	/**
	 * @param \WC_Product $item
	 *
	 * @return string|void
	 */
	final function column_cb( $item ) {
		return sprintf(
			'<label class="screen-reader-text" for="' . $this->name . '_' . $this->getID( $item ) . '">' . sprintf( __( 'Select %s' ), $this->getName( $item ) ) . '</label>'
			. "<input type='checkbox' name='{$this->name}s[]' id='{$this->name}_{$this->getID($item)}' value='{$this->getID($item)}' />"
		);
	}

	/**
	 * @param \WC_Product $item
	 *
	 * @return string|void
	 */
	final function column_name( $item ) {
		$admin_page_url = admin_url( 'admin.php' );

		// row action to view usermeta.
		$query_args_view_usermeta = array(
			'page'             => wp_unslash( $_REQUEST['page'] ),
			'action'           => 'sync',
			"{$this->name}s[]" => absint( $this->getID( $item ) ),
			'_wpnonce'         => wp_create_nonce( 'bulk-' . $this->_args['plural'] ),
			'_wp_http_referer' => add_query_arg( [ 'page' => wp_unslash( $_REQUEST['page'] ) ], $admin_page_url )
		);
		$sync_link                = esc_url( add_query_arg( $query_args_view_usermeta, $admin_page_url ) );
		$actions['sync']          = '<a href="' . $sync_link . '">همگام‌سازی</a>';

		// similarly add row actions for add usermeta.

		$row_value = '<strong>' . $this->getName( $item ) . '</strong>';

		return $row_value . $this->row_actions( $actions );
	}

	abstract function getName( $item );

	/**
	 * @param \WC_Product $item
	 *
	 * @return string|void
	 */
	final function column_guid( $item ) {
		$guid       = $this->getGUID( $item );
		$is_syncing = $this->syncs->where( 'item_id', $this->getID( $item ) )->count();
		$column     = '';
		if ( $guid ) {
			$column = "<code>{$this->getGUID($item)}</code>";
		} else {
			$column = "<span class='no-guid'>GUID موجود نیست</span>";
		}
		if ( $is_syncing ) {
			$column .= "<br><span class='syncing' >در انتظار همگام‌سازی</span>";
		}

		return $column;
	}

	public function column_sync_date( $item ) {
		$sync_date_string = $this->getSyncDate( $item );
		if ( $sync_date_string ) {
			$sync_date = Jalalian::fromDateTime( substr( $sync_date_string, 0, - 1 ), wp_timezone() );

			return $sync_date->format( 'Y-m-d H:i:s' );
		}

		return '';
	}

	abstract function getSyncDate( $item );

	abstract function getID( $item );

	abstract function getGUID( $item );

	// Returns an associative array containing the bulk action.
	final function get_bulk_actions() {

		return [
			'sync' => 'همگام‌سازی'
		];
	}

	final function prepare_items() {
		// get syncing items
		$this->syncs = Sync::where( 'item_type', $this->name )->get();
		if ( ! $this->syncs ) {
			$this->syncs = new Collection();
		}

		// code to handle bulk actions
		$this->handleTableActions();

		//used by WordPress to build and fetch the _column_headers property
		$this->_column_headers = $this->get_column_info();

		// code to handle data operations like sorting and filtering

		$table_data = $this->fetchTableData();


		// code for pagination
		$per_page   = $this->get_items_per_page( 'per_page' );
		$table_page = $this->get_pagenum();

		// provide the ordered data to the List Table
		// we need to manually slice the data based on the current pagination
		$this->items = array_slice( $table_data, ( ( $table_page - 1 ) * $per_page ), $per_page );

		// set the pagination arguments
		$total = count( $table_data );
		$this->set_pagination_args( array(
			'total_items' => $total,
			'per_page'    => $per_page,
			'total_pages' => ceil( $total / $per_page )
		) );
	}

	abstract public function fetchTableData();

	final function handleTableActions() {

		if ( ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] === 'sync' ) ||
		     ( isset( $_REQUEST['action2'] ) && $_REQUEST['action2'] === 'sync' ) ) {

			if ( check_admin_referer( 'bulk-' . $this->_args['plural'] ) ) { // verify the nonce.
				$table_items = $_REQUEST["{$this->name}s"];
				$items       = [];
				foreach ( $table_items as $table_item_id ) {
					if ( $this->syncs->where( 'item_id', $table_item_id )->count() ) {
						continue;
					}
					$items[] = [
						'item_id'   => absint( esc_attr( wp_unslash( trim( $table_item_id ) ) ) ),
						'item_type' => $this->name,
					];
				}
				$result = Sync::insert(
					$items
				);

				if ( $result and count( $items ) ) {
					AdminNotice::addSuccessDismissible( _n(
						"{$this->_args['_singular']} با موفقیت به لیست همگام‌سازی اضافه شد.",
						"{$this->_args['_plural']} با موفقیت به لیست همگام‌سازی اضافه شد.",
						count( $items )
					) );
					Sync::doAddAction();
				}

				wp_redirect( $_REQUEST['_wp_http_referer'] ?? '' );
			}
		}

	}
}
