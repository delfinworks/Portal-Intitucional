<?php

              
/**
 * @package j4age
 * @copyright Copyright (C) 2009-@THISYEAR@ j4age Team. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 *
 * Thank you to the project j4age and it's team, on which roots this project is build on.
 */
             
          

if( !defined( '_JS_STAND_ALONE' ) && !defined( '_JEXEC' ) )
{
	die( 'JS: No Direct Access to '.__FILE__ );
}


require_once( dirname(__FILE__) .DS. 'base.classes.php' );

/**
 *	This file contain filters that are used in joomla backend to change searching criteria in statistics
 */

 /**
 *	This class makes working with time period filter easy.
 *	It can generate HTML code, create SQL query, read values from request.
 *
 *  THIS CLASS DOES NOT WORK UNDER PHP4
 *
 *
 *  code of THIS CLASS IS STRONLY DEPRECATED, even js_JSFilterTimePeriod is newer!
 */
class js_JSFilterDate
{
    /**
     *  Possible options
     *  - day
     *  - week
     *  - month
     */
    var $step = 'day';

	var $year;
	var $month;
	var $day;
	
	var $prefix = '';
	var $sufix  = '';
	
	var $year_min = 2003;
	var $year_max = 2010;//will be overriden in constructor

    var $periodIndicator = "by-date";

    var $fromDate  = null; //date in seconds / timestamp
    var $toDate = null; //date in seconds / timestamp

	
	/** we need sufix in case when we create list of date filters (eg. date to each row from sql query) */
	function js_JSFilterDate( $prefix='', $sufix='' ) {
        $after2weeks = mktime(0, 0, 0, date('m'), date('d')+2, date('Y')); //new year appears in the last 2 days of current year
        $this->year_max = date( 'Y', $after2weeks ); //we do not use js_date() for performance (it does not matter in this case)

		$this->prefix = $prefix;
		$this->sufix  = $sufix;

		$this->setDefaultDate();
	}

	/** set default values for this class */
	function setDefaultDate() {
	}

	function readDateFromRequest( $alternate_year='', $alternate_month='', $alternate_day='') {
        $mainframe = JFactory::getApplication();

		if (strlen($alternate_year) == 0)
			$alternate_year = js_gmdate('Y');
		
		if (strlen($alternate_month) == 0)
			$alternate_month = js_gmdate('n');
			
		if (strlen($alternate_day) == 0)
			$alternate_day = js_gmdate('j');
			
		$this->year  = $mainframe->getUserStateFromRequest( 'year',  'year',  $alternate_year );
		$this->month = $mainframe->getUserStateFromRequest( 'month', 'month', $alternate_month );
		$this->day   = $mainframe->getUserStateFromRequest( 'day',   'day',   $alternate_day );
	}

	function getDateStr() {
		return $this->year .'-'. ((strlen($this->month)==1) ? '0' : '') . $this->month .'-'. ((strlen($this->day)==1) ? '0' : '') . $this->day;
	}
	

	/**
	 * Create the Day dropdown
	 *
	 * @access private
	 * @return string
	 */
	function CreateDayCmb() {
       
		$html = '';

		for( $i = 1; $i <= 31; $i++ ) {
			$html .= '<option value="' . $i . '"';
			if( $this->day == $i ) {
				$html .= ' selected="selected"';
			}
			$html .= '>' . $i . '</option>' . "\n";
		}

		return $html;
	}

	/**
	 * Creates the dropdown for months
	 *
	 * @access private
	 * @return string
	 */
	function CreateMonthCmb() {
		require_once( dirname(__FILE__) .DS. 'template.html.php' );

		$html = '';
		
		$JSUtil = new js_JSUtil();
		$JSTemplate = new js_JSTemplate();
		
		for( $i=1; $i<13; $i++ ) {
			$html .= '<option value="' . $i . '"';
			if( $this->month == $i ) {
				$html .= ' selected="selected"';
			}
			$html .= '>' . $JSTemplate->monthToString($i, true) . '</option>' . "\n";
		}

		return $html;
	}

