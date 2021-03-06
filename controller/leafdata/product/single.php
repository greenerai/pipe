<?php
/**
 * Return a Single Product Object as JSON
 */

$cre = \CRE::factory($_SESSION['cre']);

$res = $cre->inventory_type()->one($ARG['guid']);

if (empty($res)) {
	return $RES->withJSON(array(
		'status' => 'failure',
		'detail' => 'Not Found',
	), 404);
}

$res = RBE_LeafData::de_fuck($res);

return $RES->withJSON(array(
	'status' => 'success',
	'result' => $res,
));
