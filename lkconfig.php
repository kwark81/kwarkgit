<?
//session_start();

if( !defined( 'DATALIFEENGINE' ) ) {
	define( 'DATALIFEENGINE', true );
}
if( !defined( 'ROOT_DIR' ) ) {
	define( 'ROOT_DIR', dirname(dirname(__FILE__)));
}

if( !defined( 'ENGINE_DIR' ) ) {
	define( 'ENGINE_DIR', ROOT_DIR . '/engine' );
}

define('PRX', 'lk');

define('LKDIR', ROOT_DIR . '/persacc/');
define('LKLIB', LKDIR . 'lib/');
define('SKIN_URL', '/uploads/skins/');
define('CLOAK_URL', '/uploads/cloaks/');
define('SKIN_DIR', ROOT_DIR.SKIN_URL);
define('CLOAK_DIR', ROOT_DIR.CLOAK_URL);
define('FILE_MAX_SIZE', 1000);


require_once LKDIR.'lk.php';
//require_once LKDIR.'lkdb.php';
//require_once LKDIR.'servers.php';
//require_once LKDIR.'lkuser.php';
//require_once LKDIR.'lkskin.php';

?>