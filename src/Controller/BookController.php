<?php
namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\BookService;
use App\Interfaces\Controller;
use App\Service\FileService;
use JMS\Serializer\SerializerBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/book', name: 'book_')]
class BookController extends AbstractController implements Controller  {
	var $bookService;
	function __construct(BookService $bookService) {
		$this->bookService = $bookService;
	}
	#[Route('/', name: "all", methods: ['GET'])]
	public function getAll(ManagerRegistry $doctrine): Response {
		$serializer = SerializerBuilder::create()->build();
		$books = $this->bookService->fetchAll($doctrine, $serializer);
		return new Response($books);
	}
	#[Route('/{id}', name: "by_id", methods: ['GET'], requirements:['id' => '\d+'])]
	public function getById(int $id, ManagerRegistry $doctrine): Response {
		$serializer = SerializerBuilder::create()->build();
		$book = $this->bookService->fetchById($id, $doctrine, $serializer);
		if (!$book) {
			return new Response("Book with id {$id} not found", 404);
		}
		return new Response($book);
	}
	#[Route('/create', name: 'new', methods: ['POST'])]
	public function create(Request $request, ManagerRegistry $doctrine, ValidatorInterface $validator): Response {
		$result = $this->bookService->add($request->getContent(), $doctrine, $validator);
		if (!$result) {
			return new Response("Bad request", 400);
		}
		return new Response("{\"id\": {$result}}");
	}
	#[Route('/update/{id}', name: 'update', methods: ['POST'], requirements:['id' => '\d+'])]
	public function update(int $id, Request $request, ManagerRegistry $doctrine, ValidatorInterface $validator): Response {
		$result = $this->bookService->update($id, $request->getContent(), $doctrine, $validator);
		if ($result){
			return new Response("Updated book with id {$id}");
		}
		return new Response("Update failed", 400);
	}
	#[Route('/delete/{id}', name: 'delete', methods: ['DELETE'], requirements:['id' => '\d+'])]
	public function delete(int $id, ManagerRegistry $doctrine): Response {
		if ($this->bookService->remove($id, $doctrine)) {
			return new Response("Deleted book with id {$id}");
		}
		return new Response("Book with id {$id} not found", 404);
	}
	#[Route('/{id}/image/upload', name: 'upload', methods: ['POST'], requirements:['id' => '\d+'])]
	public function uploadImage(int $id, ManagerRegistry $doctrine, FileService $fileService): Response {
		$serializer = SerializerBuilder::create()->build();
		if ($_FILES["file"]["error"] > 0)
		{
			return new Response("Error: " . $_FILES["file"]["error"], 400);
		}
		else
		{
			$content = $_FILES["file"]["tmp_name"];
			$name = $_FILES["file"]["name"];
			$result = $fileService->write($content, $id, $doctrine, $_SERVER['DOCUMENT_ROOT'], $name, $serializer);
			if ($result) {
				return new Response($result);
			}
			else {
				return new Response("Error", 400);
			}
		}
	}
	#[Route('/{id}/image', name: 'get_image', methods: ['GET'], requirements:['id' => '\d+'])]
	public function getImage(int $id, ManagerRegistry $doctrine, FileService $fileService): Response {
		$serializer = SerializerBuilder::create()->build();
		$result = $fileService->fetch($id, $doctrine, $serializer);
		if(!$result) {
			return new Response("Error", 400);
		}
		return new Response($result);
	}
}