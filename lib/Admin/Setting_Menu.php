<?php

namespace MobinDev\Novin_Commerce\Admin;

use MobinDev\Novin_Commerce\Common\SettingAPI;
use MobinDev\Novin_Commerce\Plugin;

class Setting_Menu {
	/**
	 * The plugin's instance.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    Plugin $plugin This plugin's instance.
	 */
	private $plugin;

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
		$this->save();

	}

	public function save() {
		if ( ! isset( $_POST['submit'] ) ) {
			return;
		}
		check_admin_referer( 'novin-settings' );
		$woocommerce_analytics = $_POST['woocommerce_analytics'] ?? null;
		if ( in_array( $woocommerce_analytics, [ 'on', 'off' ] ) ) {
			SettingAPI::set( 'woocommerce_analytics', $woocommerce_analytics );
		}

		$woocommerce_allow_tracking = $_POST['woocommerce_allow_tracking'] ?? null;
		if ( in_array( $woocommerce_allow_tracking, [ 'yes', 'no' ] ) ) {
			update_option( 'woocommerce_allow_tracking', $woocommerce_allow_tracking );
		}
		$woocommerce_show_marketplace_suggestions = $_POST['woocommerce_show_marketplace_suggestions'] ?? null;
		if ( in_array( $woocommerce_show_marketplace_suggestions, [ 'yes', 'no' ] ) ) {
			update_option( 'woocommerce_show_marketplace_suggestions', $woocommerce_show_marketplace_suggestions );
		}


		$api_user = $_POST['api_user'] ?? null;
		$api_pass = $_POST['api_pass'] ?? null;
		$api_url  = $_POST['api_url'] ?? null;
		if ( $api_user ) {
			SettingAPI::set( 'api_user', trim( $api_user ) );
		}
		if ( $api_pass ) {
			SettingAPI::set( 'api_pass', trim( $api_pass ) );
		}
		if ( $api_url and in_array( $api_url, array_keys( self::getApiUrls() ) ) ) {
			SettingAPI::set( 'api_url', $api_url );
		} else {
			AdminNotice::addWarningDismissible( 'سرور انتخاب شده معتبر نیست!', 2 );
		}


		AdminNotice::addSuccessDismissible( 'تنظیمات با موفقیت ذخیره شد.', 2 );
		$redirect_url = $_POST['_wp_http_referer'] ?? null;
		if ( $redirect_url ) {
			wp_redirect( $redirect_url );
		}

	}

	public function output() {
		?>
		<div class="wrap">
			<h1>تنظیمات</h1>
			<form method="post">
				<?php wp_nonce_field( 'novin-settings' ) ?>

				<h2 class="title">تنظیمات عمومی</h2>
				<table class="form-table" role="presentation">

					<tbody>

					<tr>
						<th scope="row"><label for="woocommerce_analytics">تجزیه تحلیل</label></th>
						<td>
							<select name="woocommerce_analytics" id="woocommerce_analytics"
									aria-describedby="woocommerce_analytics-description">
								<option
									value="on" <?php echo SettingAPI::get( 'woocommerce_analytics' ) === 'on' ? 'selected' : ''; ?>>
									فعال
								</option>
								<option
									value="off" <?php echo SettingAPI::get( 'woocommerce_analytics' ) === 'off' ? 'selected' : ''; ?>>
									غیرفعال
								</option>

							</select>
							<p class="description" id="woocommerce_analytics-description">با غیر‌فعال کردن این گزینه
								فروش شما
								در قسمت تجزیه و تحلیل ووکامرس لیست نمی‌شود. این می‌تواند به سرعت سایت شما کمک
								کند.</strong></p>
						</td>
					</tr>

					<tr>
						<th scope="row"><label for="woocommerce_allow_tracking">رهگیری ووکامرس</label></th>
						<td>
							<select name="woocommerce_allow_tracking" id="woocommerce_allow_tracking"
									aria-describedby="woocommerce_allow_tracking-description">
								<option
									value="yes" <?php echo get_option( 'woocommerce_allow_tracking' ) === 'yes' ? 'selected' : ''; ?>>
									فعال
								</option>
								<option
									value="no" <?php echo get_option( 'woocommerce_allow_tracking' ) === 'no' ? 'selected' : ''; ?>>
									غیرفعال
								</option>

							</select>
							<p class="description" id="woocommerce_allow_tracking-description">رهگیری اطلاعات سایت شما
								برای بهبود به سایت ووکامرس ارسال می‌شود. با غیرفعال کردن این گزینه سرعت سایت شما بهبود
								پیدا می‌کند.</strong></p>
						</td>
					</tr>

					<tr>
						<th scope="row"><label for="woocommerce_show_marketplace_suggestions">پیشنهادهای بازار
								ووکامرس</label></th>
						<td>
							<select name="woocommerce_show_marketplace_suggestions"
									id="woocommerce_show_marketplace_suggestions"
									aria-describedby="woocommerce_show_marketplace_suggestions-description">
								<option
									value="yes" <?php echo get_option( 'woocommerce_show_marketplace_suggestions' ) === 'yes' ? 'selected' : ''; ?>>
									فعال
								</option>
								<option
									value="no" <?php echo get_option( 'woocommerce_show_marketplace_suggestions' ) === 'no' ? 'selected' : ''; ?>>
									غیرفعال
								</option>

							</select>
							<p class="description" id="woocommerce_show_marketplace_suggestions-description">ووکامرس
								پیشنهادهایی برای سایت شما ارائه می‌دهد. با غیرفعال کردن این گزینه سرعت سایت شما بهبود
								پیدا می‌کند.</strong></p>
						</td>
					</tr>

					</tbody>
				</table>
				<h2 class="title">تنظیمات ارتباطی</h2>
				<table class="form-table" role="presentation">

					<tbody>

					<tr>
						<th scope="row"><label for="api_user">نام‌کاربری</label></th>
						<td>
							<input type="text"
								   value="<?php echo SettingAPI::get( 'api_user', '' ) ?>"
								   name="api_user" id="api_user">
						</td>
					</tr>

					<tr>
						<th scope="row"><label for="api_pass">رمزعبور</label></th>
						<td>
							<input type="text"
								   value="<?php echo SettingAPI::get( 'api_pass', '' ) ?>"
								   name="api_pass" id="api_pass">
						</td>
					</tr>

					<tr>
						<th scope="row"><label for="api_url">انتخاب سرور</label></th>
						<td>
							<select name="api_url" id="api_url">
								<?php
								foreach ( self::getApiUrls() as $url => $label ) {
									?>
									<option
										value="<?php echo $url ?>"
										<?php echo SettingAPI::get( 'api_url' ) === $url ? 'selected' : ''; ?>>
										<?php echo $label ?>
									</option>
									<?php
								}
								unset( $url, $label );
								?>

							</select>
						</td>
					</tr>

					</tbody>
				</table>
				<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary"
										 value="ذخیرهٔ تغییرات"></p></form>
		</div>
		<?php
	}

	public static function getApiUrls() {
		return [
			'https://novinrank.ir/'        => 'سرور ایران ۱',
			'https://api.novinp.ir/'       => 'سرور ایران ۲',
			'https://webservice.novinp.ir' => 'سرور آلمان',
		];
	}
}
