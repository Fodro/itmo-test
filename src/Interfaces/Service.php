<?php
namespace App\Interfaces;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use JMS\Serializer\Serializer;

interface Service {
	public function fetchAll(ManagerRegistry $doctrine, Serializer $serializer): string | null;
	public function fetchById(int $id, ManagerRegistry $doctrine, Serializer $serializer): string | null;
	public function add(string $jsonBody, ManagerRegistry $doctrine, ValidatorInterface $validator): int | null;
	public function update(int $id, string $jsonBody, ManagerRegistry $doctrine, ValidatorInterface $validator): bool;
	public function remove(int $id, ManagerRegistry $doctrine): bool;
}