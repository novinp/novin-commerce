<?php

namespace MobinDev\Novin_Commerce\Models;

use As247\WpEloquent\Database\Eloquent\Model;

class Sync extends Model {
	protected $table = 'novin_commerce_syncs';
	protected $guarded = ['id'];

	public static function doAddAction() {
		do_action( 'novincommerce-add-sync-items' );
	}

	public static function doDeleteAction() {

		do_action( 'novincommerce-delete-sync-items' );
	}

	public static function insertItem( $item_id, $item_type ) {
		$result = self::updateOrInsert( [ 'item_id' => $item_id, 'item_type' => $item_type ], [] );
		self::doAddAction();
		return $result;
	}

	public static function insertProduct( $item_id ) {
		return self::insertItem( $item_id, 'product' );
	}

	public static function insertOrder( $item_id ) {
		return self::insertItem( $item_id, 'order' );
	}

	public static function insertCategory( $item_id ) {
		return self::insertItem( $item_id, 'category' );
	}

	public static function insertUser( $item_id ) {
		return self::insertItem( $item_id, 'user' );
	}

	public static function insertVariation( $item_id ) {
		return self::insertItem( $item_id, 'variation' );
	}

}
