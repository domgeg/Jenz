<?php

namespace controller;
require_once ("../model/Api.php");
ob_start();
class ListOfBooks
{
	public function __construct()
	{
		echo $this->getData();
	}

	public function getData()
	{
		if(!isset($_SESSION['access_token'])) header("Location: Login.php");

		if((isset($_REQUEST['op']) && $_REQUEST['op'] == 'deleteBook') && isset($_REQUEST['bookId']))
		{
			$this->deleteBook($_REQUEST['bookId']);
		}
		$api = new \Api();
		$books = $api->getAllBooks($_SESSION['access_token']);
		$booksArray = [];
		$authorData = $api->getSingleAuthor($_SESSION['access_token'], $_REQUEST['authorId']);
		$title = 'No Books';
		if($books['items'])
		{
			foreach($books['items'] as $booksData)
			{
				$singleBook = $api->getSingleBook($_SESSION['access_token'], $booksData['id']);
				if($singleBook['author']['id'] == $_REQUEST['authorId']) $booksArray[] = $booksData;
			}
			$title = '<h1>List of books of '. $authorData['first_name'] .' '. $authorData['last_name'] .'</h1>';
		}
		$data = $title;
		if(count($booksArray))
		{
			$style = '"border: 1px solid black"';
			$data .= '<table style='. $style .'>
				<th style='. $style .'>Title</th>
				<th style='. $style .'>Release Date</th>
				<th style='. $style .'>ISBN</th>
				<th style='. $style .'>Format</th>
				<th style='. $style .'>Number of pages</th>
				<th style='. $style .'>Action</th>
			';

			foreach($booksArray as $bookData)
			{
				$data .= '<tr>';
				$data .= '<td style='. $style .'>'. $bookData['title'] .'</td>';
				$data .= '<td style='. $style .'>'. date("d.m.Y.", strtotime($bookData['release_date'])) .'</td>';
				$data .= '<td style='. $style .'>'. $bookData['isbn'] .'</td>';
				$data .= '<td style='. $style .'>'. $bookData['format'] .'</td>';
				$data .= '<td style='. $style .'>'. $bookData['number_of_pages'] .'</td>';
				$data .= '<td style='. $style .'><a href="ListOfBooks.php?op=deleteBook&bookId='. $bookData['id'] .'">Delete book</a>';
				$data .= '</td></tr>';
			}
			$data .= '</table>';
			return $data;
		}
	}

	public function deleteBook($bookId)
	{
		$api = new \Api();
		$api->deleteBook($_SESSION['access_token'], $bookId);
		header("Location: Index.php");
	}
}

new ListOfBooks();