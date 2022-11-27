<?php
namespace App\Interfaces;

interface Service {
	public function fetchAll(): string | null;
	public function fetchById(int $id): string | null;
	public function add(string $jsonBody): int | null;
	public function update(int $id, string $jsonBody): bool;
	public function remove(int $id): bool;
}