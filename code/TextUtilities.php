<?php

/**
 * Utility functions for dealing with text/strings
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
class TextUtilities {

	/**
	 * Returns the input with a non-breaking
	 * space added in place of the final whitespace character to prevent
	 * the final word from sitting on a separate line due to word wrapping.
	 *
	 * This widont implementation only replaces if there are at least 3 words.
	 *
	 * @return string Input with widont applied
	 */
	public static function Widont( $input ) {
		return preg_replace(
			'/([^\s]\s+[^\s]+)\s+([^\s]+)\s*$/',
			'$1&nbsp;$2',
			$input
		);
	}

	/**
	 * Capitalizes a post title, per the rules by John Gruber from:
	 * http://daringfireball.net/2008/05/title_case
	 *
	 * PHP Implementation by Adam Nolley:
	 * http://nanovivid.com/stuff/wordpress/title-case/
	 *
	 * @param string $text The title to capitalize.
	 * @return string The properly capitalized title.
	 */
	public static function TitleCase( $input ) {

		$str = $input;

		// Edit this list to change what words should be lowercase
		$small_words = "a an and as at but by en for if in of on or the to v[.]? via vs[.]?";
		$small_re = str_replace(" ", "|", $small_words);

		// Replace HTML entities for spaces and record their old positions
		$htmlspaces = "/&nbsp;|&#160;|&#32;/";
		$oldspaces = array();
		preg_match_all($htmlspaces, $str, $oldspaces, PREG_OFFSET_CAPTURE);

		// Remove HTML space entities
		$words = preg_replace($htmlspaces, " ", $str);

		// Split around sentance divider-ish stuff
		$words = preg_split(
			       '/( [:.;?!][ ] | (?:[ ]|^)["â€œ])/x',
			       $words,
			       -1,
			       PREG_SPLIT_DELIM_CAPTURE
			     );

		for ($i = 0; $i < count($words); $i++) {

			// Skip words with dots in them like del.icio.us
			$words[$i] = preg_replace_callback(
				           '/\b([[:alpha:]][[:lower:].\'â€™(&\#8217;)]*)\b/x',
				           get_class() . '::TitleCaseSkipDottedWordCallback',
				           $words[$i]
				         );

			// Lowercase our list of small words
			$words[$i] = preg_replace(
				           "/\b($small_re)\b/ei",
				           "strtolower(\"$1\")",
				           $words[$i]
				         );

			// If the first word in the title is a small word, capitalize it
			$words[$i] = preg_replace(
				           "/\A([[:punct:]]*)($small_re)\b/e",
				           "\"$1\" . ucfirst(\"$2\")",
				           $words[$i]
				         );

			// If the last word in the title is a small word, capitalize it
			$words[$i] = preg_replace(
				           "/\b($small_re)([[:punct:]]*)\Z/e",
				           "ucfirst(\"$1\") . \"$2\"",
				           $words[$i]
				         );
		}

		$words = join($words);

		// Oddities
		
		// Oddities: v, vs, v., and vs.
		$words = preg_replace("/ V(s?)\. /i", " v$1. ", $words);
		// Oddities: 's
		$words = preg_replace("/(['â€™]|&#8217;)S\b/i", "$1s", $words);
		// Oddities: AT&T and Q&A
		$words = preg_replace("/\b(AT&T|Q&A)\b/ie", "strtoupper(\"$1\")", $words);
		// Oddities: -ing
		$words = preg_replace("/-ing\b/i", "-ing", $words);
		// Oddities: html entities
		$words = preg_replace("/(&[[:alpha:]]+;)/Ue", "strtolower(\"$1\")", $words);

		// Put HTML space entities back
		$offset = 0;
		for ($i = 0; $i < count($oldspaces[0]); $i++) {
			$offset = $oldspaces[0][$i][1];
			$words = substr($words, 0, $offset)
			       . $oldspaces[0][$i][0]
			       . substr($words, $offset + 1);
			$offset += strlen($oldspaces[0][$i][0]);
		}

		return $words;
	}

	/**
	 * Callback method for {@link TextUtilities::TitleCase()} for skipping words
	 * containing dots.
	 *
	 * @param array $matches
	 * @return string
	 */
	public static function TitleCaseSkipDottedWordCallback($matches) {
			return preg_match('/[[:alpha:]] [.] [[:alpha:]]/x', $matches[0])
			       ? $matches[0] : ucfirst($matches[0]
			);
	}

	/**
	 * Strips characters from a string
	 * 
	 * @todo Transliterate high-range characters
	 * @param String $input Input
	 */
	public static function Slugged( $input ) {

		$format = '/\W+/';
		$result = strtolower(preg_replace( $format, '-', $input ));

		$result = trim($result,'-');
		return $result;

	}
	
}