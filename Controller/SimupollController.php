<?php
namespace CPASimUSante\SimupollBundle\Controller;

use Claroline\CoreBundle\Library\Resource\ResourceCollection;
use CPASimUSante\SimupollBundle\Entity\Simupoll;
use CPASimUSante\SimupollBundle\Form\CategoryType;
use CPASimUSante\SimupollBundle\Form\SimupollType;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as EXT;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
     * Manage the form to create the simupoll
     *
     * @EXT\Route("/edit/{id}", name="cpasimusante_simupoll_edit", requirements={"id" = "\d+"}, options={"expose"=true})
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

            //pass the simupoll id to filter the categories
            $form = $this->get('form.factory')
                ->create(new SimupollType($simupoll->getId()), $simupoll);

            $form->handleRequest($request);
            if ($form->isValid()) {

                // remove the relationship between the question and the Simupoll
                foreach ($originalQuestions as $question) {
                    if (false === $simupoll->getQuestions()->contains($question)) {
                        // remove the Simupoll from the question
                        //$question->getSimupoll()->removeElement($simupoll);

                        // in a a ManyToOne relationship, remove the relationship
                       // $question->setSimupoll(null);
                       // $em->persist($question);

                        // to delete the question entirely, you can also do that
                        $em->remove($question);
                    }
                }
//echo '<pre>';var_dump();echo '</pre>';die();
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
     * When opening the Simupoll resource
     *
     * @EXT\Route("/open/{id}", name="cpasimusante_simupoll_open", requirements={"id" = "\d+"}, options={"expose"=true})
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

        return array(
            '_resource'         => $simupoll,
            'allowToCompose'    => $allowToCompose
        );
    }

    /**
     * Organizing the Simupoll resource
     *
     * @EXT\Route("/organize/{id}", name="cpasimusante_simupoll_organize", requirements={"id" = "\d+"}, options={"expose"=true})
     * @EXT\ParamConverter("simupoll", class="CPASimUSanteSimupollBundle:Simupoll", options={"id" = "id"})
     * @EXT\Template("CPASimUSanteSimupollBundle:Simupoll:organize.html.twig")
     * @param Simupoll $simupoll
     * @return array
     */
    public function organizeAction($simupoll)
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
            $repo = $em->getRepository('CPASimUSanteSimupollBundle:Category');
            //retrieve max level of category
            $maxCategoryLevel = $repo->getMaxLevel($simupoll);

            //display tree of categories for group
            $query = $em->createQueryBuilder()
                ->select('node')
                ->from('CPASimUSante\SimupollBundle\Entity\Category', 'node')
                ->orderBy('node.root, node.lft', 'ASC')
                ->where('node.simupoll = ?1')
                ->setParameters(array(1 => $simupoll))
                ->getQuery();
            $repoCat = $em->getRepository('CPASimUSanteSimupollBundle:Question');

            $options = array(
                'decorate' => true,
                'rootOpen' => '',
                'rootClose' => '',
                'childOpen' => '<tr>',
                'childClose' => '</tr>',
                'nodeDecorator' => function($node) use ($repoCat) {
                    $qcount = $repoCat->getQuestionCount($node['id']);
                    $input = ' <input type="checkbox" data-id="'.$node['id'].'" name="categorygroup[]">';
                    return '<td>'.$input.'</td><td>'.$qcount.'</td><td>'.str_repeat("=",($node['lvl'])*2).' '.$node['name'].'</td>';
                }
            );
            $tree = $repo->buildTree($query->getArrayResult(), $options);

            return array(
                'tree'              => $tree,
                'maxCategoryLevel'  => $maxCategoryLevel['maxlevel'],
                'allowToCompose'    => $allowToCompose,
                '_resource'         => $simupoll,
            );
        } else {
            return $this->redirect($this->generateUrl('cpasimusante_simupoll_open', array('simupollId' => $simupoll->getId())));
        }
    }

    /**
     * Display the statistics choices
     *
     * @EXT\Route("/result/{id}", name="cpasimusante_simupoll_results", requirements={"id" = "\d+"}, options={"expose"=true})
     * @EXT\ParamConverter("simupoll", class="CPASimUSanteSimupollBundle:Simupoll", options={"id" = "id"})
     * @EXT\Template("CPASimUSanteSimupollBundle:Simupoll:results.html.twig")
     * @param Simupoll $simupoll
     * @return array
     */
    public function resultsAction($simupoll)
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

            return array(
                'allowToCompose'    => $allowToCompose,
                '_resource'         => $simupoll,
            );
        } else {
            return $this->redirect($this->generateUrl('cpasimusante_simupoll_open', array('simupollId' => $simupoll->getId())));
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


    /**
     * General gathering of data to create statistics
     */
    public function prepareResultsAndStatsForSimupoll(Simupoll $simupoll)
    {
        $simupollId = $simupoll->getId();
        //list of labels for Choice
        $choicetmp = array();

        $em = $this->getDoctrine()->getManager();
        //$papers = $em->getRepository('CPASimUSanteSimupollBundle:Paper')->findBySimupoll($simupoll);

        //query to get the mean for last try for the exercise
        $averages = $this->getDoctrine()
            ->getManager()
            ->getRepository('CPASimUSanteExoverrideBundle:Response')
            ->getAverageForExerciseLastTryByUser($simupollId);

        $row['galmeanlast'] = 0;
        foreach($averages as $average)
        {
            //mean for last try for a user for an exercise
            $row['avg_last'][$average['user']] = $average['average_mark'];
            //mean for last try for a user for all exercises
            if (!isset($row['mean_last'][$average['user']]))
            {
                $row['mean_last'][$average['user']] = $average['average_mark'];
                $row['mean_lastcount'][$average['user']] = 1;
            }
            else
            {
                $row['mean_last'][$average['user']] += $average['average_mark'];
                $row['mean_lastcount'][$average['user']] += 1;
            }
            $row['galmeanlast'] += $average['average_mark'];
        }
        if (count($averages)> 0)
            $row['galmeanlast'] = $row['galmeanlast'] / count($averages);

        //simupoll title
        $row['simupoll'] = $simupoll->getName();

        $gmean = array('m'=>0, 'c'=>0);
        //get all answers
        $simupollAnswers = $this->getDoctrine()
            ->getManager()
            ->getRepository('CPASimUSanteSimupollBundle:Answer')
            ->getSimupollAllResponsesForAllUsersQuery($simupoll->getId(), 'id');

        foreach ($simupollAnswers as $responses)
        {
            $paper = $responses->getPaper();
            //paper_id
            $paperId = $paper->getId();
            //user id
            $uid = $paper->getUser()->getId();
            //user name
            $uname = $paper->getUser()->getLastName() . '-' . $paper->getUser()->getFirstName();
            $user[$uid] = $uname;

            //mark
            $mark = $responses->getMark();

            $row['user'][$uid]['uname'] = $uname;
            $row['user'][$uid]['mark'][$paperId][] = $mark;
            $row['user'][$uid]['start'][$paperId] = $paper->getStart()->format('Y-m-d H:i:s');
            $row['user'][$uid]['end'][$paperId] = '';//$paper->getEnd()->format('Y-m-d H:i:s');

            //get the result for responses for an exercise

            //can't get the choice directly in the first query (string with ;)
            $choice = array();
            $choiceIds = array_filter(explode(";", $responses->getAnswer()), 'strlen'); //to avoid empty value
            foreach ($choiceIds as $cid)
            {
                if (!in_array($cid, $choicetmp))//to avoid duplicate queries
                {
                    $label = $em->getRepository('CPASimUSanteSimupollBundle:Proposition')->find($cid)->getChoice();
                    $choicetmp[$cid] = $label;
                    $choice[] = $label;
                }
                else
                {
                    $choice[] = $choicetmp[$cid];
                }
            }

            $question = $responses->getQuestion();
            $questionId = $question->getId();
            //question title
            $row['question'][$questionId]['name'] = $question->getTitle();
            //list of choices
            $row['user'][$uid]['question'][$paperId][] = implode(';', $choice);

            if (!isset($tmpmean[$uid]))
            {
                $tmpmean[$uid]['sum'] = $mark;
                $tmpmean[$uid]['count'] = 1;
            }
            else
            {
                $tmpmean[$uid]['sum'] += $mark;
                $tmpmean[$uid]['count'] += 1;
            }

            $gmean['m'] += $mark;
            $gmean['c'] += 1;
        }

        foreach ($tmpmean as $uid => $m)
        {
            //compute mean for each user
            if (isset($m['count']))
            {
                $row['user'][$uid]['mean'] = $m['sum']/$m['count'];
            }
            else
            {
                $row['user'][$uid]['mean'] = 0;
            }
            //general mean for user
            if (isset($row['mean'][$uid]))
            {
                $row['mean'][$uid] += $row['user'][$uid]['mean'];
                $row['mean_count'][$uid] += 1;
            }
            else
            {
                $row['mean'][$uid] = $row['user'][$uid]['mean'];
                $row['mean_count'][$uid] = 1;
            }
        }

        if ($gmean['c'] != 0)
        {
            $row['galmean'] = $gmean['m']/$gmean['c'];
        }
        else
        {
            $row['galmean'] = 0;
        }
        //mean for all exercises
        if (!isset($row['allgalmean']))
        {
            $row['allgalmean']      = $row['galmean'];
            $row['allgalmeanlast']  = $row['galmeanlast'];
            $row['galmeancount']    = 1;
        }
        else
        {
            $row['allgalmean']      += $row['galmean'];
            $row['allgalmeanlast']  += $row['galmeanlast'];
            $row['galmeancount']    += 1;
        }

        return array(
            'row'               => $row
        );
    }

    /**
     * Display the statistics for the simupoll
     *
     * @EXT\Route("/showgeneralstats/{id}", name="cpasimusante_simupoll_stats_allhtml", requirements={"id" = "\d+"}, options={"expose"=true})
     * @EXT\ParamConverter("simupoll", class="CPASimUSanteSimupollBundle:Simupoll", options={"id" = "id"})
     * @EXT\Template("CPASimUSanteSimupollBundle:Simupoll:showStats.html.twig")
     * @param Simupoll $simupoll
     * @return array
     */
    public function getResultAllhtmlAction(Simupoll $simupoll)
    {
        $html = '';

        //to associate the names
        $user = array();

        $datas = $this->prepareResultsAndStatsForSimupoll($simupoll);

        $htmltmp = '';
        $data = $datas['row'];

           // echo '<pre>';var_dump($data);echo '</pre>';die();
            //echo '<pre>';echo $sid.'<br>';var_dump($data);echo '</pre>';die();

            $htmltmp .= '<tr><th><b>'.$data['simupoll'].'</b></th>';
            $htmltmp .= '<th>Moyenne générale : '.number_format(($data['galmean'])*100, 2).'%<br> = Moyenne tous essais pour tous utilisateurs</th>';
                $htmltmp .= '<th>Moyenne dernier essai : '.number_format(($data['galmeanlast'])*100, 2).'%</th></tr>';

                $htmltmp .= '<tr><td colspan="3">Questions : <ul>';
                if (isset($data['question'])) {
                    foreach ($data['question'] as $question) {
                        $htmltmp .= '<li>' . $question['name'] . '</li>';
                    }
                }
                $htmltmp .= '</ul></td></tr>';
                if (isset($data['user'])) {
                    foreach ($data['user'] as $u => $userdata) {
                        $user[$u] = $userdata['uname'];
                        $htmltmp .= '<tr><td><u>' . $userdata['uname'] . '</u></td>';
                        $htmltmp .= ' <td>Moyenne tous essais :  ' . number_format(($data['user'][$u]['mean']) * 100, 2) . '%</td>
                    <td>Moyenne dernier essai :  ' . number_format(($data['avg_last'][$u]) * 100, 2) . '%</td>
                    </tr>';

                        $inc = 1;
                        $htmltmp .= '<tr><td colspan="3">Réponse : <br>';
                        foreach ($userdata['mark'] as $p => $papermark) {
                            $htmltmp .= 'Essai ' . $inc . ' (' . $userdata['start'][$p] . ' - ' . $userdata['end'][$p] . ') => ';
                            foreach ($papermark as $m => $mark) {
                                $htmltmp .= $userdata['question'][$p][$m] . ' : ' . number_format(($mark) * 100, 2) . '%  - ';
                            }
                            $htmltmp .= '<br>';
                            $inc++;
                        }
                        $htmltmp .= '</td></tr>';
                    }
                }
            //Display mean
            $mean = '';
            if (isset($datalist['mean'])) {
                foreach ($datalist['mean'] as $u => $val) {
                    $mean .= '<tr><td><u>' . $user[$u] . '</u></td><td>' . number_format(($val) * 100, 2) . '%</td>' .
                        '<td>' . number_format(($datalist['mean_last'][$u]) * 100, 2) . '%</td></tr>';
                }
            }
            $meanlast='';

            $htmltmp = '<table class="table table-responsive">'.
                '<tr><th></th><th><b>Moyenne générale tous essais</b></th><th><b>Moyenne générale dernier essais</b></th></tr>'.
                '<tr><td>Groupe</td><td>'.number_format(($datas['row']['allgalmean'])*100, 2).'%</td><td>'.number_format(($datas['row']['allgalmeanlast'])*100, 2).'%</td><tr>'.
                $mean.$meanlast.
                $htmltmp.
                '</table>';

            $html .= $htmltmp;



        return array(
            '_resource'     => $simupoll,
            'html'          => $html
        );
    }

    /**
     * Export the statistics for the simupoll
     *
     * @EXT\Route("/exportstats/{id}", name="cpasimusante_simupoll_stats_csv", requirements={"id" = "\d+"}, options={"expose"=true})
     * @EXT\ParamConverter("simupoll", class="CPASimUSanteSimupollBundle:Simupoll", options={"id" = "id"})
     * @param Simupoll $simupoll
     * @return array
     */
    public function getResultCsvAction($simupoll)
    {
        $date = new \DateTime();
        $now = $date->format('Y-m-d-His');

        $content = '';

        return new Response($content, 200, array(
            'Content-Type' => 'application/force-download',
            'Content-Disposition' => 'attachment; filename="exportall-'.$now.'.csv"'
        ));
    }

    /**
     * Prepare the statistics in json for radar display
     *
     * @EXT\Route("/jsonstats/{id}", name="cpasimusante_simupoll_stats_json", requirements={"id" = "\d+"}, options={"expose"=true})
     * @EXT\ParamConverter("simupoll", class="CPASimUSanteSimupollBundle:Simupoll", options={"id" = "id"})
     * @param Simupoll $simupoll
     * @return array
     */
    public function getResultJsonAction($simupoll)
    {

    }
}