	/**
	 * Creates the year drop down
	 *
	 * @access private
	 * @return string
	 */
	function CreateYearCmb() {

		$html		= '';

		for( $i = $this->year_min; $i <= $this->year_max; $i++ ) {
			$html .= '<option value="' . $i . '"';
			if( $this->year == $i ) {
				$html .= ' selected="selected"';
			}
			$html .= '>' . $i . '</option>' . "\n";
		}

		return $html;
	}

	/** $date: '2008-06-19' */
	function SetYMD( $date='now' ) {

		$data_arr = explode('-', $date);
		if (count($data_arr) == 3) {
			$this->year  = $data_arr[0];
			$this->month = $data_arr[1];
			$this->day   = $data_arr[2];
		} else {
			$JSNowTimeStamp = js_getJSNowTimeStamp();
			$this->year  = js_gmdate( 'Y', $JSNowTimeStamp );
			$this->month = js_gmdate( 'n', $JSNowTimeStamp );
			$this->day   = js_gmdate( 'j', $JSNowTimeStamp );
		}
	}

	/**
	 * creates a javascript and dropdowns for date selection
	 *
	 * @since 2.3.x: if all months are selected, also all days will be checked
	 * @return string
	 */
	function getHtmlDateFilterCode() {
        
		$html  = '';

		$html .= '<select name="day">' . $this->CreateDayCmb() . '</select>';//<!-- combo day here -->
		$html .= '&nbsp;';
		$html .= '<select name="month">' . $this->CreateMonthCmb() . '</select>';//<!-- combo month here -->
		$html .= '&nbsp;';
		$html .= '<select name="year">' . $this->CreateYearCmb() . '</select>';//<!-- combo year here -->

		return $html;
	}
}

 
 

 
 
 

  
  
/**
 *	This class makes working with time period filter easy.
 *	It can generate HTML code, create SQL query, read values from request.
 *
 *	NOTICE: Calendar should be added to this class.
 */
class js_JSFilterTimePeriod
{
	/** default values are set in constuctor (because it is date aligned with JSNowTimeStamp */
	var $d;
	var $m;
    var $y;

    var $fromDate;
    var $step;
    var $toDate;
    var $hide = false;
    var $periodIndicator = "by-date";

