<?php
namespace App\Interfaces;
use JMS\Serializer\Serializer;
use Doctrine\Persistence\ManagerRegistry;

interface FileServiceInterface {
	public function write($content, int $bookId, ManagerRegistry $doctrine, string $pathToDirectory, string $name, Serializer $serializer): string | null;
	public function fetch(int $bookId, ManagerRegistry $doctrine, Serializer $serializer): string | null;
}