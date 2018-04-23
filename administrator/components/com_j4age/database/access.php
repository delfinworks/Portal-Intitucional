<?php

              
/**
 * @package j4age
 * @copyright Copyright (C) 2009-@THISYEAR@ j4age Team. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 *
 * Thank you to the project j4age and it's team, on which roots this project is build on.
 */
             
          
 
if( !defined( '_JS_STAND_ALONE' ) && !defined( '_JEXEC' ) ) {
	die( 'JS: No Direct Access to '.__FILE__ );
}


/**
 * This file provide access to JoomlaStats database in:
 *     - 'joomla v1.5.7 Native' environment
 *     - without joomla
 *
 *  To get access to database JoomlaStats use a little modified classes from Joomla CMS
 *
 *     require_once( dirname(__FILE__) .DIRECTORY_SEPARATOR. 'database' .DIRECTORY_SEPARATOR. 'access.php' );
 */
class js_JSDatabaseAccess
{
	/** it hold reference to DB object. Object is holded in other place */
	var $db = null;
	
	/** constructor initialize database access */
	function __construct() {
		$this->_getDB();
	}
	
	/**
	 * A hack to support __construct() on PHP 4
	 *
	 * Hint: descendant classes have no PHP4 class_name() constructors,
	 * so this constructor gets called first and calls the top-layer __construct()
	 * which (if present) should call parent::__construct()
	 *
	 * code from Joomla CMS 1.5.10 (thanks!)
	 *
	 * @access	public
	 * @return	Object
	 * @since	1.5
	 */
	function js_JSDatabaseAccess()
	{   
		$args = func_get_args();
		call_user_func_array(array(&$this, '__construct'), $args);
	}

    function &getInstance()
    {
        global $js_dbaccess_instance;
        if($js_dbaccess_instance == null)
        {
            $js_dbaccess_instance = new js_JSDatabaseAccess();
        }
        return $js_dbaccess_instance;
    }

    function debug($debugOn = false )
    {
       $this->db->debug($debugOn ? 1 : 0); 
    }

	function _getDB() {

		if( defined( '_JEXEC' ) ) {
			//joomla 1.5
			$this->db =& JFactory::getDBO();
		} else if ( defined( '_JS_STAND_ALONE' ) ) {
			if (!defined('DS'))
				define('DS', DIRECTORY_SEPARATOR);

			//order is important!!!
			//get resources needed by JoomlaStats to access to database
				require_once( dirname(__FILE__) .DIRECTORY_SEPARATOR. 'stand.alone.configuration.php' );
				require_once( dirname(__FILE__) .DIRECTORY_SEPARATOR. 'res_joomla' .DIRECTORY_SEPARATOR. 'object.php' );
				require_once( dirname(__FILE__) .DIRECTORY_SEPARATOR. 'res_joomla' .DIRECTORY_SEPARATOR. 'database.php' );
				
			
			//create resources needed by JoomlaStats to work correctly
				//global $database;
				$JSStandAloneConfiguration = new js_JSStandAloneConfiguration();
				$this->db =& JDatabase::getInstance($JSStandAloneConfiguration->JConfigArr);

				//show error if occure 
				//it is	VERY important - without this is very hard to determine what is not working (we are ouside joomla!)
				if ( is_object($this->db) == false ) {
					echo $this->db;
					echo '<br/><br/><br/><br/>';
				}
		} else {
			//someone try to hack page? or author forgot apply define( '_JS_STAND_ALONE' )
		}
        /*if(!($this->db instanceof js_JDatabaseMOC))
        {
            $this->db = new js_JDatabaseMOC($this->db);
        } */
	}
	
	/** This function should not be here, but now there is no better place for it 
	 * @todo - make this function works for 'stand alone' version */
	function isMySql40orGreater()
    {
        $JSDatabaseAccess = js_JSDatabaseAccess::getInstance();
		$verParts = explode( '.', $JSDatabaseAccess->db->getVersion() );
		//return ($verParts[0] == 5 || ($verParts[0] == 4 && $verParts[1] == 1 && (int)$verParts[2] >= 2));// oryginal code from joomla - works in j1.0.15 and j1.5.8
		return (bool) ($verParts[0] >= 4);
		//return false;//to tests
	}

	/**
	 *  eg.
	 *      $datetime = '2009-03-25 16:42:56'
	 *          $date will be '2009-03-25'
	 *          $time will be '16:42:56'
	 *
	 *  return true when this is datetime, false when it is only date
	 */	
	function splitDateTime( $datetime, &$date, &$time ) {
		$pieces = explode(' ', $datetime);
		$date = $pieces[0];

		if( isset( $pieces[1] ) ) {
			$time = $pieces[1];
			return true;
		}

		return false;
	}
	
