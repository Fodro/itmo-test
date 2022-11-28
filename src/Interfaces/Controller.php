<?php
namespace App\Interfaces;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Validator\ValidatorInterface;

interface Controller {
	public function getAll(ManagerRegistry $doctrine): Response;
	public function getById(int $id, ManagerRegistry $doctrine): Response;
	public function create(Request $request, ManagerRegistry $doctrine, ValidatorInterface $validator): Response;
	public function update(int $id, Request $request, ManagerRegistry $doctrine, ValidatorInterface $validator): Response;
	public function delete(int $id, ManagerRegistry $doctrine): Response;
}