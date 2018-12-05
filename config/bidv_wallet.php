<?php

return [
	'url'              => env('BIDV_WALLET_URL', ''),
	'service_id'       => env('BIDV_WALLET_SERVICE_ID', ''),
	'merchant_id'      => env('BIDV_WALLET_MERCHANT_ID', ''),
	'private_key_bidv' => env('BIDV_WALLET_SECRET_KEY', ''),
	'public_key_bidv'  => '/test/example_public_key.pem',
	'private_key_9pay' => '/test/example_private_key.pem',
];