	function __construct() {
		$this->setDefaultTimePeriod();
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
	function js_JSFilterTimePeriod()
	{
		$args = func_get_args();
		call_user_func_array(array(&$this, '__construct'), $args);
	}

	/** set default values for this class */
	function setDefaultTimePeriod() {
		$this->setDMY2Now();
	}

    function setLocalTimePeriod($from_hour = 0, $from_minute = 0,$from_second = 0,  $from_month = 1,$from_day = 1, $from_year, $to_hour = 23, $to_minute = 59,$to_second = 59,  $to_month = 12, $to_day = 31,  $to_year)
    {
        $this->fromDate = mktime($from_hour, $from_minute, $from_second, $from_month, $from_day, $from_year);
        $this->toDate = mktime(0, 0, 0, $to_month, $to_day, $to_year);

        $period = $this->toDate - $this->fromDate;
        $days = $period / 86400;

        if($days < 32)
        {
           $this->step = 'day' ;
        }
        else
        {
           $this->step = 'month' ;
        }

        $this->d = $from_day == $to_day ? $from_day : 'all';

        $this->m = $from_month == $to_month ? $from_month : 'all';

        $this->y = $from_year == $to_year ? $from_year : 'all';
    }

    /**
     * @param  $from timestamp based on the GMT timezone (NOT local timezone)
     * @param  $to timestamp based on the GMT timezone (NOT local timezone)
     * @return void 
     */
    function setTimePeriod($from, $to)
    {
        if(empty($to))
        {
           $to = js_getJSNowTimeStamp();
        }

        $this->fromDate = $from;
        $this->toDate = $to;

        $period = $to -$from;
        $days = $period / 86400;

        if($days < 32)
        {
           $this->step = 'day' ;
        }
        else
        {
           $this->step = 'month' ;
        }

        //Values might be used somewhere to indicate the requested month or year, so we set them
        $this->d = gmdate( 'j', $this->fromDate ) == gmdate( 'j', $this->toDate ) ? gmdate( 'j', $this->fromDate ) : 'all';
        $this->d = (date( 'j', $this->fromDate ) == date( 'j', $this->toDate )) ? date( 'j', $this->toDate ) : $this->d;

        $this->m = gmdate( 'n', $this->fromDate ) == gmdate( 'n', $this->toDate ) ? gmdate( 'n', $this->fromDate ) : 'all';
        $this->m = (date( 'n', $this->fromDate ) == date( 'n', $this->toDate )) ? date( 'n', $this->toDate ) : $this->m;

        $this->y = gmdate( 'Y', $this->fromDate ) == gmdate( 'Y', $this->toDate ) ? gmdate( 'Y', $this->fromDate ) : 'all';
        $this->y = (date( 'Y', $this->fromDate ) == date( 'Y', $this->toDate )) ? date( 'Y', $this->toDate ) : $this->y;
    }

	/* This function read values from request. If values are not set in request they are stay unchnged. This is for purpose.
	 * Default values are set in constructor or by calling setDefault*() function */
	function readTimePeriodFromRequest( $startdayormonth ) {

        $this->periodIndicator = JRequest::getVar( 'period', $this->periodIndicator );

        if(!empty($this->periodIndicator) && strcmp($this->periodIndicator, "by-date") != 0)
        {
            js_JSUtil::resolvePeriodIndicator($this->periodIndicator, $this->fromDate, $this->toDate, $this->d, $this->m, $this->y, $this->step);
            $this->setTimePeriod($this->fromDate, $this->toDate);
        }
        else
        {
            $d = JRequest::getVar( 'd', '' );
            $m = JRequest::getVar( 'm', '' );
            $y = JRequest::getVar( 'y', '' );

            $this->step = 'month' ;

            if ($y != '')
            {
                $this->y = $y;
            }

            if ($d != '') {
                $this->d = $d;
                $this->step = 'day' ;
            } else {
                if( $startdayormonth == 'm' )
                {
                    $this->d = 'all';
                }
            }

            if ($m != '')
            {
                $this->m = $m;
                $this->step = 'day' ;
            }
            $this->realignPeriod();
        }
	}

	/**
	 * Returns selected values. '%' char is returned when user selcect 'all' option
	 *
	 * @access public
	 * @return boolean  false when user select 'all' at least once. If true is returned user select one particular day
	 */
	function getDMY( &$day, &$month, &$year ) {
		$bResult = true;
		
		$day = $this->d;
		if( $this->d == 'all' ) {
			$day = '%';
			$bResult = false;
		}
		
		$month = $this->m;
		if( $this->m == 'all' ) {
			$month = '%';
			$bResult = false;
		}
		
		$year = $this->y;
		if( $this->y == 'all' ) {
			$year = '%';
			$bResult = false;
		}
		
		return $bResult;
	}

    /**
     * If you like to print it out, don't forget to shift it to the timezone 
     *
     * @param  $date_from GMT Timestamp
     * @param  $date_to GMT Timestamp
     * @return void
     */
    function getTimePeriodsDatesAsLong( &$date_from, &$date_to )
    {
//        echo (date('Y-m-j h:m:s',$this->fromDate).' '.date('Y-m-j h:m:s',$this->toDate).' xx<br/>');

        $date_from = $this->fromDate;// strtotime($date_from);
        $date_to = $this->toDate; //strtotime($date_to);

        $date_from = null;
        $date_to = null;
        $this->getTimePeriodsDates($date_from, $date_to);

        //$timezone = js_getJSTimeZone();
        //$tz_offset = $timezone * 3600;

        $date_from = strtotime($date_from);
        $date_to = strtotime($date_to);
    }

	function getTimePeriodsDates( &$date_from, &$date_to ) {

        if($this->fromDate > 0)
        {
            $day_from = date("j", $this->fromDate);
            $month_from = date("n", $this->fromDate);
            $year_from = date("Y", $this->fromDate);
        }
        else if(empty($this->toDate))
        {
            $day_from = $this->d;
            if( $this->d == 'all' ) {
                $day_from = '1';
            }
            $month_from = $this->m;
            if( $this->m == 'all' ) {
                $month_from = '1';
            }
            $year_from = $this->y;
            if( $this->y == 'all' ) {
                $year_from = '1979';
            }
        }
        if($this->toDate > 0)
        {
            $day_to = date("j", $this->toDate);
            $month_to = date("n", $this->toDate);
            $year_to = date("Y", $this->toDate);
        }
        else if(empty($this->fromDate))
        {
            $day_to = $this->d;
            if( $this->d == 'all' ) {
                $day_to = '31';
            }
            $month_to = $this->m;
            if( $this->m == 'all' ) {
                $month_to = '12';
            }
            $year_to = $this->y;
            if( $this->y == 'all' ) {
                $year_from = date('Y');
            }
        }
        else
        {
            $day_to = '31';
            $month_to = '12';
            $year_to = date('Y');
        }

		$date_from = $year_from.'-'.$month_from.'-'.$day_from.' 00:00:00';
		$date_to = $year_to.'-'.$month_to.'-'.$day_to.' 23:59:59';
	}

    /**
     * @param  $date_from date as UNIX timestamp
     * @param  $date_to date as UNIX timestamp
     * @return THe unix timestamp to define a period of time for values stored in the DB as UNIX timestamp
     */
	function getTimePeriodsDatesAsTimestamp( &$date_from, &$date_to )
    {
        if($this->fromDate > 0)
        {
            $date_from = $this->fromDate;
        }
        else if(empty($this->toDate))
        {
            $day_from = $this->d;
            if( $this->d == 'all' ) {
                $day_from = 1;
            }
            $month_from = $this->m;
            if( $this->m == 'all' ) {
                $month_from = 1;
            }
            $year_from = $this->y;
            if( $this->y == 'all' ) {
                $year_to = 1970;
                return;
            }
            $date_from = mktime (0, 0, 0, $month_to, $day_to, $year_to );
        }
        if($this->toDate > 0)
        {
            $date_to = $this->toDate;
        }
        else if(empty($this->fromDate))
        {
            $day_to = $this->d;
            if( $this->d == 'all' ) {
                $day_to = 31;
            }
            $month_to = $this->m;
            if( $this->m == 'all' ) {
                $month_to = 12;
            }
            $year_to = $this->y;
            if( $this->y == 'all' ) {
                $year_to = date('Y');
            }
            $date_to = mktime (23, 59, 59, $month_to, $day_to, $year_to );
        }
        else
        {
            $day_to = 31;
            $month_to = 12;
            $year_to = date('Y');
            $date_to = mktime (23, 59, 59, $month_to, $day_to,$year_to );
        }
	}

	/**
	 * Create the Day dropdown
	 *
	 * @access private
	 * @return string
	 */
	function CreateDayCmb() {

		$html = '';
		$html .= '<option value="all"';
		if( $this->d == 'all' )
			 $html .= ' selected="selected"';
		$html .= '>' . JTEXT::_( 'All' ) . '</option>' . "\n";

		for( $i = 1; $i <= 31; $i++ ) {
			$html .= '<option value="' . $i . '"';
			if( $this->d == $i ) {
				$html .= ' selected="selected"';
			}
			$html .= '>' . $i . '</option>' . "\n";
		}

		return $html;
	}

	/**
	 * Creates the dropdown for months
	 *
	 * @access private
	 * @return string
	 */
	function CreateMonthCmb() {
		require_once( dirname(__FILE__) .DS. 'template.html.php' );

		$html = '';
		
		$JSUtil = new js_JSUtil();
		$JSTemplate = new js_JSTemplate();

		$html .= '<option value="all"';
		if( $this->m == 'all' )
			 $html .= ' selected="selected"';
		$html .= '>' . JTEXT::_( 'All' ) . '</option>' . "\n";
		
		for( $i=1; $i<13; $i++ ) {
			$html .= '<option value="' . $i . '"';
			if( $this->m == $i ) {
				$html .= ' selected="selected"';
			}
			$html .= '>' . $JSTemplate->monthToString($i, true) . '</option>' . "\n";
		}

		return $html;
	}

	/**
	 * Creates the year drop down
	 *
	 * @access private
	 * @return string
	 */
	function CreateYearCmb() {

		$html		= '';
		$date_min	= 2003;
        $after2weeks = mktime(0, 0, 0, date('m'), date('d')+14, date('Y')); //new year appears in the last 2 weeks of current year
        $date_max = date( 'Y', $after2weeks ); //we do not use js_date() for performance (it does not matter in this case)

		$html .= '<option value="all"';
		if( $this->y == 'all' )
			 $html .= ' selected="selected"';
		$html .= '>' . JTEXT::_( 'All' ) . '</option>' . "\n";

		for( $i = $date_min; $i <= $date_max; $i++ ) {
			$html .= '<option value="' . $i . '"';
			if( $this->y == $i ) {
				$html .= ' selected="selected"';
			}
			$html .= '>' . $i . '</option>' . "\n";
		}

		return $html;
	}

	/**
	 * Function to set $this->d; $this->m; $this->y values to now (to JSNowTimeStamp)
	 */
	function setDMY2Now() {
		$JSNowTimeStamp = js_getJSNowTimeStamp();
		$this->setDMYFromJSTimeStamp($JSNowTimeStamp);
	}

	/**
	 * Function to set $this->d; $this->m; $this->y values to $JSNowTimeStamp
	 *
	 * $JSNowTimeStamp is timestamp with appropriate offset. See function js_getJSNowTimeStamp() for details.
	 */
	function setDMYFromJSTimeStamp($JSNowTimeStamp)
    {
		$this->d = js_gmdate( 'j', $JSNowTimeStamp );
		$this->m = js_gmdate( 'n', $JSNowTimeStamp );
		$this->y = js_gmdate( 'Y', $JSNowTimeStamp );
        $this->realignPeriod();
	}

    function realignPeriod()
    {
        $this->getTimePeriodsDatesAsLong($this->fromDate, $this->toDate);
    }

    function render()
    {
         if(!$this->hide)
         {
             $this->renderPeriodFilter();
             $this->getHtmlDateFilterCode();
         }
    }

	/**
	 * creates a javascript and dropdowns for date selection
	 *
	 * @todo mic: javascript should be outside into the header and NOT direct in the code
	 * @AT: of course not - on page could be 2 date filters
	 *
	 * @return string
	 */
	function getHtmlDateFilterCode() {
        
		?>
			<script type="text/javascript">
				/* <![CDATA[ */
				function SelectDay(Value) {
					for (index=0; index<document.adminForm.d.length; index++) {
						/* walk the list */
						if (document.adminForm.d[index].value == Value) {
							/* if the day is the day we want to select */
							document.adminForm.d.selectedIndex = index;
							/* then mark it selected */
						}
					}
				};

				function SelectMonth(Value) {
					for (index=0; index<document.adminForm.m.length; index++) {
						/* walk the list */
						if (document.adminForm.m[index].value == Value) {
							/* if the day is the day we want to select */
							document.adminForm.m.selectedIndex = index;
							/* then mark it selected */
						}
					}
				};
				function onDChange() {

					if (document.adminForm.d.value == "all") {
					} else {
						if (document.adminForm.m.value == "all")
							document.adminForm.m[1].selected = true;
						if (document.adminForm.y.value == "all")
							document.adminForm.y[1].selected = true;
					}
				    document.adminForm.period.value = "by-date"
				};
				
				function onMChange() {
					if (document.adminForm.m.value == "all") {
						document.adminForm.d.value = "all";
					} else {
						if (document.adminForm.y.value == "all")
							document.adminForm.y[1].selected = true;
					}
				    document.adminForm.period.value = "by-date"
				};
				
				function onYChange() {
					if (document.adminForm.y.value == "all") {
						document.adminForm.d.value = "all";
						document.adminForm.m.value = "all";
					} else {
					}
				    document.adminForm.period.value = "by-date"
				};
				
				/* ]]> */
			</script>
        <select name="d" onChange="onDChange();"><?php echo $this->CreateDayCmb();?></select>
        &nbsp;&nbsp;
		<select name="m" onChange="onMChange();"><?php echo $this->CreateMonthCmb();?></select>
		&nbsp;&nbsp;
		<select name="y" onChange="onYChange();"><?php echo $this->CreateYearCmb();?></select>
		&nbsp;&nbsp;
		<input type="submit" name="Submit" id="Submit" onclick="document.adminForm.method='GET'" value="<?php echo JTEXT::_('Go');?>" />

        <?php
	}

    function renderPeriodFilter()
    {
        ?>
            <script type="text/javascript">
                /* <![CDATA[ */
                function onPeriodChange(){
                    if (document.adminForm.period.value != "by-date") {
                        document.adminForm.y.value = "all";
                        document.adminForm.d.value = "all";
                        document.adminForm.m.value = "all";
                    } else {
                    }
                };
                /* ]]> */
            </script>
            <select name="period" onChange="onPeriodChange();">
                <option value="by-date" <?php echo ($this->periodIndicator == 'by-date' ? 'selected="true"' : '' );?>><?php echo JText::_("By Date");?></option>
                <option value="today" <?php echo ($this->periodIndicator == 'today' ? 'selected="true"' : '' );?>><?php echo JText::_("Today");?></option>
                <option value="7-days" <?php echo ($this->periodIndicator == '7-days' ? 'selected="true"' : '' );?>><?php echo JText::_("Last 7 days");?></option>
                <option value="14-days" <?php echo ($this->periodIndicator == '14-days' ? 'selected="true"' : '' );?>><?php echo JText::_("Last 14 days");?></option>
                <option value="28-days" <?php echo ($this->periodIndicator == '28-days' ? 'selected="true"' : '' );?>><?php echo JText::_("Last 28 days");?></option>
                <option value="365-days" <?php echo ($this->periodIndicator == '365-days' ? 'selected="true"' : '' );?>><?php echo JText::_("Last 365 days");?></option>
                <option value="this-week" <?php echo ($this->periodIndicator == 'this-week' ? 'selected="true"' : '' );?>><?php echo JText::_("This Week");?></option>
                <option value="last-week" <?php echo (strcmp($this->periodIndicator,'last-week') == 0 ? 'selected="true"' : '' );?>><?php echo JText::_("Last Week");?></option>
                <option value="this-month" <?php echo ($this->periodIndicator == 'this-month' ? 'selected="true"' : '' );?>><?php echo JText::_("This Month");?></option>
                <option value="this-year" <?php echo ($this->periodIndicator == 'this-year' ? 'selected="true"' : '' );?>><?php echo JText::_("This Year");?></option>
                <option value="last-2-years" <?php echo ($this->periodIndicator == 'last-2-years' ? 'selected="true"' : '' );?>><?php echo JText::_("Last 2 Years");?></option>
            </select>
        <?php
    }
}




/**
 *	This class makes working with domain filter easy.
 *	It can generate HTML code, create SQL query, read values from request.
 */
class js_JSFilterDomain
{
	/** This membes hold user entered (user selected) string
	 *	This string is used when database is queried
	 *	@access private */
	var $_domain_string = '';
	
