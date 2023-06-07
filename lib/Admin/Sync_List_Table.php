<?php

namespace MobinDev\Novin_Commerce\Admin;

use MobinDev\Novin_Commerce\Models\Sync;

class Sync_List_Table extends List_Table {
	protected $name = 'sync';

// just the barebone implementation.
	public function get_columns() {
		return [
			'cb'        => '<input type="checkbox" />',
			'sync_id'   => 'شناسه همگام‌سازی',
			'item_id'   => 'شناسه مورد',
			'item_type' => 'نوع مورد',
		];
	}

	/**
	 * @param Sync $item
	 * @param string $column_name
	 *
	 * @return mixed|void
	 */
	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'sync_id':
				return $item['id'];
			case 'item_id':
			case 'item_type':
				return $item[$column_name];

		}
	}


	protected function get_sortable_columns() {
		return [
			'sync_id'   => array( 'sync_id', true ),
			'item_id'   => 'item_id',
			'item_type' => 'item_type',

		];
	}


	public function fetchTableData() {
		return Sync::all()->toArray();
	}

	/**
	 * @param Sync $item
	 *
	 * @return mixed
	 */
	function getName( $item ) {
		return '#' . $item['id'];
	}

	function getSyncDate( $item ) {
		return null;
	}

	function getID( $item ) {
		return $item['id'];
	}

	function getGUID( $item ) {
		return null;
	}
}
