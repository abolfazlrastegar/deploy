Route::post('/webhook-deploy', function (Request $request) {
	$secret = 'e8437fa89c2e4fd3a7d0b5c69f83d2ae037bfbccf7aa6e0e8ff295dacb9e37f4';
	$signature = $request->header('X-Hub-Signature-256');
	$payload = file_get_contents('php://input');

	$calculated = 'sha256=' . hash_hmac('sha256', $payload, $secret);

	if (!hash_equals($calculated, $signature)) {
		abort(403, 'Invalid signature');
	}

	$data = json_decode($payload, true);
	$ref = $data['ref'] ?? '';

	if ($ref === 'refs/heads/main') {
		Artisan::call('deploy:prod');
		Log::info('deploy:prod executed');
		$message = 'Production deployed.';
	}else {
		$message = 'No matching branch. Nothing done......';
	}
// elseif ($ref === 'refs/heads/developer') {
//		Artisan::call('deploy:staging');
//		Log::info('deploy:staging executed');
//		$message = 'Staging deployed.';
//	}


	return response()->json([
		'status' => 'ok',
		'message' => $message,
	]);
});

Route::post('/front-deploy', function (Request $request) {
	$secret = 'e8437fa89c2e4fd3a7d0b5c69f83d2ae037bfbccf7aa6e0e8ff295dacb9e37f4';
	$signature = $request->header('X-Hub-Signature-256');
	$payload = file_get_contents('php://input');

	$calculated = 'sha256=' . hash_hmac('sha256', $payload, $secret);

	if (!hash_equals($calculated, $signature)) {
		abort(403, 'Invalid signature');
	}

	$data = json_decode($payload, true);
	$ref = $data['ref'] ?? '';

	if ($ref === 'refs/heads/main') {
		Artisan::call('front:deploy');
		Log::info('deploy:prod executed');
		$message = 'Production deployed.';
	} else {
		$message = 'No matching branch. Nothing done........';
	}

	return response()->json([
		'status' => 'ok',
		'message' => $message,
	]);
});

Route::post('/admin-deploy', function (Request $request) {
	$secret = 'e8437fa89c2e4fd3a7d0b5c69f83d2ae037bfbccf7aa6e0e8ff295dacb9e37f4';
	$signature = $request->header('X-Hub-Signature-256');
	$payload = file_get_contents('php://input');

	$calculated = 'sha256=' . hash_hmac('sha256', $payload, $secret);

	if (!hash_equals($calculated, $signature)) {
		abort(403, 'Invalid signature');
	}

	$data = json_decode($payload, true);
	$ref = $data['ref'] ?? '';

	if ($ref === 'refs/heads/main') {
		Artisan::call('admin:deploy');
		Log::info('deploy:prod executed');
		$message = 'Production deployed.';
	} else {
		$message = 'No matching branch. Nothing done........';
	}

	return response()->json([
		'status' => 'ok',
		'message' => $message,
	]);
});