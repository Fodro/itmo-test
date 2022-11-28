<?php
namespace App\Service;
use JMS\Serializer\Serializer;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class BasicService {
	protected function serializeObj($obj, Serializer $serializer): string | null{
		if ($obj === null){
			return null;
		}
		return $serializer->serialize($obj, 'json');
	}
	protected function validateObj($obj, ValidatorInterface $validator): bool {
		$errors = $validator->validate($obj);
		if (count($errors) > 0){
			return False;
		}
		return True;
	}
}