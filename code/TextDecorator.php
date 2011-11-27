<?php

/**
 * A Decorator which adds additional utility methods to String-based data
 * fields.
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
class TextDecorator extends Extension {

	/**
	 * Returns the value escaped for the current template, with a non-breaking
	 * space added in place of the final whitespace character to prevent
	 * the final word from sitting on a separate line due to word wrapping
	 *
	 * @see TextUtilities::Widont()
	 * @return string Text for template with widont applied
	 */
	public function Widont() {

		return TextUtilities::Widont( $this->owner->forTemplate() );

	}

	/**
	 * Returns the value escaped for the current template, capitalized for use
	 * as a post title, per the rules by John Gruber from:
	 * http://daringfireball.net/2008/05/title_case
	 *
	 * @see TextUtilities::TitleCase()
	 * @param string $text The title to capitalize.
	 * @return string The properly capitalized title.
	 */
	public function TitleCase() {

		return TextUtilities::TitleCase( $this->owner->forTemplate() );

	}

	/**
	 * Returns the value escaped for the current template, with a basic
	 * conversion to a slugged value (i.e. all non-letter characters replace
	 * with hyphens)
	 *
	 * @todo Transliterate high-range characters
	 * @see TextUtilities::Slugged()
	 * @return string Text for template
	 */
	public function Slugged() {

		return TextUtilities::Slugged( $this->owner->forTemplate() );

	}
}