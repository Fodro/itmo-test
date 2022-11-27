<?php
namespace App\Service;
use App\Entity\Author;
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
		$author = new Author();
		$authorObject = json_decode($jsonBody);
		$author->setName($authorObject->name);
		$author->setSurname($authorObject->surname);
		$author->setPatronymic($authorObject->patronymic);
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