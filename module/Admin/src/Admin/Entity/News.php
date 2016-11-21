<?php

namespace Admin\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Translatable\Translatable;

use Database\Entity\EntityTranslatable;

/**
 * News
 *
 * @ORM\Table(name="news")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\TranslationEntity(class="Admin\Entity\Translation\NewsTranslation")
 * @ORM\Entity(repositoryClass="Admin\Entity\Repository\NewsRepository")
 */
class News extends EntityTranslatable
{
    /**
     * @Gedmo\Translatable
     * @ORM\Column(type="text", length=250, nullable=true)
     */
    private $title;

    /**
     * Set title
     *
     * @param string $title
     * @return News
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }
}
