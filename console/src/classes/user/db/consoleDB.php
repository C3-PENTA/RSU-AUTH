<?PHP
/**
 * DB connection information
 *
 * @author  daekyu.seo dkseo@pentasecurity.com
 * @copyright   2014 Penta Security
*/
namespace classes\user\db;
use classes\user\db\dbQueryClass;

class consoleDB extends dbQueryClass {

    public function __construct(){

		$db_host = "consoledb";	
		$db_name = "authentica";	
		$db_user = "root";	
		$db_pass = "rootpass!";	

        $this->connectDB( $db_host, $db_user, $db_pass, $db_name );
    }
}
