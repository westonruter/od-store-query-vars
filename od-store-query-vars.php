<?php
/**
 * Plugin Name: Optimization Detective Store Query Vars
 * Plugin URI: https://gist.github.com/westonruter/???
 * Description: Stores the Query Vars with a URL Metric. This is useful for debugging URL Metrics, in particular what the slug was computed from.
 * Requires at least: 6.5
 * Requires PHP: 7.2
 * Requires Plugins: optimization-detective
 * Version: 0.1.0
 * Author: Weston Ruter
 * Author URI: https://weston.ruter.net/
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * Text Domain: optimization-detective-store-user-agent
 * Update URI: https://gist.github.com/westonruter/???
 * Gist Plugin URI: https://gist.github.com/westonruter/???
 *
 * @package OptimizationDetective\StoreQueryVars
 */

namespace OptimizationDetective\StoreQueryVars;

add_filter(
	'od_url_metric_schema_root_additional_properties',
	static function ( array $properties ): array {
		global $wp;
		$query_vars_properties = array(
			'user_logged_in' => array(
				'type' => 'boolean',
			),
		);
		foreach ( $wp->public_query_vars as $key ) {
			$query_vars_properties[ $key ] = array(
				'type' => array( 'string', 'number' ),
			);
		}
		$properties['queryVars'] = array(
			'type'       => 'object',
			'properties' => $query_vars_properties,
		);
		return $properties;
	}
);

add_filter(
	'od_extension_module_urls',
	static function ( array $urls ): array {
		$urls[] = plugins_url( 'detect.js', __FILE__ );
		return $urls;
	}
);

add_action(
	'wp_footer',
	static function (): void {
		if ( ! od_can_optimize_response() ) {
			return;
		}
		?>
		<script type="application/json" id="od-normalized-query-vars"><?php echo wp_json_encode( od_get_normalized_query_vars() ); ?></script>
		<?php
	}
);
