<?php
namespace App\Service;
use Doctrine\Persistence\ManagerRegistry;
use JMS\Serializer\SerializerBuilder;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class BasicService {
	var $doctrine, $serializer, $validator, $entityManager;
	function __construct(ManagerRegistry $doctrine, ValidatorInterface $validator){
		$this->doctrine = $doctrine;
		$this->serializer = SerializerBuilder::create()->build();
		$this->validator = $validator;
		$this->entityManager = $doctrine->getManager();
	}
	protected function serializeObj($obj): string | null{
		if ($obj === null){
			return null;
		}
		return $this->serializer->serialize($obj, 'json');
	}
	protected function validateObj($obj): bool {
		$errors = $this->validator->validate($obj);
		if (count($errors) > 0){
			return False;
		}
		return True;
	}
}