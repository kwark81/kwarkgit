<?php
namespace lk;

class lk
{
     protected static $data = array();
     public static $userID = false;	
    
     public static function get($name,$args=array())
     {
		if(!self::$data[$name])
		{
			$fname = strtolower($name);
			if(file_exists(LKLIB.$fname.'.php'))
			{
        	       		require_once(LKLIB.$fname.'.php');
			}
			$className = __NAMESPACE__.'\\'.PRX.$name;
			self::$data[$name] = new $className($args);
		}
		return self::$data[$name];
    }

    
    public static function rem($name)
    {
        if (isset(self::$data[$name]))
	{
            unset(self::$data[$name]);
        }
    }
    
    public static function dump($data,$name = 'dump'){echo '<pre> '.$name.' - '; print_r($data); echo '</pre>';}

} 
