<?php

namespace Admin\Entity\Repository;

use Database\Entity\Repository\TranslatableRepository;

class NewsRepository extends TranslatableRepository
{
	const TRANSLATION_CLASS = 'Admin\Entity\Translation\NewsTranslation';
}