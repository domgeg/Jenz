<?php
namespace controller;

require_once ("../model/Api.php");
ob_start();
class NewBook
{
	public function __construct()
	{
		$this->getData();
	}

	public function getData()
	{
		if(!isset($_SESSION['access_token'])) header("Location: Login.php");
		$api = new \Api();
		$authors = $api->getAuthors($_SESSION['access_token']);
		echo $this->pageView($authors);

		if(isset($_GET['submit']))
		{
			if(!isset($_GET['title']) || !isset($_GET['release_date']) || !isset($_GET['description']) || !isset($_GET['isbn']) || !isset($_GET['num_pages']) || !isset($_GET['author'])) die("Insert all data");
			$api->createBook($_SESSION['access_token'], $_GET);
			header("Location: Index.php");
			ob_end_flush();
		}

	}

	public function pageView($authors)
	{
		$data = '<!DOCTYPE html>
		<html lang="en">
		<head>
		<meta charset="UTF-8">
		<title>newBook</title>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
		
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
		<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
		</head>
		<script>
		$(document).ready(function(){
			$(".datepicker").datepicker({
				format: "dd.mm.yyyy.",
				autoclose: true
			});
		});
		</script>
		<body>
		<h1>Insert new book</h1>
		<a href="Index.php">Back</a>
		<form method="get">
			<label for="title">
				<span id="title">Title</span>
			</label>
			<input type="text" name="title" required><br>
		
			<label for="release_date">
				<span id="release_date">Release date</span>
			</label>
			<input type="text" name="release_date" class="form-control datepicker" style="width: 150px;" required><br>
		
			<label for="description">
				<span id="description">Description</span>
			</label>
			<textarea name="description" required></textarea><br>
		
			<label for="isbn">
				<span id="isbn">ISBN</span>
			</label>
			<input type="text" name="isbn" required><br>
		
			<label for="format">
				<span id="format">Format</span>
			</label>
			<input type="text" name="format" required><br>
		
			<label for="num_pages">
				<span id="num_pages">Number of pages</span>
			</label>
			<input type="text" name="num_pages" required><br>
			<label for="authors">
				<span id="authors">Choose author</span>
			</label>
			<select name="author" required>
			';
				foreach($authors['items'] as $authorData)
				{
					$data .= '<option value="'. $authorData['id'] .'">'. $authorData['first_name'] .' '. $authorData['last_name'] .'</option>';
		    	}

		$data .= '<input type="submit" name="submit">
		</form>
		</body>
		</html>';

		return $data;
	}
}

new NewBook();