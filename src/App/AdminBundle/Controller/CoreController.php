<?php

namespace App\AdminBundle\Controller;

use App\MainBundle\Service\ContainerTrait;
use App\MainBundle\Service\DoctrineTrait;
use Knp\Component\Pager\Pagination\AbstractPagination;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

class CoreController extends Controller
{
    use ContainerTrait;
    use DoctrineTrait;

    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @return Session
     */
    public function getSession()
    {
        return $this->getContainer()->get('session');
    }

    protected function addFlashMessage($type, $message)
    {
        $this->getSession()->getFlashBag()->add($type, $message);
    }

    /**
     * @param $target
     * @param int $limit
     * @param int $page
     * @param array $options
     * @return AbstractPagination
     */
    public function paginate($target, $limit = 50, $page = 1, $options = array())
    {
        $r = $this->getRequest();

        $paginator = $this->getPaginator();

        $pagination = $paginator
            ->paginate(
                $target,
                $r->query->get('page', $r->request->get('page', $page)),
                $r->query->get('limit', $r->request->get('limit', $limit)),
                $options
            );

        $pagination->setTemplate('AdminBundle:Pagination:panel_foot.html.twig');
        $pagination->setSortableTemplate('AdminBundle:Pagination:sortable_link.html.twig');

        return $pagination;
    }
}