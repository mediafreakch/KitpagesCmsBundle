<?php

namespace Kitpages\CmsBundle\Entity;
use Gedmo\Sluggable\Util\Urlizer;
/**
 * Kitpages\CmsBundle\Entity\Zone
 */
class Zone
{
    /**
     * @var string $slug
     */
    private $slug;


    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var boolean $isPublished
     */
    private $isPublished = false;


    /**
     * @var datetime $publishedAt
     */
    private $publishedAt;

    /**
     * @var datetime $unpublishedAt
     */
    private $unpublishedAt;

    /**
     * @var datetime $createdAt
     */
    private $createdAt;

    /**
     * @var datetime $updatedAt
     */
    private $updatedAt;

    /**
     * @var Kitpages\CmsBundle\Entity\ZoneBlock
     */
    private $zoneBlockList;


    /**
     * Set slug
     *
     * @param string $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set createdAt
     *
     * @param datetime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * Get createdAt
     *
     * @return datetime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param datetime $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * Get updatedAt
     *
     * @return datetime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Add zoneBlockList
     *
     * @param Kitpages\CmsBundle\Entity\ZoneBlock $zoneBlockList
     */
    public function addZoneBlockList(\Kitpages\CmsBundle\Entity\ZoneBlock $zoneBlockList)
    {
        $this->zoneBlockList[] = $zoneBlockList;
    }

    /**
     * Get zoneBlockList
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getZoneBlockList()
    {
        return $this->zoneBlockList;
    }

    public function prePersist()
    {
        $this->slug = Urlizer::transliterate($this->slug, '-');
    }

    public function preUpdate()
    {
        $this->slug = Urlizer::transliterate($this->slug, '-');
    }
    /**
     * @var Kitpages\CmsBundle\Entity\ZonePublish
     */
    private $zonePublish;


    /**
     * Set zonePublish
     *
     * @param Kitpages\CmsBundle\Entity\ZonePublish $zonePublish
     */
    public function setZonePublish(\Kitpages\CmsBundle\Entity\ZonePublish $zonePublish)
    {
        $this->zonePublish = $zonePublish;
    }

    /**
     * Get zonePublish
     *
     * @return Kitpages\CmsBundle\Entity\ZonePublish
     */
    public function getZonePublish()
    {
        return $this->zonePublish;
    }

    public function defaultSlug(){
        $this->setSlug('zone_'.$this->getId());
    }


    /**
     * Set isPublished
     *
     * @param boolean $isPublished
     */
    public function setIsPublished($isPublished)
    {
        $this->isPublished = $isPublished;
    }

    /**
     * Get isPublished
     *
     * @return boolean
     */
    public function getIsPublished()
    {
        return $this->isPublished;
    }

    /**
     * Set publishedAt
     *
     * @param datetime $publishedAt
     */
    public function setPublishedAt($publishedAt)
    {
        $this->publishedAt = $publishedAt;
    }

    /**
     * Get publishedAt
     *
     * @return datetime
     */
    public function getPublishedAt()
    {
        return $this->publishedAt;
    }

    /**
     * Set unpublishedAt
     *
     * @param datetime $unpublishedAt
     */
    public function setUnpublishedAt($unpublishedAt)
    {
        $this->unpublishedAt = $unpublishedAt;
    }

    /**
     * Get unpublishedAt
     *
     * @return datetime
     */
    public function getUnpublishedAt()
    {
        return $this->unpublishedAt;
    }

    /**
     * @var Kitpages\CmsBundle\Entity\PageZone
     */
    private $pageZoneList;

    public function __construct()
    {
        $this->pageZoneList = new \Doctrine\Common\Collections\ArrayCollection();
    $this->zoneBlockList = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add pageZoneList
     *
     * @param Kitpages\CmsBundle\Entity\PageZone $pageZoneList
     */
    public function addPageZoneList(\Kitpages\CmsBundle\Entity\PageZone $pageZoneList)
    {
        $this->pageZoneList[] = $pageZoneList;
    }

    /**
     * Get pageZoneList
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getPageZoneList()
    {
        return $this->pageZoneList;
    }

    /**
     * Add pageZoneList
     *
     * @param Kitpages\CmsBundle\Entity\PageZone $pageZoneList
     */
    public function addPageZone(\Kitpages\CmsBundle\Entity\PageZone $pageZoneList)
    {
        $this->pageZoneList[] = $pageZoneList;
    }

    /**
     * Add zoneBlockList
     *
     * @param Kitpages\CmsBundle\Entity\ZoneBlock $zoneBlockList
     */
    public function addZoneBlock(\Kitpages\CmsBundle\Entity\ZoneBlock $zoneBlockList)
    {
        $this->zoneBlockList[] = $zoneBlockList;
    }
    /**
     * @var string $canonicalUrl
     */
    private $canonicalUrl;


    /**
     * Set canonicalUrl
     *
     * @param string $canonicalUrl
     */
    public function setCanonicalUrl($canonicalUrl)
    {
        $this->canonicalUrl = $canonicalUrl;
    }

    /**
     * Get canonicalUrl
     *
     * @return string
     */
    public function getCanonicalUrl()
    {
        return $this->canonicalUrl;
    }

    public function __toString() {
        return '['.get_class($this).':'.$this->getId().':'.$this->getSlug().']';
    }

    /**
     * @var string $title
     */
    private $title;


    /**
     * Set title
     *
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
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