<?php
/**
 * Robert Forbids Riffraff
 *
 * @package       RIFFRAFF
 * @author        Robert Joosten
 * @license       gplv2
 * @version       2024060604
 *
 * @wordpress-plugin
 * Plugin Name:   Robert Forbids Riffraff
 * Description:   This plugin adds one php file that makes a wp-site somewhat more secure.
 * Version:       2024060604
 * Author:        Robert Joosten
 * Text Domain:   riffraff
 * Domain Path:   /languages
 * License:       GPLv2
 * License URI:   https://www.gnu.org/licenses/gpl-2.0.html
 *
 * You should have received a copy of the GNU General Public License
 * along with Robert Forbids Riffraff. If not, see <https://www.gnu.org/licenses/gpl-2.0.html/>.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

// Shameslessly nicked from https://www.wpexplorer.com/prevent-user-enumeration-wordpress/
// Shameslessly nicked from https://www.wpexplorer.com/prevent-user-enumeration-wordpress/
add_action( 'rest_authentication_errors', function( $access ) {
        if ( is_user_logged_in() ) {
                return $access;
        }

        if ( ( preg_match( '/users/i', $_SERVER['REQUEST_URI'] ) !== 0 )
                || ( isset( $_REQUEST['rest_route'] ) && ( preg_match( '/users/i', $_REQUEST['rest_route'] ) !== 0 ) )
        ) {
                return new \WP_Error(
                        'rest_cannot_access',
                        'Only authenticated users can access the User endpoint REST API.',
                        [
                                'status' => rest_authorization_required_code()
                        ]
                );
        }
 
        return $access;
} );
 
// --------------------------------------------------

add_filter( 'wp_sitemaps_add_provider', function( $provider, $name  ) {
        if ( 'users' === $name ) {
                return false;
        }
 
        return $provider;
}, 10, 2 );
 
// --------------------------------------------------
 
add_filter( 'login_errors', function() {
        return 'Er heeft zich een fout voorgedaan. Probeer het later nog eens.';
} );
 
add_action( 'init', function() {
        if ( isset( $_REQUEST['author'] )
                && preg_match( '/\\d/', $_REQUEST['author'] ) > 0
                && ! is_user_logged_in()
        ) {
                wp_die( 'forbidden - number in author name not allowed = ' . esc_html( $_REQUEST['author'] ) );
        }
} );
 
// --------------------------------------------------
 
add_filter( 'remove_author_from_oembed', function( $data  ) {
        unset( $data['author_url'] );
        unset( $data['author_name'] );

        return $data;
}, 10, 2 );

//eof

