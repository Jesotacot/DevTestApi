<?php

namespace TicketShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Ticket
 *
 * @ORM\Table(name="ticket")
 * @ORM\Entity(repositoryClass="TicketShopBundle\Repository\TicketRepository")
 */
class Ticket
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=255)
     */
    private $code;

    /**
     * @var int
     *
     * @ORM\Column(name="quantity", type="integer")
     */
    private $quantity;

    /**
     * @var int
     *
     * @ORM\Column(name="ordernum", type="integer")
     */
    private $ordernum;

    /**
     * @var string
     *
     * @ORM\Column(name="orderuuid", type="string", length=255)
     */
    private $orderuuid;

    /**
     * @var string
     *
     * @ORM\Column(name="orderlineuuid", type="string", length=255)
     */
    private $orderlineuuid;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateofuse", type="datetime")
     */
    private $dateofuse;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Ticket
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set code
     *
     * @param string $code
     *
     * @return Ticket
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set quantity
     *
     * @param integer $quantity
     *
     * @return Ticket
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set ordernum
     *
     * @param integer $ordernum
     *
     * @return Ticket
     */
    public function setOrdernum($ordernum)
    {
        $this->ordernum = $ordernum;

        return $this;
    }

    /**
     * Get ordernum
     *
     * @return int
     */
    public function getOrdernum()
    {
        return $this->ordernum;
    }

    /**
     * Set orderuuid
     *
     * @param string $orderuuid
     *
     * @return Ticket
     */
    public function setOrderuuid($orderuuid)
    {
        $this->orderuuid = $orderuuid;

        return $this;
    }

    /**
     * Get orderuuid
     *
     * @return string
     */
    public function getOrderuuid()
    {
        return $this->orderuuid;
    }

    /**
     * Set orderlineuuid
     *
     * @param string $orderlineuuid
     *
     * @return Ticket
     */
    public function setOrderlineuuid($orderlineuuid)
    {
        $this->orderlineuuid = $orderlineuuid;

        return $this;
    }

    /**
     * Get orderlineuuid
     *
     * @return string
     */
    public function getOrderlineuuid()
    {
        return $this->orderlineuuid;
    }

    /**
     * Set dateofuse
     *
     * @param \DateTime $dateofuse
     *
     * @return Ticket
     */
    public function setDateofuse($dateofuse)
    {
        $this->dateofuse = $dateofuse;

        return $this;
    }

    /**
     * Get dateofuse
     *
     * @return \DateTime
     */
    public function getDateofuse()
    {
        return $this->dateofuse;
    }
}
