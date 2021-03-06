<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: csvLine.php 1877 2011-02-09 14:02:38Z geraint $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2010 GWE Systems Ltd, 2006-2008 JEvents Project Group
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * Class for objects containing lines of converted CSV file to iCal format
 *
 * Part of the CSV to iCal conversion mechanism
 */
class CsvLine {

    var $uid;
    var $categories;
    var $summary;
    var $location;
    var $description;
    var $contact;
    var $extraInfo;
    var $dtstamp;
    var $dtstart;
    var $dtend;
    var $timezone;
    var $rrule;

    /**
     * default constructor with manatory parameters
     *
     * @param categories category of the event
     * @param summary title (name) of the event
     * @param dtstart start datetime of the event
     * @param dtend end datetime of the event
     */
    public function CsvLine($categories, $summary, $dtstart, $dtend) {
        $this->categories = $categories;
        $this->summary = $summary;
        $this->dtstart = $dtstart;
        $this->dtend = $dtend;
        $timezone = "UTC";  // default timezone
    }

    /**
     * Getters and setters
     */
    public function getCategories() {
        return $this->categories;
    }

    public function setCategories($categories) {
        $this->categories = $categories;
    }

    public function getSummary() {
        return $this->summary;
    }

    public function setSummary($summary) {
        $this->summary = $summary;
    }

    public function getLocation() {
        return $this->location;
    }

    public function setLocation($location) {
        $this->location = $location;
    }

    public function getDescription() {
        return trim($this->description);
    }

    public function setDescription($description) {
        $this->description = trim($description);
    }

    public function getContact() {
        return $this->contact;
    }

    public function setContact($contact) {
        $this->contact = trim($contact);
    }

    public function getRRule() {
        return $this->rrule;
    }

    public function setRrule($rrule) {
        $this->rrule = trim($rrule);
    }

	public function getExtraInfo() {
        return $this->extraInfo;
    }

    public function setExtraInfo($extraInfo) {
        $this->extraInfo = $extraInfo;
    }

    public function getDtstamp() {
        return $this->dtstamp;
    }

    public function setDtstamp($dtstamp) {
		if (trim($dtstamp)=="") return;
        $this->dtstamp = strtotime($dtstamp);
    }


    public function getDtend() {
        return $this->dtend;
    }

    public function setDtend($dtend) {
        $this->dtend = strtotime($dtend);
    }

    public function getUid() {
		if (isset($this->uid) && $this->uid!=""){
			return $this->uid;
		}
		else return $this->generateUid ();
    }

    public function setUID($uid) {
        $this->uid = $uid;
    }

	public function getTimezone() {
        return $this->timezone;
    }

    public function setTimezone($timezone) {
        $this->timezone = $timezone;
    }

    /**
     * function prepares event in iCal format
     *
     * @return this object in iCal format
     */
    public function getInICalFormat() {
        $prevTimezone = date_default_timezone_get();
        date_default_timezone_set($this->timezone);

        $ical = "BEGIN:VEVENT\n";
        $ical .= "UID:".$this->getUid()."\n"
               ."CATEGORIES:".$this->categories."\n"
               ."SUMMARY:".$this->summary."\n"
               ."DTSTART:".$this->datetimeToUtcIcsFormat($this->dtstart)."\n"
               ."DTEND:".$this->datetimeToUtcIcsFormat($this->dtend)."\n";
        if($this->dtstamp != "") $ical .= "DTSTAMP:".$this->datetimeToUtcIcsFormat($this->dtstamp)."\n";
        if($this->location != "") $ical .= "LOCATION:".$this->location."\n";
        if($this->description != "") $ical .= "DESCRIPTION:".$this->description."\n";
        if($this->contact != "") $ical .= "CONTACT:".$this->contact."\n";
        if($this->extraInfo != "") $ical .= "X-EXTRAINFO:".$this->extraInfo."\n";
        if($this->rrule != "") $ical .= "RRULE:".$this->rrule."\n";

        $ical .= "SEQUENCE:0\n";
        $ical .= "TRANSP:OPAQUE\n";
        $ical .= "END:VEVENT\n";

        date_default_timezone_set($prevTimezone); // set timezone back
        return $ical;
    }

    /**
     * Function generates unique UID of the event
     *
     * @return generated uid of the event
     */
    private function generateUid() {
        return md5(uniqid(rand(),true));
    }

    /**
     * Function converts datetime to iCal format
     *
     * @param datetime Datetime of the event
     * @return converted datetime in iCal format
     */
    private function datetimeToUtcIcsFormat($datetime) {
		$datetime = strtotime($datetime);
        return gmdate("Ymd", $datetime)."T".gmdate("His", $datetime)."Z";
    }
}