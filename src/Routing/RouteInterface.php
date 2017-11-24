<?php

namespace Obsidian\Routing;

use Obsidian\Request;
use Obsidian\Middleware\HasMiddlewareInterface;

/**
 * Interface that routes must implement
 */
interface RouteInterface extends HasMiddlewareInterface {
	/**
	 * Return whether the route is satisfied
	 *
	 * @param  Request $request
	 * @return boolean
	 */
	public function satisfied( Request $request );

	/**
	 * Return a response for the given request
	 *
	 * @param  Request                             $request
	 * @param  string                              $template
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	public function handle( Request $request, $template );

	/**
	 * Get the route name
	 *
	 * @return string
	 */
	public function getName();

	/**
	 * Set the route name
	 *
	 * @param  string $name
	 * @return self   $this
	 */
	public function setName( $name );

	/**
	 * Alias for setName
	 *
	 * @see    Obsidian\Routing\RouteInterface::setName
	 * @param  string $name
	 * @return self   $this
	 */
	public function name( $name );

	/**
	 * Get route url with parameter values
	 *
	 * @param  array  $parameters
	 * @return string
	 */
	public function getUrl( $parameters = [] );
}
