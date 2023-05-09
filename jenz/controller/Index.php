<?php

namespace controller;
require_once ("../model/Api.php");
ob_start();
class Index
{
	public function __construct()
	{
		echo $this->getData();
	}

	public function getData()
	{
		$api = new \Api();
		if(isset($_SESSION['access_token']))
		{
			$authors = $api->getAuthors($_SESSION['access_token']);
		}
		else $this->redirectToLogin();

		if((isset($_REQUEST['op']) && $_REQUEST['op'] == 'delete') && isset($_REQUEST['authorId'])) {
			$this->deleteAuthor($_REQUEST['authorId']);
		}
		$myData = $api->getMyData($_SESSION['access_token']);

		$books = $api->getAllBooks($_SESSION['access_token']);

		$authorsIds = [];
		foreach($books['items'] as $booksData)
		{
			$singleBook = $api->getSingleBook($_SESSION['access_token'], $booksData['id']);
			$authorsIds[] = $singleBook['author']['id'];
		}
		$data = '<h1>'. $myData["first_name"] .' '. $myData["last_name"] .'</h1>';

		$data .= '<a href="Login.php">Logout</a><br><br>';
		$data .= '<a href="NewBook.php">Add new book</a><br><br>';
		if(isset($authors['items']))
		{
			$style = '"border: 1px solid black"';
			$data .= '<table style='. $style .'>
				<th style='. $style .'>First Name</th>
				<th style='. $style .'>Last Name</th>
				<th style='. $style .'>Birthday</th>
				<th style='. $style .'>Gender</th>
				<th style='. $style .'>Place Of Birth</th>
				<th style='. $style .'>Action</th>
			';

			foreach($authors['items'] as $authorData)
			{
				$data .= '<tr>';
				$data .= '<td style='. $style .'>'. $authorData['first_name'] .'</td>';
				$data .= '<td style='. $style .'>'. $authorData['last_name'] .'</td>';
				$data .= '<td style='. $style .'>'. date("d.m.Y.", strtotime($authorData['birthday'])) .'</td>';
				$data .= '<td style='. $style .'>'. $authorData['gender'] .'</td>';
				$data .= '<td style='. $style .'>'. $authorData['place_of_birth'] .'</td>';
				$data .= '<td style='. $style .'>';
				if(!in_array($authorData['id'], $authorsIds)) $data .= '<a href="Index.php?op=delete&authorId='. $authorData['id'] .'">Delete Author</a>';
				else $data .= '<a href="ListOfBooks.php?op=listBooks&authorId='. $authorData['id'] .'">List of books</a>';
				$data .= '</td></tr>';
			}
			$data .= '</table>';
			return $data;
		}

		return $authors['items'];
	}

	public function deleteAuthor($authorId) {
		$api = new \Api();
		$api->deleteAuthor($_SESSION['access_token'], $authorId);
		header("Location: Index.php");
	}

	private function redirectToLogin() {
		header("Location: Login.php");
		exit();
	}
}

new Index();