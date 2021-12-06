<?PHP
/**
 * DB connection information
 *
 * @author  daekyu.seo dkseo@pentasecurity.com
 * @copyright   2014 Penta Security
*/
namespace classes\user\db;
use classes\user\db\dbQueryClass;

class x509DB extends dbQueryClass {

    public function __construct(){

		$db_host = "x509";	
		$db_name = "webca";	
		$db_user = "webca";	
		$db_pass = "penta@webca!";	

        $this->connectDB( $db_host, $db_user, $db_pass, $db_name );
    }
}
