const fs = require( 'fs' );
const path = require( 'path' );
const { wp, STATE_FILE } = require( './utils/fixtures' );

/**
 * Restore the plugin settings and delete everything the suite created.
 */
module.exports = async () => {
	if ( process.env.WPS_E2E_KEEP ) {
		return; // Debugging: leave fixtures in place.
	}
	const result = wp( 'teardown' );
	console.log( `E2E teardown: settings restored=${ result.restored }, test orders deleted=${ result.deleted_orders }` );
	for ( const file of [ STATE_FILE, path.join( path.dirname( STATE_FILE ), 'admin.json' ) ] ) {
		if ( fs.existsSync( file ) ) {
			fs.unlinkSync( file );
		}
	}
};
