<?php 

	/**
	 * Afro - The simplistic PHP port of Sinatra for Ruby.
	 * 
	 * @author  James <ukjbrooks@gmail.com>
	 * @version 1.2
	 */

	define('AJAX', 'XMLHttpRequest');

	function get($route, $callback) {
		Afro::process($route, $callback, 'GET');
	}

	function post($route, $callback) {
		Afro::process($route, $callback, 'POST');
	}

	function put($route, $callback) {
		Afro::process($route, $callback, 'PUT');
	}

	function delete($route, $callback) {
		Afro::process($route, $callback, 'DELETE');
	}

	function ajax($route, $callback) {
		Afro::process($route, $callback, AJAX);
	}

	class Afro {
		public static $foundRoute = FALSE;

		// Routing Data
		public $URI        = '';
		public $params     = array();
		public $sMethod    = '';
		public $format     = '';
		public $paramCount = 0;
		public $payload    = array();
		public $route      = '';

		// Request Data
		public $headers = array();
		public $ip      = '';

		public static function getInstance() {
			static $instance = NULL;
			if($instance === NULL) {
				$instance = new Afro;
			}
			return $instance;
		}

		public static function run() {
			if(!static::$foundRoute) {
				trigger_error('The requested route is not defined!');
			}

			ob_end_flush();
		}

		public static function process($route, $callback, $type) {
			$Afro = static::getInstance();

			$Afro->route = $route;

			if($type === AJAX) {
				$Afro->sMethod = isset($_SERVER['HTTP_X_REQUESTED_WITH']) ? $_SERVER['HTTP_X_REQUESTED_WITH'] : 'GET';
			}

			if(static::$foundRoute || (!preg_match('@^'.$route.'(?:\.(\w+))?$@uD', $Afro->URI, $matches) || $Afro->sMethod != $type)) {
				return FALSE;
			}

			static::$foundRoute = TRUE;

			// Clean and store the route
			$route = '/^' . str_replace('/', '\/', $route) . '$/';
			if(preg_match($route, $Afro->URI, $params)) {
				$routeParams = [$Afro];
				$routeParams = $routeParams + $params;
				return call_user_func_array($callback, $routeParams);
			}else{
				return $callback($Afro);
			}
		}

		public function __construct() {
			ob_start();
			// Routing Data
			$this->URI        = $this->getURI();
			$this->params     = explode('/', trim($this->URI, '/'));
			$this->params     = array_slice($this->params, -1, 1);
			$this->paramCount = count($this->params);
			$this->sMethod    = $this->getMethod();
			$this->payload    = $GLOBALS['_' . $this->sMethod];

			// Request Data
			$this->headers = getallheaders();
			$this->ip      = $_SERVER['REMOTE_ADDR'];
		}

		public function param($num) {
			$num--;
			$this->params[$num] = isset($this->params[$num]) ? basename($this->params[$num], '.' . $this->format) : NULL;
			return isset($this->params[$num]) ? $this->params[$num] : NULL;
		}

		protected function getMethod() {
			return isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET';
		}

		protected function getURI($prefixSlash = TRUE) {
			if(isset($_SERVER['PATH_INFO'])) {
				$uri = $_SERVER['PATH_INFO'];
			}elseif(isset($_SERVER['REQUEST_URI'])) {
				$uri = $_SERVER['REQUEST_URI'];

				if(strpos($uri, $_SERVER['SCRIPT_NAME']) === 0) {
					$uri = substr($uri, strlen($_SERVER['SCRIPT_NAME']));
				}elseif(strpos($uri, dirname($_SERVER['SCRIPT_NAME'])) === 0) {
					$uri = substr($uri, strlen(dirname($_SERVER['SCRIPT_NAME'])));
				}

				if(strncmp($uri, '?/', 2) === 0) $uri = substr($uri, 2);

				$parts = preg_split('#\?#i', $uri, 2);
				$uri   = $parts[0];

				if(isset($parts[1])) {
					$_SERVER['QUERY_STRING'] = $parts[1];
					parse_str($_SERVER['QUERY_STRING'], $_GET);
				}else {
					$_SERVER['QUERY_STRING'] = '';
					$_GET                    = array();
				}
				$uri = parse_url($uri, PHP_URL_PATH);
			}else {
				return FALSE;
			}

			$URIString = ($prefixSlash ? '/' : '') . str_replace(array('//', '../'), '/', trim($uri, '/'));
			$this->format = pathinfo($URIString, PATHINFO_EXTENSION);

			return str_replace('.' . $this->format, '', $URIString);
		}

		public function format($name, $callback) {
			$Afro = static::getInstance();

			if(!empty($Afro->format) && $name == $Afro->format) {
				return call_user_func($callback, $Afro);
			} else {
				return FALSE;
			} 
		}

		public function response($data, $for = NULL, $echo = TRUE) {
			$Afro = static::getInstance();
			if (is_null($for) && !empty($Afro->format)) $for = $Afro->format;
			$for = strtolower($for);
			switch ($for) {
				case 'json':
					if ($echo) {
						echo json_encode($data);
					} else {
						return json_encode($data);
					}
					break;
				case 'csv':
					$headings = array();
					if (isset($data[0]) && is_array($data[0])) {
						$headings = array_keys($data[0]);
					} else {
						// Single array
						$headings = array_keys($data);
						$data = array($data);
					}

					$output = implode(',', $headings).PHP_EOL;
					foreach ($data as &$row) {
						$output .= '"'.implode('","', $row).'"'.PHP_EOL;
					}

					return $output;
					break;
				case 'text':
				default:
					return $data;
			}
		}
	}

	$Afro = Afro::getInstance();

?>
