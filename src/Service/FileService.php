<?php
namespace App\Service;
use App\Entity\Image;
use App\Entity\Book;
use Doctrine\Persistence\ManagerRegistry;
use App\Interfaces\FileServiceInterface;
use JMS\Serializer\Serializer;

class FileService implements FileServiceInterface {
	public function write($content, int $bookId, ManagerRegistry $doctrine, string $pathToDirectory, string $name, Serializer $serializer): string | null{
		$uniqid = uniqid();
		if (str_contains($name, 'jpg')){
			$name = "{$uniqid}.jpg";
		} else if (str_contains($name,'png')) {
			$name = "{$uniqid}.png";
		} else {
			return null;
		}
		$path = "{$pathToDirectory}{$name}";
		move_uploaded_file($content, $path);
		$entityManager = $doctrine->getManager();
		$image = new Image();
		$image->setPath($name);
		$entityManager->persist($image);
		$book = $entityManager->getRepository(Book::class)->find($bookId);
		if (!$book) {
			return null;
		}
		$existingImage = $book->getImage();
		if($existingImage){
			$entityManager->remove($existingImage);
		}
		$book->setImage($image);
		$entityManager->flush();
		$result = $serializer->serialize($image, 'json');
		return $result;
	}
	public function fetch(int $bookId, ManagerRegistry $doctrine, Serializer $serializer): string | null {
		$entityManager = $doctrine->getManager();
		$book = $entityManager->getRepository(Book::class)->find($bookId);
		if (!$book) {
			return null;
		}
		$image = $book->getImage();
		$result = $serializer->serialize($image, 'json');
		return $result;
	}
}