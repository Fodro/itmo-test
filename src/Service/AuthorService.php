<?php
namespace App\Service;
use App\Entity\Author;
use App\Entity\Book;
use App\Service\BasicService;
use App\Interfaces\Service;

class AuthorService extends BasicService implements Service{
	public function fetchAll(): string | null{
		$authors = $this->doctrine->getRepository(Author::class)->findAll();
		$authorsJson = $this->serializeObj($authors);
		return $authorsJson;
	}
	public function fetchById(int $id): string | null{
		$author = $this->doctrine->getRepository(Author::class)->find($id);
		$authorJson = $this->serializeObj($author);
		return $authorJson;
	}
	public function add(string $jsonBody): int | null {
		$authorObject = json_decode($jsonBody);
		$existingAuthor = $this->doctrine->getRepository(Author::class)->findOneBy(
			array('name' => $authorObject->name, 'surname' => $authorObject->surname, 'patronymic' => $authorObject->patronymic));
		if ($existingAuthor !== null){
			return null;
		}
		$author = new Author();
		$author->setName($authorObject->name);
		$author->setSurname($authorObject->surname);
		$author->setPatronymic($authorObject->patronymic);
		$bookIds = $authorObject->books;
		foreach ($bookIds as $currentId){
			$book = $this->entityManager->getRepository(Book::class)->find($currentId);
			if (!$book) {
				return null;
			}
			$author->addBook($book);
		}
		if ($this->validateObj($author))
		{
			$this->entityManager->persist($author);
			$this->entityManager->flush();
			return $author->getId();
		}
		return null;
	}
	public function update(int $id, string $jsonBody): bool {
		$author = $this->entityManager->getRepository(Author::class)->find($id);
		$updateObject = json_decode($jsonBody);
		if (!$author){
			return False;
		}
		$author->setName(property_exists($updateObject, 'name') ? $updateObject->name : $author->getName());
		$author->setSurname(property_exists($updateObject, 'surname') ? $updateObject->surname : $author->getSurname());
		$author->setPatronymic(property_exists($updateObject, 'patronymic')? $updateObject->patronymic : $author->getPatronymic());
		if (property_exists($updateObject, 'books')) {
			$existingBooks = $author->getBooks();
			foreach ($existingBooks as $currentBook){
				$author->removeBook($currentBook);
			}
			$bookIds = $updateObject->books;
			foreach ($bookIds as $currentId){
				$book = $this->entityManager->getRepository(Book::class)->find($currentId);
				if (!$book) {
					return False;
				}
				$author->addBook($book);
			}
		}
		$existingAuthor = $this->doctrine->getRepository(Author::class)->findOneBy(
			array('name' => $author->getName(), 'surname' => $author->getSurname(), 'patronymic' => $author->getPatronymic()));
		if ($existingAuthor !== null and $existingAuthor->getId() !== $author->getId()){
			return False;
		}
		if(!$this->validateObj($author)){
			return False;
		}
		$this->entityManager->flush();
		return True;
	}
	public function remove(int $id): bool {
		$author = $this->entityManager->getRepository(Author::class)->find($id);
		if (!$author){
			return False;
		}
		$this->entityManager->remove($author);
		$this->entityManager->flush();
		return True;
	}
}