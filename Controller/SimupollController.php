<?php
namespace CPASimUSante\SimupollBundle\Controller;

use Claroline\CoreBundle\Library\Resource\ResourceCollection;
use CPASimUSante\SimupollBundle\Entity\Simupoll;
use CPASimUSante\SimupollBundle\Form\CategoryType;
use CPASimUSante\SimupollBundle\Form\SimupollType;
use Doctrine\Common\Collections\ArrayCollection;
use CPASimUSante\SimupollBundle\Tag\RecursiveTagIterator;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as EXT;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Class SimupollController
 *
 * @category   Controller
 * @package    CPASimUSante
 * @subpackage Simupoll
 * @author     CPASimUSante <contact@simusante.com>
 * @copyright  2015 CPASimUSante
 * @license    http://www.opensource.org/licenses/mit-license.php MIT License
 * @version    0.1
 * @link       http://simusante.com
 *
 * @EXT\Route(
 *      "/",
 *      name    = "cpasimusante_simupoll",
 *      service = "cpasimusante_simupoll.controller.simupoll"
 * )
 */
class SimupollController extends Controller
{
    /**
     *
     * @EXT\Route("/edit/{id}", name="cpasimusante_editsimupoll", requirements={"id" = "\d+"}, options={"expose"=true})
     * @EXT\ParamConverter("simupoll", class="CPASimUSanteSimupollBundle:Simupoll", options={"id" = "id"})
     * @EXT\Template("CPASimUSanteSimupollBundle:Simupoll:edit.html.twig")
     * @param Request $request
     * @param Simupoll $simupoll
     * @return array
     */
    public function editAction(Request $request, Simupoll $simupoll)
    {
        //can user access ?
        $this->checkAccess($simupoll);

        //can user edit ?
        $simupollAdmin = $this->container
            ->get('cpasimusante_simupoll.services.simupoll')
            ->isSimupollAdmin($simupoll);

        if ($simupollAdmin === true)
        {
            $em = $this->getDoctrine()->getManager();

            // Create an array of the current Question objects in the database
            $originalQuestions = array();
            foreach ($simupoll->getQuestions() as $question) {
                $originalQuestions[] = $question;
            }

            $form = $this->get('form.factory')
                ->create(new SimupollType(), $simupoll);

            $form->handleRequest($request);
            if ($form->isValid()) {
/*
                // filter $originalQuestions to contain question no longer present
                foreach ($simupoll->getQuestions() as $question) {
                    foreach ($originalQuestions as $key => $toDel) {
                        if ($toDel->getId() === $question->getId()) {
                            unset($originalQuestions[$key]);
                        }
                    }
                }
*/
                // remove the relationship between the question and the Simupoll
                foreach ($originalQuestions as $question) {
                    if (false === $simupoll->getQuestions()->contains($question)) {
                        // remove the Simupoll from the question
                        //$question->getSimupoll()->removeElement($simupoll);

                        // in a a ManyToOne relationship, remove the relationship
                        $question->setSimupoll(null);
                        $em->persist($question);

                        // to delete the question entirely, you can also do that
                        $em->remove($question);
                    }
                }

                $em->persist($simupoll);
                $em->flush();
                $this->get('session')->getFlashBag()->add('info', 'Simupoll mis à jour');
            }
            return array(
                '_resource'     => $simupoll,
                'form'          => $form->createView(),
            );
        }
        //If not admin, open
        else
        {
            return $this->redirect($this->generateUrl('cpasimusante_simupoll_open', array('simupollId' => $simupoll->getId())));
        }
    }

    /**
     *
     * @EXT\Route("/open/{id}", name="cpasimusante_opensimupoll", requirements={"id" = "\d+"}, options={"expose"=true})
     * @EXT\ParamConverter("simupoll", class="CPASimUSanteSimupollBundle:Simupoll", options={"id" = "id"})
     * @EXT\Template("CPASimUSanteSimupollBundle:Simupoll:open.html.twig")
     * @param Simupoll $simupoll
     * @return array
     */
    public function openAction($simupoll)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $this->container->get('security.token_storage')
            ->getToken()->getUser();

        //can user access ?
        $this->checkAccess($simupoll);

        //can user edit ?
        $simupollAdmin = $this->container
            ->get('cpasimusante_simupoll.services.simupoll')
            ->isSimupollAdmin($simupoll);

        //can user manage exercise
        $allowToCompose = 0;

        if (is_object($user) && ($simupollAdmin === true) )
        {
            $allowToCompose = 1;
        }

      //  $nbQuestions = $em->getRepository('CPASimUSanteSimupollBundle:SimupollGroupQuestion')->getCountQuestion($simupoll->getId());

