<?php

namespace Obsidian\Routing;

use Exception;
use Obsidian\Middleware\HasMiddlewareTrait;
use Obsidian\Request;
use Obsidian\Routing\Conditions\ConditionInterface;
use Obsidian\Routing\Conditions\Factory;
use Obsidian\Routing\Conditions\InvalidRouteConditionException;
use Obsidian\Routing\Conditions\Url as UrlCondition;

/**
 * Represent a route
 */
class Route implements RouteInterface {
	use HasMiddlewareTrait;

	/**
	 * Allowed methods
	 *
	 * @var string[]
	 */
	protected $methods = [];

	/**
	 * Route target
	 *
	 * @var ConditionInterface
	 */
	protected $target = null;

	/**
	 * Route handler
	 *
	 * @var Handler|null
	 */
	protected $handler = null;

	/**
	 * Route name
	 *
	 * @var string
	 */
	protected $name = '';

	/**
	 * Constructor
	 *
	 * @param string[]        $methods
	 * @param mixed           $target
	 * @param string|\Closure $handler
	 */
	public function __construct( $methods, $target, $handler ) {
		if ( ! is_a( $target, ConditionInterface::class ) ) {
			try {
				$target = Factory::make( $target );
			} catch ( InvalidRouteConditionException $e ) {
				throw new Exception( 'Route target is not a valid route string or condition.' );
			}
		}

		$this->methods = $methods;
		$this->target = $target;
		$this->handler = new Handler( $handler );
	}

	/**
	 * {@inheritDoc}
	 */
	public function satisfied( Request $request ) {
		if ( ! in_array( $request->getMethod(), $this->methods) ) {
			return false;
		}
		return $this->target->satisfied( $request );
	}

	/**
	 * {@inheritDoc}
	 */
	public function handle( Request $request, $template ) {
		$arguments = array_merge( [$request, $template], $this->target->getArguments( $request ) );
		return $this->executeMiddleware( $this->getMiddleware(), $request, function() use ( $arguments ) {
			return call_user_func_array( [$this->handler, 'execute'], $arguments );
		} );
	}

	/**
	 * {@inheritDoc}
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setName( $name ) {
		$this->name = $name;
		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function name( $name ) {
		return $this->setName( $name );
	}

	/**
	 * {@inheritDoc}
	 */
	public function getUrl( $parameters = [] ) {
		if ( ! is_a( $this->target, UrlCondition::class ) ) {
			throw new Exception( 'Only routes with Url conditions can be resolved to a url.' );
		}

		return $this->target->getUrlWithParameters( $parameters );
	}

	/**
	 * Add a rewrite rule to WordPress for url-based routes
	 *
	 * @param  string $rewrite_to
	 * @return RouteInterface
	 */
	public function rewrite( $rewrite_to ) {
		if ( ! is_a( $this->target, UrlCondition::class ) ) {
			throw new Exception( 'Only routes with url targets can add rewrite rules.' );
		}

		$regex = $this->target->getMatchingRegex( $this->target->getUrl(), false );
		$regex = preg_replace( '~^\^/~', '^', $regex ); // rewrite rules require NO leading slash

		add_filter( 'obsidian_routing_rewrite_rules', function( $rules ) use ( $regex, $rewrite_to ) {
			$rules[ $regex ] = $rewrite_to;
			return $rules;
		} );

		return $this;
	}
}
