<?php

namespace App\MainBundle\Service;

use App\MainBundle\Entity\AdmitadAction;
use App\MainBundle\Entity\AdmitadActionRepository;
use App\MainBundle\Entity\CampaignRepository;
use App\MainBundle\Entity\CategoryRepository;
use App\MainBundle\Entity\ClickRepository;
use App\MainBundle\Entity\PaymentRepository;
use App\MainBundle\Entity\PlatformRepository;
use App\MainBundle\Entity\PlatformTypeRepository;
use App\MainBundle\Entity\RateRepository;
use App\MainBundle\Entity\RegionRepository;
use App\MainBundle\Entity\StatisticRepository;
use App\MainBundle\Entity\UserRepository;
use App\MainBundle\Entity\UserStatisticRepository;
use App\MainBundle\Service\ContainerAwareTrait;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;

trait DoctrineTrait
{
    use ContainerAwareTrait;

    /**
     * @return Registry
     */
    public function getDoctrine()
    {
        return $this->getContainer()->get('doctrine');
    }

    /**
     * @param null $name
     * @return EntityManager
     */
    public function getEntityManager($name = null)
    {
        return $this->getDoctrine()->getManager($name);
    }

    /**
     * @param $name
     * @return \Doctrine\Common\Persistence\ObjectRepository
     */
    public function getRepository($name)
    {
        return $this->getDoctrine()->getRepository('MainBundle:' . $name);
    }

    /**
     * @return UserRepository
     */
    public function getUserRepository()
    {
        return $this->getRepository('User');
    }
}