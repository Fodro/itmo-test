<?php
namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/author', name: 'author_')]
class AuthorController extends AbstractController {
	#[Route('/all', name: "all")]
	public function getAll(): Response {
		return new Response('get all authors');
	}
	#[Route('/by-id/{id}', name: "by_id", methods: ['GET'], requirements:['id' => '\d+'])]
	public function getById(int $id): Response {
		return new Response("get author {$id}");
	}
	#[Route('/new', name: 'new', methods: ['POST'])]
	public function newAuthor(): Response {
		return new Response("new author");
	}
	#[Route('/update/{id}', name: 'update', methods: ['POST'], requirements:['id' => '\d+'])]
	public function updateAuthor(int $id): Response {
		return new Response("update author {$id}");
	}
	#[Route('/delete/{id}', name: 'delete', methods: ['DELETE'], requirements:['id' => '\d+'])]
	public function deleteAuthor(int $id): Response {
		return new Response("delete author {$id}");
	}
}