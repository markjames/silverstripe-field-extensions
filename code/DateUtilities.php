<?php

/**
 *
 * @todo Internationalize formats and strings
 */
class DateUtilities {

	/**
	 * Format an interval value with the requested granularity.
	 * i.e. 3 hours 4 minutes
	 *
	 * @param integer $seconds The length of the interval in seconds.
	 * @param integer $granularity How many different units to display in the string.
	 * @return string A string representation of the interval.
	 */
	public static function Interval($seconds, $granularity=2) {

		$units = array(
			'1 year|:count years' => 31536000,
			'1 week|:count weeks' => 604800,
			'1 day|:count days' => 86400,
			'1 hour|:count hours' => 3600,
			'1 minute|:count minutes' => 60,
			'1 second|:count seconds' => 1);

		$seconds = abs($seconds);

		$output = '';
		foreach( $units as $key=>$value ) {
			$key = explode('|', $key);
			if( $seconds >= $value ) {
				$count = floor($seconds / $value);
				$output .= $output ? ' ' : '';
				$output .= ($count == 1) ? $key[0] : str_replace(':count', $count, $key[1]);
				$seconds %= $value;
				$granularity--;
			}
			if( 0 == $granularity ) {
				break;
			}
		}

		return $output ? $output : '0 seconds';
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
	 * @param mixed $date The date as a timestamp, string or Date object
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
	public static function Friendly($date, $maxFriendlyTimeInSeconds=604800, $unfriendlyDateFormat='j F Y') {

		// If we have been passed date objects or strings, convert to timestamps
		if( is_a($date, 'Date') ) {
			$timestamp = intval($date->format('U'));
		} elseif( !is_numeric( $date ) ) {
			$timestamp = strtotime($date);
		}

		$seconds = time() - $timestamp;

		if( $seconds > $maxFriendlyTimeInSeconds ) {
			return date( $unfriendlyDateFormat, $timestamp );
		} elseif( $seconds < 0 ) {
			return date( $unfriendlyDateFormat, $timestamp );
		} elseif( $seconds < 1 ) {
			return 'just now';
		} else {
			return self::Interval( time() - $timestamp, 1 ) . ' ago';
		}

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
	 * @param mixed $start_date The start date as a timestamp, string or Date object
	 * @param mixed $end_date The start date as a timestamp, string or Date object
	 * @param string $month_format The php date() format to use for the month (either F or M)
	 * @return string formatted end date
	 */
	public static function NiceRange($start_date, $end_date, $month_format = 'F') {

		// If we have been passed date objects or strings, convert to timestamps
		if( is_a($start_date, 'Date') ) {
			$start_date = intval($start_date->format('U'));
		} elseif( !is_numeric( $start_date ) ) {
			$start_date = strtotime($start_date);
		}
		if( is_a($end_date, 'Date') ) {
			$end_date = intval($end_date->format('U'));
		} elseif( !is_numeric( $end_date ) ) {
			$end_date = strtotime($end_date);
		}
		
		// Is start_date after end_date?
		if( $start_date > $end_date ) {
			$prev_start_date = $start_date;
			$start_date = $end_date;
			$end_date = $prev_start_date;
			unset($prev_start_date);
		}

		$is_same_day = date('Y-m-d',$start_date) == date('Y-m-d',$end_date);
		$is_same_month = $is_same_day || date('Y-m',$start_date) == date('Y-m',$end_date);
		$is_same_year = $is_same_day || $is_same_month || (date('Y',$start_date) == date('Y',$end_date));
		$is_current_year = (date('Y',$start_date) == date('Y')) && (date('Y',$end_date) == date('Y'));

		if( $is_same_day && $is_current_year ) {
			// Single day event in the current year
			return date("jS $month_format",$start_date);
		} elseif( $is_same_month && $is_current_year ) {
			// Current year, but a span of dates in a single month
			if( $end_date < time() ) {
				return date("jS",$start_date) . '–' . date("jS $month_format Y",$end_date);
			} else {
				return date("jS",$start_date) . '–' . date("jS $month_format",$end_date);
			}
		} elseif( $is_current_year ) {
			// Current year, but a span of dates
			if( $end_date < time() ) {
				return date("jS $month_format",$start_date) . '–' . date("jS $month_format Y",$end_date);
			} else {
				return date("jS $month_format",$start_date) . '–' . date("jS $month_format",$end_date);
			}
		} elseif( $is_same_day ) {
			// Single day event, not the current year
			return date("jS $month_format Y",$start_date);
		} elseif( $is_same_month ) {
			// Span of dates in a single month (not in current year)
			return date('jS',$start_date) . '–' . date("jS $month_format",$end_date);
		} elseif( $is_same_year ) {
			// Span of dates where it starts in the same year that it ends
			return date("jS $month_format",$start_date) . '–' . date("jS $month_format Y",$end_date);
		} else {
			// Span of dates where it starts in a different year than it ends
			return date("jS $month_format Y",$start_date) . '–' . date("jS $month_format Y",$end_date);
		}

	}

}