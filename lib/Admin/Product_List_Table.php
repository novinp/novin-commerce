<?php

namespace MobinDev\Novin_Commerce\Admin;

use As247\WpEloquent\Database\Eloquent\Collection;
use MobinDev\Novin_Commerce\Models\Sync;
use Morilog\Jalali\Jalalian;

class Product_List_Table extends List_Table {
	protected $name = 'product';

// just the barebone implementation.
	public function get_columns() {
		return [
			'cb'             => '<input type="checkbox" />',
			'thumb'          => '<span class="wc-image tips">تصویر</span>',
			'name'           => 'نام محصول',
//			'slug'           => 'شناسه محصول',
			'category'       => 'دسته‌بندی',
			'price'          => 'قیمت',
			'stock_quantity' => 'موجودی',
			'id'             => 'ID',
			'guid'           => 'GUID',
			'sync_date'      => 'زمان همگام‌سازی',
		];
	}

	/**
	 * @param \WC_Product $item
	 * @param string $column_name
	 *
	 * @return mixed|void
	 */
	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'thumb':
				return $item->get_image( 'thumbnail' );
			case 'name':
				return $item->get_name();
			case 'slug':
				return urldecode( $item->get_slug() );
			case 'category':
				return wc_get_product_category_list( $item->get_id() );
			case 'price':
				return wc_price( $item->get_price() );
			case 'stock_quantity':
				return $item->get_stock_quantity();
			case 'id':
				return $item->get_id();

		}
	}



	protected function get_sortable_columns() {
		return [
			'id'             => array( 'id', true ),
			'name'           => 'نام محصول',
			'slug'           => 'slug',
			'category'       => 'category',
			'price'          => 'price',
			'stock_quantity' => 'stock_quantity',
			'guid'           => 'guid',
			'sync_date'      => 'sync_date',

		];
	}


	public function fetchTableData( ) {
		$query_args = [];
		$search     = isset( $_REQUEST['s'] ) ? wp_unslash( trim( $_REQUEST['s'] ) ) : null;

		if ( $search ) {
			$query_args['like_name'] = $search;
		}

		$query = new \WC_Product_Query(
			array_merge(
				[ 'limit' => - 1 ],
				$query_args
			)
		);

		return $query->get_products();
	}

	function getName($item) {
		return $item->get_name();
	}

	function getSyncDate($item) {
		return $item->get_meta( '_np-api-sync-date' );
	}

	function getID( $item ) {
		return $item->get_id();
	}

	function getGUID( $item ) {
		return $item->get_meta( 'guid' );
	}
}
