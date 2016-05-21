<?php

namespace CPASimUSante\SimupollBundle\Listener;

use Symfony\Component\DependencyInjection\ContainerAware;
use Claroline\CoreBundle\Event\CopyResourceEvent;
use Claroline\CoreBundle\Event\CreateFormResourceEvent;
use Claroline\CoreBundle\Event\CreateResourceEvent;
use Claroline\CoreBundle\Event\OpenResourceEvent;
use Claroline\CoreBundle\Event\DeleteResourceEvent;
use CPASimUSante\SimupollBundle\Entity\Simupoll;
use CPASimUSante\SimupollBundle\Form\SimupollType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use JMS\DiExtraBundle\Annotation as DI;

/**
 * @DI\Service("cpasimusante.simupoll.simupoll_listener")
 */
class SimupollResourceListener extends ContainerAware
{
    public function onCreateForm(CreateFormResourceEvent $event)
    {
        $form = $this->container->get('form.factory')
            ->create(new SimupollType(), new Simupoll(), array('inside' => false));
        $content = $this->container->get('templating')->render(
            'CPASimUSanteSimupollBundle:Simupoll:createForm.html.twig',
            array(
                'form' => $form->createView(),
                'resourceType' => 'cpasimusante_simupoll',
            )
        );
        $event->setResponseContent($content);
        $event->stopPropagation();
    }

    public function onCreate(CreateResourceEvent $event)
    {
        $request = $this->container->get('request');
        $form = $this->container->get('form.factory')
            ->create(new SimupollType(), new Simupoll(), array('inside' => false));
        $form->handleRequest($request);
        if ($form->isValid()) {
            $simupoll = $form->getData();
            //update name
            $simupoll->setName($simupoll->getTitle());

            $event->setResources(array($simupoll));
            $event->stopPropagation();
        }
        $content = $this->container->get('templating')->render(
            'CPASimUSanteSimupollBundle:Simupoll:createForm.html.twig',
            array(
                'form' => $form->createView(),
                'resourceType' => $event->getResourceType(),
            )
        );
        $event->setErrorFormContent($content);
        $event->stopPropagation();
    }

    public function onDelete(DeleteResourceEvent $event)
    {
        $event->stopPropagation();
    }

    public function onCopy(CopyResourceEvent $event)
    {
        $simupoll = $event->getResource();
        $loggedUser = $this->container->get('security.token_storage')
            ->getToken()->getUser();
        $newSimupoll = null;
        $newSimupoll = $this->container->get('cpasimusante.simupoll.simupoll_manager')
            ->copySimupoll($simupoll, $loggedUser);
        $event->setCopy($newSimupoll);
        $event->stopPropagation();
    }

    public function onOpen(OpenResourceEvent $event)
    {
        $route = $this->container
            ->get('router')
            ->generate(
                'cpasimusante_simupoll_open',
                array(
                    'id' => $event->getResource()->getId(),
                )
            );
        $event->setResponse(new RedirectResponse($route));
        $event->stopPropagation();
    }
}
