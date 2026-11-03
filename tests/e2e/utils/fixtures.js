/**
 * Bridge to tests/e2e/fixtures/wp-fixtures.php (runs inside WordPress via bin/wp-php.sh).
 */
const { execFileSync } = require( 'child_process' );
const fs = require( 'fs' );
const path = require( 'path' );

const ROOT = path.resolve( __dirname, '../../..' );
const STATE_FILE = path.join( ROOT, 'tests/e2e/.auth/state.json' );

/**
 * Run a fixture command and return its JSON output.
 *
 * @param {string} command Command.
 * @param {string} [arg]   Argument.
 * @return {any} Parsed JSON.
 */
function wp( command, arg = '' ) {
	const out = execFileSync( path.join( ROOT, 'bin/wp-php.sh' ), [ path.join( ROOT, 'tests/e2e/fixtures/wp-fixtures.php' ), command, arg ], {
		encoding: 'utf8',
		stdio: [ 'ignore', 'pipe', 'pipe' ],
	} );
	const line = out.trim().split( '\n' ).pop();
	return JSON.parse( line );
}

/**
 * Seeded state written by global-setup (product ids, admin login, base URL).
 *
 * @return {{baseUrl:string, products:{target:number, offer:number, recovery:number}, admin:{user:string, pass:string}}}
 */
function state() {
	return JSON.parse( fs.readFileSync( STATE_FILE, 'utf8' ) );
}

module.exports = { wp, state, STATE_FILE, ROOT };