	/**
	 * This membes hold hint that is displayed on search mouse over action
	 * eg. 'Domain (google.com/.eu/.com)'
	 * @access private
	 */
	var $_domain_hint = '';
	

	/**
	 * This membes decide when domain filter should be shown. Set it to 'true' if You want have domain filter visible
	 *	@access public
	 */
	var $show_domain_filter = false;

    function __construct( )
    {
        $this->readDomainStringFromRequest();
    }

	/** set default values for this class */
	function setDefaultDomain() {
		$def = new js_JSFilterDomain();
		$this->_domain_string     = $def->_domain_string;
		$this->_domain_hint       = $def->_domain_hint;
		$this->show_domain_filter = $def->show_domain_filter;
	}

	/** gets var from request string */
	function readDomainStringFromRequest() {
		$this->_domain_string = JRequest::getVar( 'dom' );
	}

	/** return 'Domain String' */
	function getDomainString() {
		return $this->_domain_string;
	}
	
	/**
	 * Set hint that is displayed on search mouse over action
	 * eg. 'Domain (google.com/.eu/.com)'
	 * @param string
	 */
	function setDomainHint( $domain_hint ) {
		$this->_domain_hint = $domain_hint;
	}
	
	/**
	 * builds a hidden field holding the search item
	 *
	 * @return string
	 */
	function getHtmlDomainFilterHiddenCode() {
		if ($this->show_domain_filter == false)
			return '<input type="hidden" name="dom" id="dom" value="' . $this->_domain_string . '" />';
		else
			return '';
	}
	
