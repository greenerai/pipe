<?php
/**
	OpenTHC Pipe Application Bootstrap
*/

define('APP_NAME', 'OpenTHC');
define('APP_SITE', 'https://pipe.openthc.org');
define('APP_ROOT', dirname(__FILE__));
define('APP_SALT', md5(APP_NAME . APP_SITE));

openlog('openthc-pipe', LOG_ODELAY|LOG_PID, LOG_LOCAL0);

error_reporting((E_ALL|E_STRICT) ^ E_NOTICE);

// My (crappy) AutoLoader
spl_autoload_register(function($c) {

	$c = str_replace('_', '/', $c);
	$f = sprintf('%s/lib/%s.php', APP_ROOT, $c);

	if (is_file($f)) {
		require_once($f);
	}

}, true, false);

require_once(APP_ROOT . '/vendor/autoload.php');
require_once(APP_ROOT . '/lib/RCE.php');
require_once(APP_ROOT . '/lib/RCE_HTTP.php');

function _from_rce_file($f, $RES, $ARG)
{
	$f = trim($f, '/');
	$f = sprintf('%s/controller/%s/%s', APP_ROOT, $_SESSION['rbe-base'], $f);
	if (!is_file($f)) {
		return $RES->withJSON(array(
			'status' => 'failure',
			'detail' => 'Not Found',
			'_f' => $f,
		), 404);
	}

	$r = require_once($f);

	return $r;

	// $RES = new \Response_From_File();
	//return $RES->execute(sprintf('%s/, $_SESSION['rbe-base']), $ARG);

};

function _exit_501($RES)
{
	return $RES->withJSON(array(
		'status' => 'failure',
		'detail' => 'Not Implemented',
	), 501, JSON_PRETTY_PRINT);
}

function _hash_obj($o)
{
	_ksort_r($o);
	$hash = sha1(json_encode($o));
	return $hash;
}



class App
{
	static function log() { }

	static function metric()
	{
		return new App_Metric();
	}

}

class App_Metric
{
	function counter() { }
	function timing() { }
}

/**
	A Faker cause some of the RBE tools depend on this
*/
class License
{
	static function findByCode($x)
	{
		//var_dump($x);
		//exit;
		return array(
			'code' => $x,
			'guid' => $x,
		);
	}

	static function findByGUID($x)
	{
		//var_dump($x);
		//exit;
		return array(
			'code' => $x,
			'guid' => $x,
		);
	}

}

class Response_From_File extends Slim\Http\Response
{
	function execute($f, $ARG=null)
	{
		$f = trim($f, '/');
		$f = sprintf('%s/controller/%s', APP_ROOT, $f);
		if (!is_file($f)) {
			return $this->withJSON(array(
				'status' => 'failure',
				'detail' => 'Not Found',
				'_f' => $f,
			), 404);
		}
		$r = require_once($f);
		return $r;
	}
}


class Response_JSON extends Slim\Http\Response
{
	function withJSON($data, $code=200, $opts=null)
	{
		if (empty($opts)) {
			$opts = JSON_PRETTY_PRINT;
		}

		return parent::withJSON($data, $code, $opts);

	}
}
