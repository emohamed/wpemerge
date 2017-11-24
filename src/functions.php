<?php

use Obsidian\Response;

if ( ! function_exists( 'obs_response' ) ) {
	/**
	 * @codeCoverageIgnore
	 * @see    \Obsidian\Response::response()
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	function obs_response() {
		return Response::response();
	}
}

if ( ! function_exists( 'obs_output' ) ) {
	/**
	 * @codeCoverageIgnore
	 * @see    \Obsidian\Response::output()
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	function obs_output( $output ) {
		return Response::output( obs_response(), $output );
	}
}

if ( ! function_exists( 'obs_template' ) ) {
	/**
	 * @codeCoverageIgnore
	 * @see    \Obsidian\Response::template()
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	function obs_template( $templates, $context = array() ) {
		return Response::template( obs_response(), $templates, $context );
	}
}

if ( ! function_exists( 'obs_json' ) ) {
	/**
	 * @codeCoverageIgnore
	 * @see    \Obsidian\Response::json()
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	function obs_json( $data ) {
		return Response::json( obs_response(), $data );
	}
}

if ( ! function_exists( 'obs_redirect' ) ) {
	/**
	 * @codeCoverageIgnore
	 * @see    \Obsidian\Response::redirect()
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	function obs_redirect( $url, $status = 302 ) {
		return Response::redirect( obs_response(), $url, $status );
	}
}

if ( ! function_exists( 'obs_error' ) ) {
	/**
	 * @codeCoverageIgnore
	 * @see    \Obsidian\Response::error()
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	function obs_error( $code ) {
		return Response::error( obs_response(), $code );
	}
}

if ( ! function_exists( 'obs_route' ) ) {
	/**
	 * @codeCoverageIgnore
	 * @see    \Obsidian\Router::getRouteUrl()
	 * @param  string $name
	 * @param  array  $parameters
	 * @return string
	 */
	function obs_route( $name, $parameters = [] ) {
		return Router::getRouteUrl( $name, $parameters );
	}
}
