<?php
namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\AuthorService;
use Symfony\Component\HttpFoundation\Request;
use App\Interfaces\Controller;
use JMS\Serializer\SerializerBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/author', name: 'author_')]
class AuthorController extends AbstractController implements Controller {
	var $authorService;
	function __construct(AuthorService $authorService) {
		$this->authorService = $authorService;
	}
	#[Route('/', name: "all", methods: ['GET'])]
	public function getAll(ManagerRegistry $doctrine): Response {
		$serializer = SerializerBuilder::create()->build();
		$authors = $this->authorService->fetchAll($doctrine, $serializer);
		return new Response($authors);
	}
	#[Route('/{id}', name: "by_id", methods: ['GET'], requirements:['id' => '\d+'])]
	public function getById(int $id, ManagerRegistry $doctrine): Response {
		$serializer = SerializerBuilder::create()->build();
		$author = $this->authorService->fetchById($id, $doctrine, $serializer);
		if ($author === null) {
			return new Response("Author with id {$id} not found", 404);
		}
		return new Response($author);
	}
	#[Route('/create', name: 'new', methods: ['POST'])]
	public function create(Request $request, ManagerRegistry $doctrine, ValidatorInterface $validator): Response {
		$result = $this->authorService->add($request->getContent(), $doctrine, $validator);
		if (!$result) {
			return new Response("Bad Request", 400);
		}
		return new Response("{\"id\": {$result}}");
	}
	#[Route('/update/{id}', name: 'update', methods: ['POST'], requirements:['id' => '\d+'])]
	public function update(int $id, Request $request, ManagerRegistry $doctrine, ValidatorInterface $validator): Response {
		$result = $this->authorService->update($id, $request->getContent(), $doctrine, $validator);
		if ($result){
			return new Response("Updated author with id {$id}");
		}
		return new Response("Update failed", 400);
	}
	#[Route('/delete/{id}', name: 'delete', methods: ['DELETE'], requirements:['id' => '\d+'])]
	public function delete(int $id, ManagerRegistry $doctrine): Response {
		if ($this->authorService->remove($id, $doctrine)) {
			return new Response("Deleted author with id {$id}");
		}
		return new Response("Author with id {$id} not found", 404);
	}
}