<?php

namespace Obsidian\Routing\Conditions;

use Exception;
use Obsidian\Url as UrlUtility;
use Obsidian\Request;

/**
 * Check against the current url
 */
class Url implements ConditionInterface {
	const WILDCARD = '*';

	/**
	 * URL to check against
	 *
	 * @var string
	 */
	protected $url = '';

	/**
	 * Regex to detect parameters in urls
	 *
	 * @var string
	 */
	protected $url_regex = '~
		(?<=/)                    # lookbehind for a slash
		(?:\{)                    # require opening curly brace
			(?P<name>[a-z]\w*)    # require a string starting with a-z and followed by any number of word characters for the parameter name
			(?P<optional>\?)?     # optionally allow the user to mark the parameter as option using literal ?
			(?::(?P<regex>.*?))?  # optionally allow the user to supply a regex to match the argument against
		(?:\})                    # require closing curly brace
		(?:/)                     # require trailing slash
	~ix';

	/**
	 * Regex to detect valid parameters in url segments
	 *
	 * @var string
	 */
	protected $parameter_regex = '[^/]+';

	/**
	 * Constructor
	 *
	 * @param string $url
	 */
	public function __construct( $url ) {
		if ( $url !== static::WILDCARD ) {
			$url = UrlUtility::addLeadingSlash( $url );
			$url = UrlUtility::addTrailingSlash( $url );
		}
		$this->url = $url;
	}

	/**
	 * {@inheritDoc}
	 */
	public function satisfied( Request $request ) {
		if ( $this->getUrl() === static::WILDCARD ) {
			return true;
		}

		$matching_regex = $this->getMatchingRegex( $this->getUrl() );
		$url = UrlUtility::getCurrentPath( $request );
		return (bool) preg_match( $matching_regex, $url );
	}

	/**
	 * {@inheritDoc}
	 */
	public function getArguments( Request $request ) {
		$matching_regex = $this->getMatchingRegex( $this->getUrl() );
		$url = UrlUtility::getCurrentPath( $request );
		$matches = [];
		$success = preg_match( $matching_regex, $url, $matches );

		if ( ! $success ) {
			return []; // this should not normally happen
		}

		$arguments = [];
		$parameter_names = $this->getParameterNames( $this->getUrl() );
		foreach ( $parameter_names as $parameter_name ) {
			$arguments[] = ! empty( $matches[ $parameter_name ] ) ? $matches[ $parameter_name ] : '';
		}

		return $arguments;
	}

	/**
	 * Return the url for this condition
	 *
	 * @return string
	 */
	public function getUrl() {
		return $this->url;
	}

	/**
	 * Return the url for this condition with parameter values
	 *
	 * @param  array  $parameters
	 * @return string
	 */
	public function getUrlWithParameters( $parameters = [] ) {
		$result = preg_replace_callback( $this->url_regex, function( $matches ) use ( $parameters ) {
			$name = $matches['name'];
			$optional = ! empty( $matches['optional'] );

			if ( isset( $parameters[ $name ] ) ) {
				return urlencode( $parameters[ $name ] ) . '/';
			}

			if ( ! $optional ) {
				throw new Exception( 'No value supplied for the required parameter "' . $name . '"' );
			}

			return '';
		}, $this->getUrl() );
		return $result;
	}

	/**
	 * Concatenate 2 url conditions into a new one
	 *
	 * @param  Url $url
	 * @return Url
	 */
	public function concatenate( Url $url ) {
		return new static( UrlUtility::removeTrailingSlash( $this->getUrl() ) . $url->getUrl() );
	}

	/**
	 * Return parameter names as defined in the url
	 *
	 * @param  string   $url
	 * @return string[]
	 */
	protected function getParameterNames( $url ) {
		$matches = [];
		preg_match_all( $this->url_regex, $url, $matches );
		return $matches['name'];
	}

	/**
	 * Return regex to test whether normal urls match the parameter-based one
	 *
	 * @param  string  $url
	 * @param  boolean $wrap
	 * @return string
	 */
	public function getMatchingRegex( $url, $wrap = true ) {
		$parameters = [];

		// Replace all parameters with placeholders
		$matching_regex = preg_replace_callback( $this->url_regex, function( $matches ) use ( &$parameters ) {
			$name = $matches['name'];
			$optional = ! empty( $matches['optional'] );
			$regex = ! empty( $matches['regex'] ) ? $matches['regex'] : $this->parameter_regex;
			$replacement = '(?P<' . $name . '>' . $regex . ')/';
			if ( $optional ) {
				$replacement .= '?';
			}

			$placeholder = '___placeholder_' . sha1( count( $parameters) . '_' . $replacement . '_' . uniqid() ) . '___';
			$parameters[ $placeholder ] = $replacement;
			return $placeholder;
		}, $url );

		// quote the remaining string so that it does not get evaluated as regex
		$matching_regex = preg_quote( $matching_regex, '~' );

		// replace the placeholders with the real parameter regexes
		$matching_regex = str_replace( array_keys( $parameters ), array_values( $parameters ), $matching_regex );

		// add a question mark to the end to make the trailing slash optional
		$matching_regex = $matching_regex . '?';

		// make sure the regex matches the entire url
		$matching_regex = '^' . $matching_regex . '$';

		if ( $wrap ) {
			$matching_regex = '~' . $matching_regex . '~';
		}

		return $matching_regex;
	}
}
