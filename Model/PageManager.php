<?php
namespace Kitpages\CmsBundle\Model;

use Kitpages\CmsBundle\Entity\Page;
use Kitpages\CmsBundle\Entity\PagePublish;
use Kitpages\CmsBundle\Entity\Zone;
use Kitpages\CmsBundle\Entity\Block;
use Kitpages\CmsBundle\Entity\ZonePublish;
use Kitpages\CmsBundle\Entity\ZoneBlock;
use Kitpages\CmsBundle\Event\PageEvent;
use Kitpages\CmsBundle\KitpagesCmsEvents;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Bundle\DoctrineBundle\Registry;
use Symfony\Component\HttpKernel\Log\LoggerInterface;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;

class PageManager
{
    ////
    // dependency injection
    ////
    protected $dispatcher = null;
    protected $doctrine = null;
    protected $templating = null;
    protected $zoneManager = null;
    protected $logger = null;
    
    public function __construct(
        Registry $doctrine,
        EventDispatcher $dispatcher,
        $templating,
        ZoneManager $zoneManager,
        LoggerInterface $logger
    )
    {
        $this->dispatcher = $dispatcher;
        $this->doctrine = $doctrine;
        $this->templating = $templating;
        $this->zoneManager = $zoneManager;        
        $this->logger = $logger;
    }      

    /**
     * @return EventDispatcher $dispatcher
     */
    public function getDispatcher() {
        return $this->dispatcher;
    }  
    
    /**
     * @return $templating
     */
    public function getTemplating() {
        return $this->templating;
    }    
    
    /**
     * @return Registry $doctrine
     */
    public function getDoctrine() {
        return $this->doctrine;
    }    

    /**
     * @return $zoneManager
     */
    public function getZoneManager() {
        return $this->zoneManager;
    }
    
    /**
     * @return LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }

    ////
    // actions
    ////
    
    public function delete(Page $page)
    {
        // throw on event
        $event = new PageEvent($page);
        $this->getDispatcher()->dispatch(KitpagesCmsEvents::onPageDelete, $event);

        // preventable action
        if (!$event->isDefaultPrevented()) {
            $em = $this->getDoctrine()->getEntityManager();
            $zoneManager = $this->getZoneManager();
            foreach($em->getRepository('KitpagesCmsBundle:Zone')->findByPage($page) as $zone){
                $nbr = $em->getRepository('KitpagesCmsBundle:PageZone')->nbrPageZoneByZoneWithPageDiff($zone, $page);
                if ($nbr == 0) {
                    $zoneManager->delete($zone);
                }
            }
            $em->remove($page);
            $em->flush();
        }
        // throw after event
        $this->getDispatcher()->dispatch(KitpagesCmsEvents::afterPageDelete, $event);
    }
    
    public function publish(Page $page, array $listLayout, array $listRenderer)
    {
        $event = new PageEvent($page, $listLayout);
        $this->getDispatcher()->dispatch(KitpagesCmsEvents::onPagePublish, $event);
        if (! $event->isDefaultPrevented()) {
            // publish zone
            $em = $this->getDoctrine()->getEntityManager();
            foreach($em->getRepository('KitpagesCmsBundle:Zone')->findByPage($page) as $zone){
                $this->getZoneManager()->publish($zone, $listRenderer);
            }
            $em->flush();
            // remove old pagePublish
            $zonePublish = null;
            $query = $em->createQuery("
                SELECT pp FROM KitpagesCmsBundle:PagePublish pp
                WHERE pp.page = :page
            ")->setParameter('page', $page);
            $pagePublishList = $query->getResult();
            if (count($pagePublishList) == 1) {
                $pagePublish = $pagePublishList[0];
                $em->remove($pagePublish);
                $em->flush();
            }

            $zoneList = array();
            // create page publish
            foreach($em->getRepository('KitpagesCmsBundle:Zone')->findByPage($page) as $zone){
                $zoneList[] = $zone->getId();
            }
            $pagePublishNew = new PagePublish();
            $pagePublishNew->initByPage($page);
            $pagePublishNew->setZoneList(array("blockList"=>$zoneList));
            $page->setIsPublished(true);
            $page->setPagePublish($pagePublishNew);
            $em->flush();
        }
        $event = new PageEvent($page, $listLayout, $listRenderer);
        $this->getDispatcher()->dispatch(KitpagesCmsEvents::afterPagePublish, $event);
    }

    public function afterModify($page, $oldPageData)
    {
        if ($oldPageData != $page->getData()) {
            $page->setRealUpdatedAt(new \DateTime());
            $em = $this->getDoctrine()->getEntityManager();
            $em->flush();
            $this->unpublish($page);
            $event = new PageEvent($page);
            $this->getDispatcher()->dispatch(KitpagesCmsEvents::afterPageModify, $event);
        }
    }

    public function unpublish($page){
        $event = new PageEvent($page);
        $this->getDispatcher()->dispatch(KitpagesCmsEvents::onPageUnpublish, $event);  
        $em = $this->getDoctrine()->getEntityManager();
        $page->setIsPublished(false);
        $em->flush();        
        $event = new PageEvent($page);
        $this->getDispatcher()->dispatch(KitpagesCmsEvents::afterPageUnpublish, $event);
    }
    
    ////
    // event listener
    ////
    public function afterZoneUnpublish(Event $event)
    {
        $zone = $event->getZone();
        $em = $this->getDoctrine()->getEntityManager(); 
        foreach($em->getRepository('KitpagesCmsBundle:Page')->findByZone($zone) as $page) {
            $this->unpublish($page);
        }
    }


    
}
