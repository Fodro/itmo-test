<?php
namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\BookService;
use App\Interfaces\Controller;
#[Route('/book', name: 'book_')]
class BookController extends AbstractController implements Controller {
	var $bookService;
	function __construct(BookService $bookService) {
		$this->bookService = $bookService;
	}
	#[Route('/all', name: "all")]
	public function getAll(): Response {
		$books = $this->bookService->fetchAll();
		return new Response($books);
	}
	#[Route('/by-id/{id}', name: "by_id", methods: ['GET'], requirements:['id' => '\d+'])]
	public function getById(int $id): Response {
		$book = $this->bookService->fetchById($id);
		if (!$book) {
			return new Response("Book with id {$id} not found", 404);
		}
		return new Response($book);
	}
	#[Route('/new', name: 'new', methods: ['POST'])]
	public function create(Request $request): Response {
		$result = $this->bookService->add($request->getContent());
		if (!$result) {
			return new Response("Bad request", 400);
		}
		return new Response("{\"id\": {$result}}");
	}
	#[Route('/update/{id}', name: 'update', methods: ['POST'], requirements:['id' => '\d+'])]
	public function update(int $id, Request $request): Response {
		$result = $this->bookService->update($id, $request->getContent());
		if ($result){
			return new Response("Updated book with id {$id}");
		}
		return new Response("Update failed", 400);
	}
	#[Route('/delete/{id}', name: 'delete', methods: ['DELETE'], requirements:['id' => '\d+'])]
	public function delete(int $id): Response {
		if ($this->bookService->remove($id)) {
			return new Response("Deleted book with id {$id}");
		}
		return new Response("Book with id {$id} not found", 404);
	}
}