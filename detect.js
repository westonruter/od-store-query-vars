/**
 * @typedef {import("../optimization-detective/types.ts").InitializeArgs} InitializeArgs
 * @typedef {import("../optimization-detective/types.ts").InitializeCallback} InitializeCallback
 */

/**
 * Initializes extension.
 *
 * @type {InitializeCallback}
 * @param {InitializeArgs} args Args.
 */
export async function initialize( { extendRootData } ) {
	const script = document.getElementById( 'od-normalized-query-vars' );
	if ( script instanceof HTMLScriptElement ) {
		extendRootData( { queryVars: JSON.parse( script.text ) } );
	}
}
