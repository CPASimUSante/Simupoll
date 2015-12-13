<?php
namespace CPASimUSante\SimupollBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as EXT;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class QuestionController
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
 *      name    = "cpasimusante_question",
 * )
 */
class QuestionController extends Controller
{
    /**
     * To duplicate a question
     *
     * @EXT\Route(
     *      "/duplicate_question/{qid}",
     *      name="cpasimusante_question_duplicate",
     *      requirements={"qid" = "\d+"},
     *      options={"expose"=true}
     * )
     * @EXT\Template("CPASimUSanteSimupollBundle:Question:edit.html.twig")
     *
     * @access public
     *
     * @param integer $qid id Question
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function duplicateAction ($qid)
    {
        $exercise = null;
        $interaction = $this->getDoctrine()
            ->getManager()
            ->getRepository('UJMExoBundle:Interaction')
            ->find($qid);
        $service = $this->container->get('ujm.exercise_services');

        $em = $this->getDoctrine()->getManager();
        $question = $service->controlUserQuestion($interaction->getQuestion()->getId(), $this->container, $em);
        $sharedQuestion = $service->controlUserSharedQuestion($qid);

        $allowToAccess = FALSE;

        if ($id != -1) {
            $exercise = $this->getDoctrine()->getManager()->getRepository('UJMExoBundle:Exercise')->find($id);

            if ($this->container->get('ujm.exercise_services')
                    ->isExerciseAdmin($exercise) === true) {
                $allowToAccess = TRUE;
            }
        }
        if (count($question) > 0 || count($sharedQuestion) > 0 || $allowToAccess === TRUE) {

            $typeInter = $interaction->getType();

            switch ($typeInter) {
                case "InteractionQCM":
                    $interactionX = $this->getDoctrine()
                        ->getManager()
                        ->getRepository('UJMExoBundle:InteractionQCM')
                        ->getInteractionQCM($interaction->getId());

                    $interXHandler = new \UJM\ExoBundle\Form\InteractionQCMHandler(
                        NULL , NULL, $this->getDoctrine()->getManager(),
                        $this->container->get('ujm.exercise_services'),
                        $this->container->get('security.token_storage')->getToken()->getUser(), $exercise,
                        $this->get('translator')
                    );

                    break;


                case "InteractionGraphic":
                    $interactionX = $this->getDoctrine()
                        ->getManager()
                        ->getRepository('UJMExoBundle:InteractionGraphic')
                        ->getInteractionGraphic($interaction->getId());

                    $interXHandler = new \UJM\ExoBundle\Form\InteractionGraphicHandler(
                        NULL , NULL, $this->getDoctrine()->getManager(),
                        $this->container->get('ujm.exercise_services'),
                        $this->container->get('security.token_storage')->getToken()->getUser(), $exercise,
                        $this->get('translator')
                    );

                    break;


                case "InteractionHole":
                    $interactionX = $this->getDoctrine()
                        ->getManager()
                        ->getRepository('UJMExoBundle:InteractionHole')
                        ->getInteractionHole($interaction->getId());

                    $interXHandler = new \UJM\ExoBundle\Form\InteractionHoleHandler(
                        NULL , NULL, $this->getDoctrine()->getManager(),
                        $this->container->get('ujm.exercise_services'),
                        $this->container->get('security.token_storage')->getToken()->getUser(), $exercise,
                        $this->get('translator')
                    );

                    break;


                case "InteractionOpen":
                    $interactionX = $this->getDoctrine()
                        ->getManager()
                        ->getRepository('UJMExoBundle:InteractionOpen')
                        ->getInteractionOpen($interaction->getId());

                    $interXHandler = new \UJM\ExoBundle\Form\InteractionOpenHandler(
                        NULL , NULL, $this->getDoctrine()->getManager(),
                        $this->container->get('ujm.exercise_services'),
                        $this->container->get('security.token_storage')->getToken()->getUser(), $exercise,
                        $this->get('translator')
                    );

                    break;

                case "InteractionMatching":
                    $interactionX = $this->getDoctrine()
                        ->getManager()
                        ->getRepository('UJMExoBundle:InteractionMatching')
                        ->getInteractionMatching($interaction->getId());

                    $interXHandler = new \UJM\ExoBundle\Form\InteractionMatchingHandler(
                        NULL , NULL, $this->getDoctrine()->getManager(),
                        $this->container->get('ujm.exercise_services'),
                        $this->container->get('security.token_storage')->getToken()->getUser(), $exercise,
                        $this->get('translator')
                    );

                    break;
            }

            $interXHandler->singleDuplicateInter($interactionX[0]);

            $categoryToFind = $interactionX[0]->getInteraction()->getQuestion()->getCategory();
            $titleToFind = $interactionX[0]->getInteraction()->getQuestion()->getTitle();

            if ($id == -1) {
                return $this->redirect(
                    $this->generateUrl('ujm_question_index', array(
                            'categoryToFind' => base64_encode($categoryToFind), 'titleToFind' => base64_encode($titleToFind))
                    )
                );
            } else {
                return $this->redirect(
                    $this->generateUrl('ujm_exercise_questions', array(
                            'id' => $id, 'categoryToFind' => $categoryToFind, 'titleToFind' => $titleToFind)
                    )
                );
            }
        } else {
            return $this->redirect($this->generateUrl('ujm_question_index'));
        }

    }
}