	/**
	 * builds an input field for search
	 *
	 * @return string
	 */
	function getHtmlDomainFilterVisibleCode() {
		if ($this->show_domain_filter == true) {
			$hint = ( $this->_domain_hint == '' ) ? '' : ( ' title="' . $this->_domain_hint . '"' );
	
			$html  = JTEXT::_( 'Domain' )
			. ':&nbsp;'
			. '<input type="text" name="dom" id="dom" value="' . $this->_domain_string . '"'
			. ' class="text_area" onChange="document.adminForm.limitstart.value=0;document.adminForm.submit();"' . $hint . ' />';
			
			return $html;
		} else {
			return '';
		}
	}
}




/**
 *	This class makes working with search filter easy.
 *	It can generate HTML code, create SQL query, read values from request.
 */
class js_JSFilterSearch
{
	/**
	 * This membes hold user entered sting to search input
	 * This string is used when database is queried
	 * @access private
	 */
	var $_search_string = '';

	/**
	 * This membes hold hint that is displayed on search mouse over action
	 * eg. 'Search (IP/TLD/NS-Lookup/OS)'
	 * @access private
	 */
	var $_search_hint = '';

	/**
	 * This membes decide when search filter should be shown. Set it to 'true' if You want have search filter visible
	 *	@access public
	 */
	var $show_search_filter = true;

