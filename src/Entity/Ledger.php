<?php


namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LedgerRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Ledger
{
    const CASH_PAYMENT = "Cash Payment";
    const CARD_PAYMENT = "Coupon Payment";
    const AUTO_CHARGE  = "System Charged";
    const RFID_CHARGE = "RFID Charged";

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=60)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $debit;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $credit;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="ledger_entries")
     */
    private $user;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description): void
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getDebit()
    {
        return $this->debit;
    }

    /**
     * @param mixed $debit
     */
    public function setDebit($debit): void
    {
        $this->debit = $debit;
    }

    /**
     * @return mixed
     */
    public function getCredit()
    {
        return $this->credit;
    }

    /**
     * @param mixed $credit
     */
    public function setCredit($credit): void
    {
        $this->credit = $credit;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * @param mixed $created_at
     */
    public function setCreatedAt($created_at): void
    {
        $this->created_at = $created_at;
    }

    /**
     * @ORM\PrePersist
     */
    public function setCreatedAtValue()
    {
        $this->created_at = new \DateTime();
    }

    /**
     * @return mixed
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     *
     * @return Ledger
     */
    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

}