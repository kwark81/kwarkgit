<?
namespace lk;

class lkDB{
	private $db;
	/*private static $instance;
  	private $db;
	
	public static function getInstance() {
        	if (!(self::$instance instanceof self)) {
            		self::$instance = new self();
        	}
        	return self::$instance;
     	}

     	private function __clone() {
    	}
        */
    	function __construct()
	{
		global $db;
		if(!$db){
			require_once (ENGINE_DIR . '/classes/mysql.php');
			require_once (ENGINE_DIR . '/data/dbconfig.php');
		}
		$this->db = $db;
     	}
	
     	public function data($query,$key=false)
	{
		//lk::dump($query);
		if(!strlen($query)){throw new \Exception('empty query'); return false;}
		$result = false;
		$this->db->query($query);
		if($this->db->num_rows() > 0)
		{
			$result = array();
			while($val = $this->db->get_row())
			{
				if($key and isset($val[$key])){$result[$val[$key]] = $val;}
				else{$result[] = $val;}
			}	
		}
		//elseif($this->db->num_rows()==1){
			//$result = $this->db->get_row();
		//}
		elseif($this->db->get_affected_rows()>0){$result = $this->db->get_affected_rows();}
		return $result;
	}

	public function safedata($query,$key=false)
	{
		$result = false;
		$this->db->query($this->db->safesql($query));
		if($this->db->get_affected_rows()>0){$result = $this->db->get_affected_rows();}
		return $result;
	}
}
?>