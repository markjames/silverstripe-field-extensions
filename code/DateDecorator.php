<?php

/**
 * A Decorator which adds additional utility methods to Dates and Datetime objects.
 *
 * @package		field-extensions
 * @author		Mark James <mail@mark.james.name>
 * @copyright	2011 - Mark James
 * @license		New BSD License
 * @link		http://github.com/markjames/silverstripe-fieldextensions
 * 
 * Copyright (c) 2011, Mark James
 * All rights reserved.
 * 
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 * 
 *     * Redistributions of source code must retain the above copyright notice,
 *       this list of conditions and the following disclaimer.
 * 
 *     * Redistributions in binary form must reproduce the above copyright notice,
 *       this list of conditions and the following disclaimer in the documentation
 *       and/or other materials provided with the distribution.
 * 
 *     * Neither the name of Zend Technologies USA, Inc. nor the names of its
 *       contributors may be used to endorse or promote products derived from this
 *       software without specific prior written permission.
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
 * ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
 * ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */
class DateDecorator extends Extension {

	/**
	 * Returns the numeric ISO 8601 day of the week value from 1 (Monday)
	 * through to 7 (Sunday)
	 *
	 * @return string Day of week (1-7)
	 */
	public function DayOfWeek() {

		return $this->owner->Format( 'N' );

	}

	/**
	 * Returns true if the decorated date represents a Saturday or Sunday
	 *
	 * @return boolean TRUE if on the weekend, FALSE if not 
	 */
	public function IsWeekend() {

		return 6 <= $this->owner->Format( 'N' );

	}

	/**
	 * Returns the string representation of the date in ISO8601 format
	 *
	 * @return string Date as a full ISO8601 string
	 */
	public function Iso8601() {

		return $this->owner->Format( DateTime::ISO8601 );

	}

	/**
	 * Get the decorated date as a friendly string, such as '4 days ago', but
	 * with a maximum duration until the time is instead returned as a
	 * non-friendly string (e.g. 28 January 2020)
	 *
	 * If the date is in the future, the date will be returned in the non-friendly
	 * format as specified by the $unfriendlyDateFormat parameter (which defaults)
	 * to the format "j F Y".
	 *
	 * If the date is right now, result will be 'just now' instead of '0 seconds'
	 *
	 * @see DateUtilities::Friendly()
	 * @param integer $maxFriendlyTimeInSeconds How long to go back showing
	 *                                          friendly about the date format
	 *                                          before just giving the actual
	 *                                          date in the non-friendly format
	 * @param integer $unfriendlyDateFormat The php date format used when the
	 *                                      date is in the future or older than
	 *                                      the maximum time specified in the
	 *                                      $maxFriendlyTimeInSeconds parameter
	 * @return string
	 */
	public function Friendly($maxFriendlyTimeInSeconds=null, $unfriendlyDateFormat='j F Y') {

		// Havn't specified the date? Only go back 7 days before reverting
		// to the standard date format
		if (null === $maxFriendlyTimeInSeconds) {
			$maxFriendlyTimeInSeconds = 604800;
		};

		return DateUtilities::Friendly($this->owner, $maxFriendlyTimeInSeconds, $unfriendlyDateFormat);

	}

	/**
	 * Get the decorated date as a string showing the difference between that
	 * that date and the current time formatted with the requested granularity
	 * (i.e. 3 hours 4 minutes)
	 *
	 * @see DateUtilities::Interval()
	 * @param integer $granularity How many different units to display in the string.
	 * @return string A string representation of the interval.
	 */
	public function Interval($granularity=2) {

		return DateUtilities::Interval( time() - $this->owner->Format('U') );

	}

	/**
	 * Returns the shortest possible date range between the decorated date and a
	 * second date object (or date string/timestamp).
	 * 
	 * This method will build up the minimum required string to fully convey the
	 * dates, eg. it will not duplicate the month name if both dates start and end
	 * in the same month. Does not include time parts when decorating {@link
	 * SS_Datetime} objects.
	 *
	 * You will probably want to use this in your controller/page object:
	 *
	 * <code>
	 * 	$this->obj('StartDate')->NiceRangeString( $this->obj('EndDate') );
	 * </code>
	 *
	 * @see DateUtilities::NiceRange()
	 * @param string $month_format The php date() format to use for the month (either F or M)
	 * @return string formatted end date
	 */
	public function NiceRangeString($otherDate, $month_format = 'F') {

		return DateUtilities::NiceRange($this->owner, $otherDate, $month_format);

	}

}