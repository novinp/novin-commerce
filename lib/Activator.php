<?php

/**
 * Fired during plugin activation
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Novin_Commerce
 * @subpackage Novin_Commerce/includes
 */

namespace MobinDev\Novin_Commerce;

use As247\WpEloquent\Application;
use As247\WpEloquent\Database\Schema\Blueprint;
use As247\WpEloquent\Support\Facades\DB;
use As247\WpEloquent\Support\Facades\Schema;
use MobinDev\Novin_Commerce\Common\SettingAPI;

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Novin_Commerce
 * @subpackage Novin_Commerce/includes
 * @author     Seyed Mobin Avazolhayat <mobin7332@gmail.com>
 */
class Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		Application::bootWp();
		self::createTables();
		self::setSettings();
	}

	public static function createTables() {

		if ( ! Schema::hasTable( 'novin_commerce_syncs' ) ) {
			Schema::create( 'novin_commerce_syncs', function ( Blueprint $table ) {
				$table->increments( 'id' );
				$table->bigInteger( 'item_id' );
				$table->enum( 'item_type', [ 'product', 'category', 'user', 'order', 'variation' ] );
				$table->timestamp( 'created_at' )->default( DB::raw( 'CURRENT_TIMESTAMP' ) );
				$table->timestamp( 'updated_at' )->default( DB::raw( 'CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP' ) );
				$table->unique( [ 'item_id', 'item_type' ] );
			} );
		}
	}

	public static function setSettings() {
		if ( ! SettingAPI::get( 'woocommerce_analytics' ) ) {
			SettingAPI::set( 'woocommerce_analytics', 'off' );
		}
		if ( ! SettingAPI::get( 'api_url' ) ) {
			SettingAPI::set( 'api_url', 'https://novinrank.ir/' );
		}

		//turn off woocommerce tracking
		update_option( 'woocommerce_allow_tracking', 'no' );
		//turn off woocommerce marketplace suggestions
		update_option( 'woocommerce_show_marketplace_suggestions', 'no' );

		// update Permalink
		add_rewrite_endpoint( 'novin-commerce-transactions', EP_PAGES );
		flush_rewrite_rules();
	}
}
