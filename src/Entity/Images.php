<?php

namespace App\Entity;

use App\Repository\ImagesRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=ImagesRepository::class)
 * @Vich\Uploadable()
 */
class Images
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;


    public function getId(): ?int
    {
        return $this->id;
    }

    //D ICI

    /**
     * @var string|null
     * @ORM\Column(type="string", length=255)
     */
    private $image;

    /**
     * @var File|null
     * @Assert\Image(mimeTypes={"image/jpeg", "image/jpg", "image/png", "image/gif" })
     * @Vich\UploadableField(mapping="upload", fileNameProperty="image")
     *
     */
    private $imageFile;

    public function getImage(): ?string
    {
        return $this->image;
    }


    public function setImage(?string $image)
    {
        $this->image = $image;
        return $this;
    }


    /**
     * @ORM\Column(type="datetime")
     * @var null|DateTime
     */
    private $updated_at;

    /**
     * @ORM\OneToMany(targetEntity=Article::class, mappedBy="image")
     */
    private $articles;

    public function __construct()
    {
        $this->articles = new ArrayCollection();
    }


    public function getImageFile()
    {
        return $this->imageFile;
    }



    public function setImageFile( ?File $imageFile ): void {
        $this->imageFile = $imageFile;
        if($this->imageFile instanceof UploadedFile){
            $this->updated_at = new \DateTime('now');
        }
        //return $this;
    }

    // A LA !

    /**
     * @return Collection|Article[]
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    public function addArticle(Article $article): self
    {
        if (!$this->articles->contains($article)) {
            $this->articles[] = $article;
            $article->setImage($this);
        }

        return $this;
    }

    public function removeArticle(Article $article): self
    {
        if ($this->articles->removeElement($article)) {
            // set the owning side to null (unless already changed)
            if ($article->getImage() === $this) {
                $article->setImage(null);
            }
        }

        return $this;
    }


}
