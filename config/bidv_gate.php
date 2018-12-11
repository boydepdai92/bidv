<?php

return [
	'url'              => env('BIDV_GATE_URL', ''),
	'service_id'       => env('BIDV_GATE_SERVICE_ID', ''),
	'merchant_id'      => env('BIDV_GATE_MERCHANT_ID', ''),
	'private_key_bidv' => env('BIDV_GATE_SECRET_KEY', ''),
	'public_key_bidv'  => '/test/example_public_key.pem',
	'private_key_9pay' => '/test/example_private_key.pem',
];