<?php
namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\AuthorService;
use Symfony\Component\HttpFoundation\Request;

#[Route('/author', name: 'author_')]
class AuthorController extends AbstractController {
	var $authorService;
	function __construct(AuthorService $authorService) {
		$this->authorService = $authorService;
	}
	#[Route('/all', name: "all")]
	public function getAll(): Response {
		$authors = $this->authorService->fetchAll();
		return new Response($authors);
	}
	#[Route('/by-id/{id}', name: "by_id", methods: ['GET'], requirements:['id' => '\d+'])]
	public function getById(int $id): Response {
		$author = $this->authorService->fetchById($id);
		if ($author == null) {
			return new Response("Author with id {$id} not found", 404);
		}
		return new Response($author);
	}
	#[Route('/new', name: 'new', methods: ['POST'])]
	public function newAuthor(Request $request): Response {
		$result = $this->authorService->addAuthor($request->getContent());
		if ($result == null) {
			return new Response("Bad Request", 400);
		}
		return new Response("{\"id\": {$result}}");
	}
	#[Route('/update/{id}', name: 'update', methods: ['POST'], requirements:['id' => '\d+'])]
	public function updateAuthor(int $id, Request $request): Response {
		$result = $this->authorService->updateAuthor($id, $request->getContent());
		if ($result){
			return new Response("Updated author with id {$id}");
		}
		return new Response("Update failed", 400);
	}
	#[Route('/delete/{id}', name: 'delete', methods: ['DELETE'], requirements:['id' => '\d+'])]
	public function deleteAuthor(int $id): Response {
		if ($this->authorService->removeAuthor($id)) {
			return new Response("deleted author {$id}");
		}
		return new Response("Author with id {$id} not found", 404);
	}
}