	/** 
	 *  This function convert dates to SQL WHERE condition
	 *     Both dates are inclusive
	 *  
	 *  Now this function works on colum of type 'DATETIME' and name 'time'
	 *
	 *  date formats: 
	 *      ''             - use '' to omit date and time limitation
	 *      '2009-03-25'
	 *      '2009-3-9'
	 *      '2009-03-25 16:42:56' (NOT RECOMENDED - much slower)
	 */
	/*function getConditionStringFromDates( $datetime_from, $datetime_to ) {
		if ( ($datetime_from === '') && ($datetime_to === '') )
			return '1=1';
			
		if ($datetime_from == $datetime_to) {
			$date = '';
			$time = '';
			$isDateTime = $this->splitDateTime( $datetime_from, $date, $time );

			if( $isDateTime == false)
				return 'v.visit_date=\''.$date.'\'';
			else
				return '(v.visit_date=\''.$date.'\' AND v.visit_time=\''.$time.'\')';
		}
			
		$res_from = '';
		if ($datetime_from !== '') {
			$date = '';
			$time = '';
			$isDateTime = $this->splitDateTime( $datetime_from, $date, $time );

			if( $isDateTime == true)
				$res_from .= 'CAST(CONCAT(v.visit_date, \' \', v.visit_time) AS DATETIME)>=\''.$date.' '.$time.'\''; //@todo maybe this line could be optimized. Tests are needed
			else
				$res_from .= 'v.visit_date>=\''.$date.'\'';
		}
		
		$res_to = '';
		if ($datetime_to !== '') {
			$date = '';
			$time = '';
			$isDateTime = $this->splitDateTime( $datetime_to, $date, $time );

			if( $isDateTime == true)
				$res_to .= 'CAST(CONCAT(v.visit_date, \' \', v.visit_time) AS DATETIME)<=\''.$date.' '.$time.'\''; //@todo maybe this line could be optimized. Tests are needed
			else
				$res_to .= 'v.visit_date<=\''.$date.'\'';
		}
			
		if ( ($res_from !== '') && ($res_to !== '') )
			return '('.$res_from.' AND '.$res_to.')';
		
		return $res_from.$res_to;//one of it always will be empty
	}  */

	/**
	 *  This function convert dates to SQL WHERE condition
	 *     Both dates are inclusive
	 *
	 *  Now this function works on colum of type 'DATETIME' and name 'time'
	 *
	 *  date formats:
	 *      ''             - use '' to omit date and time limitation
	 *      '2009-03-25'
	 *      '2009-3-9'
	 *      '2009-03-25 16:42:56' (NOT RECOMENDED - much slower)
	 */
	function getConditionStringFromTimestamps( $datetime_from, $datetime_to ) {
		if ( empty($datetime_from) && empty($datetime_to) )
			return '1=1';

		if ($datetime_from == $datetime_to) {

            $datetime_from = mktime(0,0,0, date('n', $datetime_from), date('j', $datetime_from), date('Y', $datetime_from));
            $datetime_to = mktime(23,59,59, date('n', $datetime_from), date('j', $datetime_from), date('Y', $datetime_from));

            return " v.changed_at >= $datetime_from and v.changed_at <= $datetime_to ";
		}

		$res_from = '';
		if (!empty($datetime_from)) {
				$res_from = " v.changed_at >= $datetime_from ";
		}

		$res_to = '';
		if (!empty($datetime_to)) {
				$res_to = " v.changed_at <= $datetime_to ";
		}

		if ( !empty($res_from)  && !empty($res_to) )
			return '('.$res_from.'AND'.$res_to.')';

		return $res_from.$res_to;//one of it always will be empty
	}

	/**
	 *  performing many DB queries
	 *
	 * @since 2.3.x (mic)
	 * @param array $queries_arr    holds all queries
	 * @param bool	$printErrorMsg  supress error message
	 * @return bool                 true if all queries were successful
	 */
	function populateSQL( $queries_arr, $printErrorMsg = true ) {
		$bResult = true;

		foreach( $queries_arr as $query) {
			$this->db->setQuery( $query );
			if( !$this->db->query() ) {
				$bResult &= false;
				if( $printErrorMsg ) {
					echo '<br/>' . $this->db->getErrorMsg() . '<br/>' . $query;
				}
			}
		}
		
		return $bResult;
	}

    function getTableIndices($tableName)
    {
        $tableName = strtolower(trim($tableName));
        $query = "SHOW INDEX FROM  `$tableName`";
        $this->db->setQuery( $query );
        $rows = $this->db->loadObjectList();

        if ($this->db->getErrorNum() > 0)
        {
            js_echoJSDebugInfo("".$this->db->getErrorMsg());
            return array();
        }
        return $rows;
    }

    /**
     * @param  $tableName
     * @param  $column_name
     * @returns index row if match found or null, if nothing found
     */
    function hasTableIndexForColumn($tableName, $column_name )
    {
        $column_name = strtolower(trim($column_name));
        $indices = $this->getTableIndices($tableName);
        $matchPrimary = null; 
        foreach($indices as $index)
        {
            $tableColumn = strtolower(trim($index->Column_name));
            if(strcmp($tableColumn, $column_name) == 0)
            {
               $key_name = strtolower(trim($index->Key_name));
               if(strcmp($key_name, 'primary') == 0)
               {
                  $matchPrimary = $index;
               }
               else
               {
                  return $index;
               }
            }
        }
        if($matchPrimary != null)
        {
            return $matchPrimary;
        }
        return null;
    }

