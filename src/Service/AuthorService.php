<?php
namespace App\Service;
use App\Entity\Author;
use App\Entity\Book;
use App\Service\BasicService;
use App\Interfaces\Service;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use JMS\Serializer\Serializer;

class AuthorService extends BasicService implements Service{
	public function fetchAll(ManagerRegistry $doctrine, Serializer $serializer): string | null{
		$authors = $doctrine->getRepository(Author::class)->findAll();
		$authorsJson = $this->serializeObj($authors, $serializer);
		return $authorsJson;
	}
	public function fetchById(int $id, ManagerRegistry $doctrine, Serializer $serializer): string | null{
		$author = $doctrine->getRepository(Author::class)->find($id);
		$authorJson = $this->serializeObj($author, $serializer);
		return $authorJson;
	}
	public function add(string $jsonBody, ManagerRegistry $doctrine, ValidatorInterface $validator): int | null {
		$authorObject = json_decode($jsonBody);
		$entityManager = $doctrine->getManager();
		$existingAuthor = $doctrine->getRepository(Author::class)->findOneBy(
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
			$book = $entityManager->getRepository(Book::class)->find($currentId);
			if (!$book) {
				return null;
			}
			$author->addBook($book);
		}
		if ($this->validateObj($author, $validator))
		{
			$entityManager->persist($author);
			$entityManager->flush();
			return $author->getId();
		}
		return null;
	}
	public function update(int $id, string $jsonBody, ManagerRegistry $doctrine, ValidatorInterface $validator): bool {
		$entityManager = $doctrine->getManager();
		$author = $entityManager->getRepository(Author::class)->find($id);
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
				$book = $entityManager->getRepository(Book::class)->find($currentId);
				if (!$book) {
					return False;
				}
				$author->addBook($book);
			}
		}
		$existingAuthor = $doctrine->getRepository(Author::class)->findOneBy(
			array('name' => $author->getName(), 'surname' => $author->getSurname(), 'patronymic' => $author->getPatronymic()));
		if ($existingAuthor !== null and $existingAuthor->getId() !== $author->getId()){
			return False;
		}
		if(!$this->validateObj($author, $validator)){
			return False;
		}
		$entityManager->flush();
		return True;
	}
	public function remove(int $id, ManagerRegistry $doctrine): bool {
		$entityManager = $doctrine->getManager();
		$author = $entityManager->getRepository(Author::class)->find($id);
		if (!$author){
			return False;
		}
		$entityManager->remove($author);
		$entityManager->flush();
		return True;
	}
}