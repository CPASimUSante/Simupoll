<?php
namespace CPASimUSante\SimupollBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration as EXT;
use JMS\DiExtraBundle\Annotation as DI;

use CPASimUSante\SimupollBundle\Entity\Question;
use CPASimUSante\SimupollBundle\Form\QuestionType;

use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Collections\ArrayCollection;

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
 *      name    = "cpasimusante_simupoll_question",
 * )
 */
class QuestionController extends Controller
{
    /**
     * list questions
     */
    public function indexAction()
    {

    }

    /**
     * Manage questions
     *
     * @EXT\Route("/manage/{id}", name="cpasimusante_simupoll_question_manage", requirements={"id" = "\d+"}, options={"expose"=true})
     * @EXT\ParamConverter("question", class="CPASimUSanteSimupollBundle:Question", options={"id" = "id"})
     * @EXT\Template("CPASimUSanteSimupollBundle:Question:manage.html.twig")
     * @param Request $request
     * @param Question $question
     * @return array
     */
    public function manageAction(Request $request, Question $question)
    {
        $em = $this->getDoctrine()->getManager();

        // Create an ArrayCollection of the current Proposition objects in the database
        $originalPropositions = new ArrayCollection();
        foreach ($question->getPropositions() as $proposition) {
            $originalPropositions->add($proposition);
        }

        $form = $this->get('form.factory')
            ->create(new QuestionType(), $question);
        $form->handleRequest($request);

        if ($form->isValid()) {
            // remove the relationship between the Proposition and the Question
            foreach ($originalPropositions as $proposition) {
                if (false === $question->getPropositions()->contains($proposition)) {
                    // in a a many-to-one relationship, remove the relationship
                    $proposition->setQuestion(null);
                    $em->persist($proposition);
                    // to delete the Proposition entirely, you can also do that
                    $em->remove($proposition);
                }
            }

            $em->persist($question);
            $em->flush();
        }

        return array(
            '_resource' => $question,
            'form'      => $form->createView(),
        );
    }
}
