<?php
// postavljanje timeout sessiona na 1 sat
$timeout = 3600;
session_set_cookie_params($timeout);
session_cache_expire($timeout / 60);
session_start();
class Api
{
	public function getToken($email, $password): void
	{
		try
		{
			$url = 'https://symfony-skeleton.q-tests.com/api/v2/token';
			$data = [
				'email' => $email,
				'password' => $password
			];
			$payload = json_encode($data);
			$ch = curl_init();

			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
			curl_setopt($ch, CURLOPT_HTTPHEADER, [
				'Content-Type: application/json',
				'accept: application/json'
			]);

			$response = curl_exec($ch);
			$response = json_decode($response, true);
			if(!isset($response['token_key'])) throw new Exception("Token not found!");
			else
			{
				$_SESSION['access_token'] = $response['token_key'];
			}
		}
		catch(Exception $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * @param $token
	 * @param $authorId
	 * @return array
	 */
	public function getSingleAuthor($token, $authorId)
	{
		try
		{
			$url = 'https://symfony-skeleton.q-tests.com/api/v2/authors/'. $authorId;
			$options = [
				'http' => [
					'method' => 'GET',
					'header' => "Authorization: Bearer " . $token . "\r\n"
				]
			];
			$context = stream_context_create($options);
			$data = file_get_contents($url, false, $context);
			if(!$data) throw new Exception("Author not found!");
			return json_decode($data, true);
		}
		catch(Exception $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * @param $token
	 * @return array
	 */
	public function getAuthors($token)
	{
		$url = 'https://symfony-skeleton.q-tests.com/api/v2/authors?orderBy=id&direction=ASC&limit=12&page=1';

		$options = [
			'http' => [
				'method' => 'GET',
				'header' => "Authorization: Basic ". $token
			]
		];

		$context = stream_context_create($options);
		$data = file_get_contents($url, false, $context);
		$data = json_decode($data, true);

		return $data;
	}

	/**
	 * @param $token
	 * @return array
	 */
	public function getMyData($token)
	{
		$url = 'https://symfony-skeleton.q-tests.com/api/v2/me?orderBy=id&direction=ASC&limit=12&page=1';

		$options = [
			'http' => [
				'method' => 'GET',
				'header' => "Authorization: Basic ". $token
			]
		];

		$context = stream_context_create($options);
		$data = file_get_contents($url, false, $context);
		$data = json_decode($data, true);

		return $data;
	}

	/**
	 * @param $token
	 * @return array
	 */
	public function getAllBooks($token)
	{
		$url = 'https://symfony-skeleton.q-tests.com/api/v2/books?orderBy=id&direction=ASC&limit=12&page=1';
		$options = [
			'http' => [
				'method' => 'GET',
				'header' => "Authorization: Bearer " . $token . "\r\n"
			]
		];
		$context = stream_context_create($options);
		$data = file_get_contents($url, false, $context);

		return json_decode($data, true);
	}

	/**
	 * @param $token
	 * @param $bookId
	 * @return array
	 */
	public function getSingleBook($token, $bookId)
	{
		$url = 'https://symfony-skeleton.q-tests.com/api/v2/books/'. $bookId;
		$options = [
			'http' => [
				'method' => 'GET',
				'header' => "Authorization: Bearer " . $token . "\r\n"
			]
		];
		$context = stream_context_create($options);
		$data = file_get_contents($url, false, $context);

		return json_decode($data, true);
	}

	public function deleteBook($token, $bookId)
	{
		try
		{
			$url = 'https://symfony-skeleton.q-tests.com/api/v2/books/'. $bookId;
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, [
				'Content-Type: application/json',
				'Authorization: Bearer ' . $token
			]);
			$result = curl_exec($ch);
			if(!$result) throw new Exception("Unknown book!");
			curl_close($ch);
		}
		catch(Exception $e) {
			echo $e->getMessage();
		}
	}

	public function deleteAuthor($token, $authorId)
	{
		try
		{
			$url = 'https://symfony-skeleton.q-tests.com/api/v2/authors/' . $authorId;
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, [
				'Content-Type: application/json',
				'Authorization: Bearer ' . $token
			]);
			$result = curl_exec($ch);
			if(!$result) throw new Exception("Unknown author!");
			curl_close($ch);
		}
		catch(Exception $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * @param string $token
	 * @param array $bookData
	 * @return void
	 * @throws Exception
	 */
	public function createBook($token, $bookData)
	{
		$url = 'https://symfony-skeleton.q-tests.com/api/v2/books';

		$releaseDate = $bookData['release_date'];
		$timestamp = strtotime($releaseDate);
		$releaseDate = date('Y-m-d\TH:i:s.u\Z', $timestamp);

		$data = [
			'author' => ['id' => htmlspecialchars($bookData['author'])],
			'title' => htmlspecialchars($bookData['title']),
			'release_date' => htmlspecialchars($releaseDate),
			'description' => htmlspecialchars($bookData['description']),
			'isbn' => htmlspecialchars($bookData['isbn']),
			'format' => htmlspecialchars($bookData['format']),
			'number_of_pages' => (int)htmlspecialchars($bookData['num_pages']),
		];

		$payload = json_encode($data);

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			'Content-Type: application/json',
			'Authorization: Bearer '.$token,
			'accept: application/json'
		]);

		$response = curl_exec($ch);
		curl_close($ch);

		$response = json_decode($response, true);

		if(isset($response['id'])) {
			return $response['id'];
		} else {
			throw new Exception("Could not create new book.");
		}
	}
}