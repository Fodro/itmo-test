<?php
namespace App\Service;
use App\Entity\Book;
use App\Service\BasicService;
use App\Interfaces\Service;

class BookService extends BasicService implements Service{
	public function fetchAll(): string | null {
		$books = $this->doctrine->getRepository(Book::class)->findAll();
		$booksJson = $this->serializeObj($books);
		return $booksJson;
	}
	public function fetchById(int $id): string | null {
		$book = $this->doctrine->getRepository(Book::class)->find($id);
		$bookJson = $this->serializeObj($book);
		return $bookJson;
	}
	public function add(string $jsonBody): int | null {
		$book = new Book();
		$bookObject = json_decode($jsonBody);
		$book->setTitle($bookObject->title);
		$book->setPublishingYear($bookObject->publishing_year);
		$book->setISBN($bookObject->_isbn);
		$book->setPagesCount($bookObject->pages_count);
		if ($this->validateObj($book))
		{
			$this->entityManager->persist($book);
			$this->entityManager->flush();
			return $book->getId();
		}
		return null;
	}
	public function update(int $id, string $jsonBody): bool {
		$book = $this->entityManager->getRepository(Book::class)->find($id);
		$updateObject = json_decode($jsonBody);
		if (!$book) {
			return False;
		} 
		$book->setTitle(property_exists($updateObject, 'title') ? $updateObject->title : $book->getTitle());
		$book->setPublishingYear(property_exists($updateObject, 'publishing_year') ? $updateObject->publishing_year : $book->getPublishingYear());
		$book->setISBN(property_exists($updateObject, '_isbn') ? $updateObject->_isbn : $book->getISBN());
		$book->setPagesCount(property_exists($updateObject, 'pages_count') ? $updateObject->pages_count : $book->getPagesCount());
		if (!$this->validateObj($book)) {
			return False;
		}
		$this->entityManager->flush();
		return True;
	}
	public function remove($id): bool {
		$book = $this->entityManager->getRepository(Book::class)->find($id);
		if (!$book) {
			return False;
		}
		$this->entityManager->remove($book);
		$this->entityManager->flush();
		return True;
	}
}