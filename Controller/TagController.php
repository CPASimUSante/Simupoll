<?php

namespace CPASimUSante\SimupollBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Doctrine\Common\Collections\ArrayCollection;
use CPASimUSante\SimupollBundle\Tag\RecursiveTagIterator;

use CPASimUSante\SimupollBundle\Entity\Tag;
use CPASimUSante\SimupollBundle\Form\TagType;

/**
 * Tag controller for CRUD
 *
 */
class TagController extends Controller
{
    /**
     * Lists all Tag entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        //display only the tags for this user
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $tags = $em->getRepository('CPASimUSanteSimupollBundle:Tag')
            ->findBy(
                array(
                    'user' => $user->getId()
                ),
                array('parent' => 'ASC')
            );
        $rootTags = $em->getRepository('CPASimUSanteSimupollBundle:Tag')
            ->findBy(
                array(
                    'parent' => null,
                    'user' => $user->getId()
                ),
                array('parent' => 'ASC')
            );
        $collection = new ArrayCollection($rootTags);
        $tag_iterator = new RecursiveTagIterator($collection);
        $recursive_iterator = new \RecursiveIteratorIterator($tag_iterator, \RecursiveIteratorIterator::SELF_FIRST);

        $arr_tag = array();
        foreach ($recursive_iterator as $index => $child_tag)
        {
            if ($child_tag->getUser() != null)
            {
                $parent = $child_tag->getParent();
                $parentname = (isset($parent)) ? $child_tag->getParent()->getName() : null;
                $arr_tag[] = array(
                    'name' => str_repeat('--', $recursive_iterator->getDepth()) . $child_tag->getName(),
                    'parent' => $parentname,
                    'id' => $child_tag->getId()
                );
            }
        }

        return $this->render('CPASimUSanteSimupollBundle:Tag:index.html.twig', array(
            'tags' => $tags,
            'tags2' => $recursive_iterator,
            'arr_tag' => $arr_tag
        ));
    }

    /**
     * Creates a new Tag entity.
     *
     * @param Request $request
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request)
    {
        $entity = new Tag();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $tag = $form->getData();

            //Set user
            $user = $this->get('security.token_storage')->getToken()->getUser();
            $tag->setUser($user);

            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('tag'));
        }

        return $this->render('CPASimUSanteSimupollBundle:Tag:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Tag entity.
     *
     * @param Tag $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Tag $entity)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $form = $this->createForm(new TagType($user->getId()), $entity, array(
            'action' => $this->generateUrl('tag_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array(
            'label' => 'Create',
            'attr'=> array('class' => 'btn btn-primary')
        ));

        return $form;
    }

    /**
     * Displays a form to create a new Tag entity.
     *
     */
    public function newAction()
    {
        $entity = new Tag();
        $form   = $this->createCreateForm($entity);

        return $this->render('CPASimUSanteSimupollBundle:Tag:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Tag entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CPASimUSanteSimupollBundle:Tag')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Tag entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('CPASimUSanteSimupollBundle:Tag:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Tag entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CPASimUSanteSimupollBundle:Tag')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Tag entity.');
        }

        $user = $this->get('security.token_storage')->getToken()->getUser();
        if ($entity->getUser()->getId() == $user->getId())
        {
            $editForm = $this->createEditForm($entity);

            return $this->render('CPASimUSanteSimupollBundle:Tag:edit.html.twig', array(
                'edit_form'   => $editForm->createView(),
                'tid'         => $id,
            ));
        }
        //prevent editing other user tags
        else
        {
            return $this->render('CPASimUSanteSimupollBundle:Tag:edit.html.twig', array(
                'edit_form'   => ''
            ));
        }
    }

    /**
    * Creates a form to edit a Tag entity.
    *
    * @param Tag $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Tag $entity)
    {
        //send user parameter to the form
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $form = $this->createForm(new TagType($user->getId()), $entity, array(
            'action' => $this->generateUrl('tag_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array(
            'label' => 'Update',
            'attr'=> array('class' => 'btn btn-primary')
        ));

        return $form;
    }
    /**
     * Edits an existing Tag entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CPASimUSanteSimupollBundle:Tag')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Tag entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('tag'));
        }

        return $this->render('CPASimUSanteSimupollBundle:Tag:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Tag entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('CPASimUSanteSimupollBundle:Tag')->find($id);
        $user = $this->get('security.token_storage')->getToken()->getUser();

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Tag entity.');
        }

        if ($entity->getUser()->getId() == $user->getId())
        {
            $em->remove($entity);
            $em->flush();
            return $this->redirect($this->generateUrl('tag'));
        }
       else
       {
           throw $this->createNotFoundException('You can\'t delete this element');
       }
    }

    /**
     * Creates a form to delete a Tag entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('tag_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array(
                'label' => 'Delete',
                'attr'=> array('class' => 'btn btn-primary')
            ))
            ->getForm()
        ;
    }
}
