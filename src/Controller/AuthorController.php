<?php
namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\AuthorService;
use Symfony\Component\HttpFoundation\Request;
use App\Interfaces\Controller;

#[Route('/author', name: 'author_')]
class AuthorController extends AbstractController implements Controller {
	var $authorService;
	function __construct(AuthorService $authorService) {
		$this->authorService = $authorService;
	}
	#[Route('/', name: "all", methods: ['GET'])]
	public function getAll(): Response {
		$authors = $this->authorService->fetchAll();
		return new Response($authors);
	}
	#[Route('/{id}', name: "by_id", methods: ['GET'], requirements:['id' => '\d+'])]
	public function getById(int $id): Response {
		$author = $this->authorService->fetchById($id);
		if ($author === null) {
			return new Response("Author with id {$id} not found", 404);
		}
		return new Response($author);
	}
	#[Route('/create', name: 'new', methods: ['POST'])]
	public function create(Request $request): Response {
		$result = $this->authorService->add($request->getContent());
		if (!$result) {
			return new Response("Bad Request", 400);
		}
		return new Response("{\"id\": {$result}}");
	}
	#[Route('/update/{id}', name: 'update', methods: ['POST'], requirements:['id' => '\d+'])]
	public function update(int $id, Request $request): Response {
		$result = $this->authorService->update($id, $request->getContent());
		if ($result){
			return new Response("Updated author with id {$id}");
		}
		return new Response("Update failed", 400);
	}
	#[Route('/delete/{id}', name: 'delete', methods: ['DELETE'], requirements:['id' => '\d+'])]
	public function delete(int $id): Response {
		if ($this->authorService->remove($id)) {
			return new Response("Deleted author with id {$id}");
		}
		return new Response("Author with id {$id} not found", 404);
	}
}