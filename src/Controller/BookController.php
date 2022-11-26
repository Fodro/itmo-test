<?php
namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
#[Route('/book', name: 'book_')]
class BookController extends AbstractController {
	#[Route('/all', name: "all")]
	public function getAll(): Response {
		return new Response('get all books');
	}
	#[Route('/by-id/{id}', name: "by_id", methods: ['GET'], requirements:['id' => '\d+'])]
	public function getById(int $id): Response {
		return new Response("get book {$id}");
	}
	#[Route('/new', name: 'new', methods: ['POST'])]
	public function newBook(): Response {
		return new Response("new book");
	}
	#[Route('/update/{id}', name: 'update', methods: ['POST'], requirements:['id' => '\d+'])]
	public function updateBook(int $id): Response {
		return new Response("update book {$id}");
	}
	#[Route('/delete/{id}', name: 'delete', methods: ['DELETE'], requirements:['id' => '\d+'])]
	public function deleteBook(int $id): Response {
		return new Response("delete book {$id}");
	}
}