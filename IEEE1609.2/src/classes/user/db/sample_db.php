<?PHP
/**
 * DB connection information
 *
 * DB 커넥션 위한 정보 입력
 *
 * @author  daekyu.seo dkseo@pentasecurity.com
 * @copyright   2014 Penta Security
*/
namespace classes\user\db;
use classes\user\db\dbQueryClass;

class sample_db extends dbQueryClass {

    public function __construct(){

		$db_host = "127.0.0.1";	
		$db_name = "db_name";	
		$db_user = "user";	
		$db_pass = "password";	

        $this->connectDB( $db_host, $db_user, $db_pass, $db_name );
    }
}
