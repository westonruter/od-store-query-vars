<?php
/**
 * Plugin Name: Optimization Detective Store Query Vars
 * Plugin URI: https://github.com/westonruter/od-store-query-vars
 * Description: Stores the Query Vars with a URL Metric in the Optimization Detective plugin. This is useful for debugging URL Metrics, in particular what the slug was computed from.
 * Requires at least: 6.5
 * Requires PHP: 7.2
 * Requires Plugins: optimization-detective
 * Version: 0.1.2
 * Author: Weston Ruter
 * Author URI: https://weston.ruter.net/
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * Text Domain: optimization-detective-store-user-agent
 * Update URI: https://github.com/westonruter/od-store-query-vars
 * GitHub Plugin URI: https://github.com/westonruter/od-store-query-vars
 *
 * @package OptimizationDetective\StoreQueryVars
 */

namespace OptimizationDetective\StoreQueryVars;

// Important: If a plugin manually adds query vars which aren't in $wp->public_query_vars then the URL Metric storage will be rejected.
add_filter(
	'od_url_metric_schema_root_additional_properties',
	static function ( array $properties ): array {
		global $wp;
		$query_vars_properties = array(
			// Introduced by od_get_normalized_query_vars().
			'user_logged_in' => array(
				'type' => 'boolean',
			),
		);

		/** This filter is documented in wp-includes/class-wp.php */
		$query_vars = apply_filters( 'query_vars', $wp->public_query_vars );

		foreach ( $query_vars as $key ) {
			$query_vars_properties[ $key ] = array(
				'type'      => array( 'string', 'number', 'boolean' ),
				'maxLength' => 100, // Something reasonable to guard against abuse.
			);
		}
		$properties['queryVars'] = array(
			'type'                 => 'object',
			'properties'           => $query_vars_properties,
			'additionalProperties' => false,
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