    /**
     * @param  $tableName
     * @param  $column_name
     * @returns index row if match found or null, if nothing found
     */
    function hasTableKeyName($tableName, $keyname )
    {
        $keyname = strtolower(trim($keyname));
        $indices = $this->getTableIndices($tableName);
        foreach($indices as $index)
        {
            $key_name = strtolower(trim($index->Key_name));
            if(strcmp($key_name, $keyname) == 0)
            {
                return true;
            }
        }
        return false;
    }

    function addIndex($tableName, $column_name, $index_name = null)
    {
        if(empty($index_name))
        {
            $index_name = $column_name;
        }
        $query = "ALTER IGNORE TABLE `$tableName` ADD INDEX $index_name ($column_name)";
        $this->db->setQuery( $query );

        if( !$this->db->query() ) {
            if ($this->db->getErrorNum() > 0)
            {
                js_echoJSDebugInfo("".$this->db->getErrorMsg());
            }
            return false;
        }
        return true;
    }

    function dropIndex($tableName, $index_name)
    {
        if(empty($index_name))
        {
            $index_name = $column_name;
        }
        $query = "ALTER IGNORE TABLE `$tableName` DROP INDEX $index_name";
        $this->db->setQuery( $query );

        if( !$this->db->query() ) {
            if ($this->db->getErrorNum() > 0)
            {
                js_echoJSDebugInfo("".$this->db->getErrorMsg());
            }
            return false;
        }
        return true;
    }

   /* function js_hasTable($tablename)
    {
        $query = "SHOW TABLES";

		$this->setQuery( 'SHOW TABLES' );
		return $this->loadResultArray();

    }*/

    function js_hasTableColumn( $columns, $column )
    {
       $column = strtolower($column);
       foreach($columns as $c)
       {
           if(strtolower($c['Field']) == $column){
               return true;
           }
       }
       return false;
    }

    function js_isColumnTypeOf( $columns, $column, $type )
    {
        $column = strtolower(trim($column));
        $type = strtolower(trim($type));
        foreach($columns as $c)
        {
            if(strtolower($c['Field']) == $column)
            {
                if( strpos(strtolower($c['Type']), $type) != false)
                {
                    return true;
                }
                return false;
            }
        }
        return false;
    }

    function js_getColumnCollation( $columns, $column )
    {
        $column = strtolower(trim($column));
        foreach($columns as $c)
        {
            if(strtolower($c['Field']) == $column)
            {
                return strtolower($c['Collation']);
            }
        }
        return null;
    }

    function js_getTableColumns($dbtable )
    {
        $query = "show full columns from $dbtable";
        $this->db->setQuery( $query );
        $columns = $this->db->loadAssocList();
        if(empty($columns))
        {
            return array();
        }
        //todo use Joomla method: $columns = $JSDatabaseAccess->db->getTableFields($dbtable);
        return $columns;
    }

    function executionTimeAvailable( $max = -1, $mintime = 5)
    {
        global $js_start;
        if(empty($js_start))
        {
           $js_start = time();
        }
        $max_execution_time = ini_get('max_execution_time');

        /**
         * Make sure that we do not run into time outs related to SQL
         */
        $connect_timeout = ini_get('mysql.connect_timeout');
        if( $connect_timeout > 15 && ($max_execution_time <= 0 || $max_execution_time > $connect_timeout))
        {
           $max_execution_time = $connect_timeout;
        }
        $connect_timeout = ini_get('max_input_time');
        if( $connect_timeout > 15 && ($max_execution_time <= 0 || $max_execution_time > $connect_timeout))
        {
           $max_execution_time = $connect_timeout;
        }

        if( $max_execution_time > 5 )
        {
           $max_execution_time = $max_execution_time - 5; //We leave some time to the remaining scripts
        }
        if( $max_execution_time <= 0 && $max > 0 && $max < $max_execution_time  )
        {
           $max_execution_time = $max; //After 1 Minute we should stop
        }

        $limit = $js_start + $max_execution_time;
        //$minlimit = $js_now + $mintime;
        $now = time();
        $timeleft = $limit - $now;
        if($timeleft < $mintime)
        {
            js_echoJSDebugInfo("Potential PHP Timeout prevented - Now: $now => Started at $js_start end at $limit using max_execution_time $max_execution_time time left $timeleft");
            return false;
        }
        if($now < $limit )
        {
            //js_echoJSDebugInfo(" PHP Time - Now: $now => Started at $js_start end at $limit using max_execution_time $max_execution_time time left $timeleft");
          return true;
        }
        js_echoJSDebugInfo("Potential PHP Timeout prevented - Now: $now => Started at $js_start end at $limit using max_execution_time $max_execution_time time left $timeleft");
        return false;
    }
}