	function setDefaultDomain() {
		$def = new js_JSFilterSearch();
		$this->_search_string     = $def->_search_string;
		$this->_search_hint       = $def->_search_hint;
		$this->show_search_filter = $def->show_search_filter;
	}

	function readSearchStringFromRequest() {
        $mainframe = JFactory::getApplication();

		global $option;

		$this->_search_string = $mainframe->getUserStateFromRequest("search{$option}", 'search', '');
	}

	/** return 'Search String' */
	function getSearchString() {
		return $this->_search_string;
	}

	/**
	 * Set hint that is displayed on search mouse over action
	 * eg. 'Search (IP/TLD/NS-Lookup/OS)'
	 * @param string
	 */
	function setSearchHint( $search_hint ) {
		$this->_search_hint = $search_hint;
	}

	/**
	 * builds an input field for search
	 *
	 * @return string
	 */
	function getHtmlSearchFilterVisibleCode() {

		if ($this->show_search_filter == true) {
			$hint = ( $this->_search_hint == '' ) ? '' : ( ' title="' . $this->_search_hint . '"' );
	
			$html  = JTEXT::_( 'Search' )
			. ':&nbsp;'
			. '<input type="text" name="search" id="search" value="' . $this->_search_string . '"'
			. ' class="text_area" onChange="document.adminForm.limitstart.value=0;document.adminForm.submit();"' . $hint . ' />';
			
			return $html;
		} else {
			return '';
		}
	}

	/**
	 * builds a hidden field holding the search item
	 *
	 * @return string
	 */
	function getHtmlSearchFilterHiddenCode() {
		if ($this->show_search_filter == false)
//            return '<input type="hidden" name="search" id="search" value="' . $this->_search_string . '" />';
            return '<input type="hidden" name="search" id="search" value="" />';
		else
			return '';
	}
}
	