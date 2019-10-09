<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity(repositoryClass="App\Repository\RoomRepository")
 */
class Room {
	/**
	 * @ORM\Id()
	 * @ORM\GeneratedValue()
	 * @ORM\Column(type="integer")
	 */
	private $id;
	/**
	 * @ORM\Column(type="text", nullable=true)
	 */
	private $summary;
	/**
	 * @ORM\Column(type="text", nullable=true)
	 */
	private $description;
	/**
	 * @ORM\Column(type="smallint")
	 */
	private $capacity;
	/**
	 * @ORM\Column(type="float")
	 */
	private $area;
	/**
	 * @ORM\Column(type="float")
	 */
	private $price;
	/**
	 * @ORM\Column(type="string", length=400)
	 */
	private $address;
	/**
	 * @ORM\ManyToOne(targetEntity="App\Entity\Owner", inversedBy="rooms")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private $owner;
	/**
	 * @ORM\ManyToMany(targetEntity="App\Entity\Region", inversedBy="rooms")
	 */
	private $regions;
	/**
	 * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="room", orphanRemoval=true)
	 */
	private $comments;
	/**
	 * @ORM\ManyToMany(targetEntity="App\Entity\Reservation", mappedBy="rooms")
	 */
	private $reservations;
	
	public function __construct() {
		$this->regions = new ArrayCollection();
		$this->comments = new ArrayCollection();
		$this->reservations = new ArrayCollection();
	}
	
	public function getId(): ?int {
		return $this->id;
	}
	
	public function getSummary(): ?string {
		return $this->summary;
	}
	
	public function setSummary(?string $summary): self {
		$this->summary = $summary;
		return $this;
	}
	
	public function getDescription(): ?string {
		return $this->description;
	}
	
	public function setDescription(?string $description): self {
		$this->description = $description;
		return $this;
	}
	
	public function getCapacity(): ?int {
		return $this->capacity;
	}
	
	public function setCapacity(int $capacity): self {
		$this->capacity = $capacity;
		return $this;
	}
	
	public function getArea(): ?float {
		return $this->area;
	}
	
	public function setArea(float $area): self {
		$this->area = $area;
		return $this;
	}
	
	public function getPrice(): ?float {
		return $this->price;
	}
	
	public function setPrice(float $price): self {
		$this->price = $price;
		return $this;
	}
	
	public function getAddress(): ?string {
		return $this->address;
	}
	
	public function setAddress(string $address): self {
		$this->address = $address;
		return $this;
	}
	
	public function getOwner(): ?Owner {
		return $this->owner;
	}
	
	public function setOwner(?Owner $owner): self {
		$this->owner = $owner;
		return $this;
	}
	
	/**
	 * @return Collection|Region[]
	 */
	public function getRegions(): Collection {
		return $this->regions;
	}
	
	public function addRegion(Region $region): self {
		if (!$this->regions->contains($region)) {
			$this->regions[] = $region;
		}
		
		return $this;
	}
	
	public function removeRegion(Region $region): self {
		if ($this->regions->contains($region)) {
			$this->regions->removeElement($region);
		}
		
		return $this;
	}
	
	/**
	 * Makes a textual representation of the Room object containing its
	 * id, capacity in people, price in euros and address.
	 *
	 * @return String: The String representing the object.
	 */
	public function __toString(): String {
		return "$this->id: $this->capacity p for $this->price € @ $this->address";
	}
	
	/**
	 * @return Collection|Comment[]
	 */
	public function getComments(): Collection {
		return $this->comments;
	}
	
	public function addComment(Comment $comment): self {
		if (!$this->comments->contains($comment)) {
			$this->comments[] = $comment;
			$comment->setRoom($this);
		}
		
		return $this;
	}
	
	public function removeComment(Comment $comment): self {
		if ($this->comments->contains($comment)) {
			$this->comments->removeElement($comment);
			// set the owning side to null (unless already changed)
			if ($comment->getRoom() === $this) {
				$comment->setRoom(null);
			}
		}
		
		return $this;
	}
	
	/**
	 * @return Collection|Reservation[]
	 */
	public function getReservations(): Collection {
		return $this->reservations;
	}
	
	public function addReservation(Reservation $reservation): self {
		if (!$this->reservations->contains($reservation)) {
			$this->reservations[] = $reservation;
			$reservation->addRoom($this);
		}
		
		return $this;
	}
	
	public function removeReservation(Reservation $reservation): self {
		if ($this->reservations->contains($reservation)) {
			$this->reservations->removeElement($reservation);
			$reservation->removeRoom($this);
		}
		
		return $this;
	}
}
