<?php
namespace App\Service;
use App\Entity\Book;
use App\Entity\Author;
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
		$bookObject = json_decode($jsonBody);
		$existingBookbyISBN = $this->doctrine->getRepository(Book::class)->findOneBy(
			array('title' => $bookObject->title, 'ISBN' => $bookObject->_isbn)
		);
		if ($existingBookbyISBN !== null) {
			return null;
		}
		$existingBookbyYear = $this->doctrine->getRepository(Book::class)->findOneBy(
			array('title' => $bookObject->title, 'publishing_year' => $bookObject->publishing_year)
		);
		if ($existingBookbyYear !== null) {
			return null;
		}
		$book = new Book();
		$book->setTitle($bookObject->title);
		$book->setPublishingYear($bookObject->publishing_year);
		$book->setISBN($bookObject->_isbn);
		$book->setPagesCount($bookObject->pages_count);
		$authorIds = $bookObject->authors;
		foreach ($authorIds as $currentId){
			$author = $this->entityManager->getRepository(Author::class)->find($currentId);
			if (!$author){
				return null;
			}
			$book->addAuthor($author);
		}
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
		if (property_exists($updateObject, 'authors')){
			$existingAuthors = $book->getAuthors();
			foreach ($existingAuthors as $currentAuthor){
				$book->removeAuthor($currentAuthor);
			}
			$authorIds = $updateObject->authors;
			foreach ($authorIds as $currentId){
				$author = $this->entityManager->getRepository(Author::class)->find($currentId);
				if (!$author){
					return False;
				}
				$book->addAuthor($author);
			}
		}
		$existingBookbyISBN = $this->doctrine->getRepository(Book::class)->findOneBy(
			array('title' => $book->getTitle(), 'ISBN' => $book->getISBN())
		);
		if ($existingBookbyISBN !== null and $existingBookbyISBN->getId() !== $book->getId()) {
			return False;
		}
		$existingBookbyYear = $this->doctrine->getRepository(Book::class)->findOneBy(
			array('title' => $book->getTitle(), 'publishing_year' => $book->getPublishingYear())
		);
		if ($existingBookbyYear !== null and $existingBookbyYear->getId() !== $book->getId()) {
			return False;
		}
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