        return array(
            '_resource'         => $simupoll,
            'allowToCompose'    => $allowToCompose,
           // 'nbQuestion'        => $nbQuestions['nbq'],
        );
    }

    /**
     * Manage Categories entity
     *
     * @EXT\Route(
     *      "/managecategories",
     *      name="cpasimusante_managecategories",
     *      requirements={},
     *      options={"expose"=true}
     * )
     * @EXT\Template("CPASimUSanteSimupollBundle:Simupoll:categories.html.twig")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function manageCategoriesAction()
    {

    }

    /**
     * Manage Categories entity
     *
     * @EXT\Route(
     *      "/managetags",
     *      name="cpasimusante_managetags",
     *      requirements={},
     *      options={"expose"=true}
     * )
     * @EXT\Template("CPASimUSanteSimupollBundle::tag.html.twig")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function manageTagsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $rootTags = $em->getRepository("CPASimUSanteSimupollBundle:Tag")
            ->findBy(
                array(
                    'parent' => null,
                    'user'=>$user->getId()
                ),
                array('parent' => 'ASC')
            );

        $collection = new ArrayCollection($rootTags);
        $tag_iterator = new RecursiveTagIterator($collection);
        $recursive_iterator = new \RecursiveIteratorIterator($tag_iterator, \RecursiveIteratorIterator::SELF_FIRST);

        $select = '';
        foreach ($recursive_iterator as $index => $child_tag)
        {
            $select .= '<option value="' . $child_tag->getId() . '">' . str_repeat('&nbsp;&nbsp;', $recursive_iterator->getDepth()) . $child_tag->getName() . '</option>';
        }

        return array(
            'tags' => $recursive_iterator,
            'select' => $select
        );
    }
    /**
     * Finds and displays a Question entity to this Simupoll
     *
     * @EXT\Route(
     *      "/managequestions/{id}/{pageNow}/{displayAll}/{categoryToFind}/{titleToFind}",
     *      name="cpasimusante_managequestions",
     *      defaults={ "pageNow" = 0, "categoryToFind" = "z", "titleToFind" = "z", "displayAll" = 0 },
     *      requirements={"id" = "\d+", "categoryToFind" =".+", "titleToFind" = ".+"},
     *      options={"expose"=true}
     * )
     * @EXT\Template("CPASimUSanteSimupollBundle:Simupoll:questions.html.twig")
     *
     * @access public
     *
     * @param integer $id id of Simupoll
     * @param integer $pageNow actual page for the pagination
     * @param string $categoryToFind used for pagination (for example after creating a question, go back to page contaning this question)
     * @param string $titleToFind used for pagination (for example after creating a question, go back to page contaning this question)
     * @param boolean $displayAll to use pagination or not
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showQuestionsAction($id, $pageNow, $categoryToFind, $titleToFind, $displayAll)
    {
        $user = $this->container->get('security.token_storage')
            ->getToken()->getUser();
        $allowEdit = array();
        $em = $this->getDoctrine()->getManager();
        $exercise = $em->getRepository('UJMExoBundle:Exercise')->find($id);
        $this->checkAccess($exercise);

        $workspace = $exercise->getResourceNode()->getWorkspace();

        $exoAdmin = $this->container->get('ujm.exercise_services')->isExerciseAdmin($exercise);

        $max = 10; // Max Per Page
        $request = $this->get('request');
        $page = $request->query->get('page', 1);

        if ($exoAdmin === true) {
            $interactions = $this->getDoctrine()
                ->getManager()
                ->getRepository('UJMExoBundle:Interaction')
                ->getExerciseInteraction($em, $id, 0);

            if ($displayAll == 1) {
                $max = count($interactions);
            }

            $questionWithResponse = array();
            foreach ($interactions as $interaction) {
                $response = $em->getRepository('UJMExoBundle:Response')
                    ->findBy(array('interaction' => $interaction->getId()));
                if (count($response) > 0) {
                    $questionWithResponse[$interaction->getId()] = 1;
                } else {
                    $questionWithResponse[$interaction->getId()] = 0;
                }

                $share = $this->container->get('ujm.exercise_services')->controlUserSharedQuestion(
                    $interaction->getQuestion()->getId());

                if ($user->getId() == $interaction->getQuestion()->getUser()->getId()) {
                    $allowEdit[$interaction->getId()] = 1;
                } else if(count($share) > 0) {
                    $allowEdit[$interaction->getId()] = $share[0]->getAllowToModify();
                } else {
                    $allowEdit[$interaction->getId()] = 0;
                }

            }

            if ($categoryToFind != '' && $titleToFind != '' && $categoryToFind != 'z' && $titleToFind != 'z') {
                $i = 1 ;
                $pos = 0 ;
                $temp = 0;

                foreach ($interactions as $interaction) {
                    if ($interaction->getQuestion()->getCategory() == $categoryToFind) {
                        $temp = $i;
                    }
                    if ($interaction->getQuestion()->getTitle() == $titleToFind && $temp == $i) {
                        $pos = $i;
                        break;
                    }
                    $i++;
                }

                if ($pos % $max == 0) {
                    $pageNow = $pos / $max;
                } else {
                    $pageNow = ceil($pos / $max);
                }
            }

            $pagination = $this->paginationWithIf($interactions, $max, $page, $pageNow);

            $interactionsPager = $pagination[0];
            $pagerQuestion = $pagination[1];

            return $this->render(
                'UJMExoBundle:Question:exerciseQuestion.html.twig',
                array(
                    'workspace'            => $workspace,
                    'interactions'         => $interactionsPager,
                    'exerciseID'           => $id,
                    'questionWithResponse' => $questionWithResponse,
                    'pagerQuestion'        => $pagerQuestion,
                    'displayAll'           => $displayAll,
                    'allowEdit'            => $allowEdit,
                    '_resource'            => $exercise
                )
            );

        } else {
            return $this->redirect($this->generateUrl('ujm_exercise_open', array('exerciseId' => $id)));
        }
    }

    /**
     * To check the right to open or not
     *
     * @access private
     *
     * @param \CPASimUSante\SimupollBundle\Entity\Simupoll $simupoll
     *
     * @return exception
     */
    private function checkAccess($simupoll)
    {
        $collection = new ResourceCollection(array($simupoll->getResourceNode()));
        if (!$this->get('security.authorization_checker')->isGranted('OPEN', $collection)) {
            throw new AccessDeniedException($collection->getErrorsForDisplay());
        }
    }
}