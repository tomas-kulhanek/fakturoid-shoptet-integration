<?php

declare(strict_types=1);

namespace App\Mapping;

use App\Exception\LogicException;
use JMS\Serializer\Serializer;

class EntityMapping
{
	public function __construct(
		private EntityValidator $validator,
		private Serializer      $serializer
	) {
	}

	/**
	 * @template T of object
	 * @param string $data
	 * @param string $className
	 * @return object
	 * @phpstan-param class-string<T> $className
	 * @phpstan-return T
	 */
	public function createEntity(string $data, string $className): object
	{
		$object = $this->serializer->deserialize($data, $className, 'json');
		$this->validator->validate($object);
		return $object;
	}

	public function serialize(mixed $data): string
	{
		try {
			$this->validator->validate($data);
			return $this->serializer->serialize($data, 'json');
		} catch (LogicException) {
			throw new LogicException();
		}
	}
}
