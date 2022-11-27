<?php
namespace App\Interfaces;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

interface Controller {
	public function getAll(): Response;
	public function getById(int $id): Response;
	public function create(Request $request): Response;
	public function update(int $id, Request $request): Response;
	public function delete(int $id): Response;
}