/**
 * Finalizes extension.
 *
 * @type {FinalizeCallback}
 * @param {FinalizeArgs} args Args.
 */
export async function finalize( { extendRootData } ) {
	const script = document.getElementById( 'od-normalized-query-vars' );
	if ( script instanceof HTMLScriptElement) {
		extendRootData( { queryVars: JSON.parse( script.text ) } );
	}
}
