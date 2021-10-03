<?php

declare(strict_types=1);

namespace App\Mapping;

use App\Exception\LogicException;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class EntityValidator
{
	private ValidatorInterface $validator;

	public function __construct(ValidatorInterface $validator)
	{
		$this->validator = $validator;
	}

	public function validate(object $entity): void
	{
		/** @var ConstraintViolationInterface[] $violations */
		$violations = $this->validator->validate($entity);

		if (count($violations) > 0) {
			$fields = [];
			foreach ($violations as $violation) {
				$fields[$violation->getPropertyPath()][] = $violation->getMessage();
			}

			throw new LogicException();
		}
	}
}
