<?PHP
/**
 * DB connect & query Class
 *
 * DB커넥션하고 쿼리 날리고 결과값 얻는 클래스
 *
 * @author  daekyu.seo dkseo@pentasecurity.com
 * @copyright   2014 Penta Security
*/
namespace classes\user\db;
use Exception;

class dbQueryClass {
    private $db;
    private $queryString;
    private $result;

    protected function connectDB($host, $user, $passwd, $dbname){
        $this->db = mysqli_connect($host, $user, $passwd, $dbname);
        $this->query("set names utf8");
        if (mysqli_connect_errno()) {
            printf("Connect failed: %s\n", mysqli_connect_error());
            exit;
        }
    }

    public function query($query){
        $this->queryString = $query;
        $result = mysqli_query($this->db, $query);
        if(!$result) $this->trace_error(mysqli_error($this->db));
        else $this->result = $result;
    }

    public function simple_query($query){
        $this->query($query);
        $row = mysqli_fetch_row($this->result);
        return $row[0];
    }

    public function next_row($type="assoc"){
        switch (strtolower($type)){
            case "assoc" : return mysqli_fetch_assoc($this->result); return;
            case "row" : return mysqli_fetch_row($this->result); return;
            case "array" : return mysqli_fetch_array($this->result); return;
            default : return mysqli_fetch_array($this->result); return;
        }
        return mysqli_fetch_array($this->result);

    }

    public function get_num_rows(){
        return mysqli_num_rows($this->result);
    }

    public function insert($query){
        $result = mysqli_query($this->db, $query);
        if(!$result) $this->trace_error(mysqli_error($this->db));
        //return mysqli_affected_rows($this->db);
        return mysqli_insert_id($this->db);
    }

    public function update($query){
        $result = mysqli_query($this->db, $query);
        if(!$result) $this->trace_error(mysqli_error($this->db));
        return mysqli_affected_rows($this->db);
    }

    public function trace_error($msg) {
		throw new Exception($msg);
    }

    public function close() {
        mysqli_free_result($this->result);
        mysqli_close($this->db);

    }

    public function destroy() {
        $this->close();
    }
}
