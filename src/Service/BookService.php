<?php
namespace App\Service;
use App\Entity\Book;
use App\Entity\Author;
use App\Service\BasicService;
use App\Interfaces\Service;
use Doctrine\Persistence\ManagerRegistry;
use JMS\Serializer\Serializer;
use Symfony\Component\Validator\Validator\ValidatorInterface;



class BookService extends BasicService implements Service{
	public function fetchAll(ManagerRegistry $doctrine, Serializer $serializer): string | null {
		$books = $doctrine->getRepository(Book::class)->findAll();
		$booksJson = $this->serializeObj($books, $serializer);
		return $booksJson;
	}
	public function fetchById(int $id, ManagerRegistry $doctrine, Serializer $serializer): string | null {
		$book = $doctrine->getRepository(Book::class)->find($id);
		$bookJson = $this->serializeObj($book, $serializer);
		return $bookJson;
	}
	public function add(string $jsonBody, ManagerRegistry $doctrine, ValidatorInterface $validator): int | null {
		$bookObject = json_decode($jsonBody);
		$entityManager = $doctrine->getManager();
		$existingBookbyISBN = $doctrine->getRepository(Book::class)->findOneBy(
			array('title' => $bookObject->title, 'ISBN' => $bookObject->_isbn)
		);
		if ($existingBookbyISBN !== null) {
			return null;
		}
		$existingBookbyYear = $doctrine->getRepository(Book::class)->findOneBy(
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
			$author = $doctrine->getRepository(Author::class)->find($currentId);
			if (!$author){
				return null;
			}
			$book->addAuthor($author);
		}
		if ($this->validateObj($book, $validator))
		{
			$entityManager->persist($book);
			$entityManager->flush();
			return $book->getId();
		}
		return null;
	}
	public function update(int $id, string $jsonBody, ManagerRegistry $doctrine, ValidatorInterface $validator): bool {
		$entityManager = $doctrine->getManager();
		$book = $entityManager->getRepository(Book::class)->find($id);
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
				$author = $entityManager->getRepository(Author::class)->find($currentId);
				if (!$author){
					return False;
				}
				$book->addAuthor($author);
			}
		}
		$existingBookbyISBN = $doctrine->getRepository(Book::class)->findOneBy(
			array('title' => $book->getTitle(), 'ISBN' => $book->getISBN())
		);
		if ($existingBookbyISBN !== null and $existingBookbyISBN->getId() !== $book->getId()) {
			return False;
		}
		$existingBookbyYear = $doctrine->getRepository(Book::class)->findOneBy(
			array('title' => $book->getTitle(), 'publishing_year' => $book->getPublishingYear())
		);
		if ($existingBookbyYear !== null and $existingBookbyYear->getId() !== $book->getId()) {
			return False;
		}
		if (!$this->validateObj($book, $validator)) {
			return False;
		}
		$entityManager->flush();
		return True;
	}
	public function remove($id, ManagerRegistry $doctrine): bool {
		$entityManager = $doctrine->getManager();
		$book = $entityManager->getRepository(Book::class)->find($id);
		if (!$book) {
			return False;
		}
		$entityManager->remove($book);
		$entityManager->flush();
		return True;
	}
}