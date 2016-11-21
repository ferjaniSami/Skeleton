<?php

namespace Admin\Entity\Translation;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

use Database\Entity\Translation\EntityTranslation;

/**
 * @ORM\Table(name="news_translations", indexes={
 *      @ORM\Index(name="news_translation_idx", columns={"locale", "object_class", "field", "foreign_key"})
 * })
 * @ORM\Entity(repositoryClass="Database\Entity\Repository\TranslationRepository")
 */
class NewsTranslation extends EntityTranslation
{
}
