<?php

namespace Database\Entity\Repository;

use Database\Entity\Repository\AbstractTranslatableRepository;

class TranslatableRepository extends AbstractTranslatableRepository
{

	public function test($locale = null, $default = null, $empty = null)
	{
		$qb = $this->createQueryBuilder(get_class($this));
        return $this->getArrayResult($qb, $locale, $default, $empty);
	}
}