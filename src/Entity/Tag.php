<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TagRepository")
 */
class Tag
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\TagCloud", mappedBy="tag")
     */
    private $tagClouds;

    public function __construct()
    {
        $this->tagClouds = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|TagCloud[]
     */
    public function getTagClouds(): Collection
    {
        return $this->tagClouds;
    }

    public function addTagCloud(TagCloud $tagCloud): self
    {
        if (!$this->tagClouds->contains($tagCloud)) {
            $this->tagClouds[] = $tagCloud;
            $tagCloud->setTag($this);
        }

        return $this;
    }

    public function removeTagCloud(TagCloud $tagCloud): self
    {
        if ($this->tagClouds->contains($tagCloud)) {
            $this->tagClouds->removeElement($tagCloud);
            // set the owning side to null (unless already changed)
            if ($tagCloud->getTag() === $this) {
                $tagCloud->setTag(null);
            }
        }

        return $this;
    }
    public function __toString() {
        return $this->name;
    }
}
