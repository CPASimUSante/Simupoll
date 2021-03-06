<?php

namespace CPASimUSante\SimupollBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration as EXT;
use JMS\DiExtraBundle\Annotation as DI;
use CPASimUSante\SimupollBundle\Manager\SimupollManager;
use CPASimUSante\SimupollBundle\Manager\CategoryManager;
use CPASimUSante\SimupollBundle\Manager\PeriodManager;
use CPASimUSante\SimupollBundle\Manager\StatmanageManager;
use CPASimUSante\SimupollBundle\Manager\StatcategorygroupManager;
use CPASimUSante\SimupollBundle\Entity\Organization;
use CPASimUSante\SimupollBundle\Entity\Statcategorygroup;
use CPASimUSante\SimupollBundle\Entity\Simupoll;
use CPASimUSante\SimupollBundle\Entity\Statmanage;
use CPASimUSante\SimupollBundle\Form\SimupollType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class SimupollController.
 *
 * @category   Controller
 *
 * @author     CPASimUSante <contact@simusante.com>
 * @copyright  2015 CPASimUSante
 * @license    http://www.opensource.org/licenses/mit-license.php MIT License
 *
 * @version    0.1
 *
 * @link       http://simusante.com
 *
 * @EXT\Route(
 *      "/",
 *      name    = "cpasimusante_simupoll",
 * )
 */
class SimupollController extends Controller
{
    //list of hexa colors for graph
    public static $RGBCOLORS = array('#000000', '#FFFF00', '#1CE6FF', '#FF34FF', '#FF4A46', '#008941', '#006FA6', '#A30059',
      '#FFDBE5', '#7A4900', '#0000A6', '#63FFAC', '#B79762', '#004D43', '#8FB0FF', '#997D87',
      '#5A0007', '#809693', '#FEFFE6', '#1B4400', '#4FC601', '#3B5DFF', '#4A3B53', '#FF2F80',
      '#61615A', '#BA0900', '#6B7900', '#00C2A0', '#FFAA92', '#FF90C9', '#B903AA', '#D16100',
      '#DDEFFF', '#000035', '#7B4F4B', '#A1C299', '#300018', '#0AA6D8', '#013349', '#00846F',
      '#372101', '#FFB500', '#C2FFED', '#A079BF', '#CC0744', '#C0B9B2', '#C2FF99', '#001E09',
      '#00489C', '#6F0062', '#0CBD66', '#EEC3FF', '#456D75', '#B77B68', '#7A87A1', '#788D66',
      '#885578', '#FAD09F', '#FF8A9A', '#D157A0', '#BEC459', '#456648', '#0086ED', '#886F4C', );

    private $simupollManager;
    private $categoryManager;
    private $periodManager;
    private $statmanageManager;
    private $statcategorygroupManager;

    /**
     * @DI\InjectParams({
     *     "simupollManager"            = @DI\Inject("cpasimusante.simupoll.simupoll_manager"),
     *     "categoryManager"            = @DI\Inject("cpasimusante.simupoll.category_manager"),
     *     "periodManager"              = @DI\Inject("cpasimusante.simupoll.period_manager"),
     *     "statmanageManager"          = @DI\Inject("cpasimusante.simupoll.statmanage_manager"),
     *     "statcategorygroupManager"   = @DI\Inject("cpasimusante.simupoll.statcategorygroup_manager")
     * })
     *
     * @param SimupollManager   simupollManager
     * @param CategoryManager   categoryManager
     * @param PeriodManager   periodManager
     * @param StatmanageManager   statmanageManager
     */
    public function __construct(
        SimupollManager $simupollManager,
        CategoryManager $categoryManager,
        PeriodManager $periodManager,
        StatmanageManager $statmanageManager,
        StatcategorygroupManager $statcategorygroupManager
    ) {
        $this->simupollManager = $simupollManager;
        $this->categoryManager = $categoryManager;
        $this->periodManager = $periodManager;
        $this->statmanageManager = $statmanageManager;
        $this->statcategorygroupManager = $statcategorygroupManager;
    }

    /**
     * Manage the form to create the simupoll.
     *
     * @EXT\Route("/edit/{id}", name="cpasimusante_simupoll_edit", requirements={"id" = "\d+"}, options={"expose"=true})
     * @EXT\ParamConverter("simupoll", class="CPASimUSanteSimupollBundle:Simupoll", options={"id" = "id"})
     * @EXT\Template("CPASimUSanteSimupollBundle:Simupoll:edit.html.twig")
     *
     * @param Request  $request
     * @param Simupoll $simupoll
     *
     * @return array
     */
    public function editAction(Request $request, Simupoll $simupoll)
    {
        //can user access ?
        $this->checkAccess('OPEN', $simupoll);

        //can user edit ?
        $simupollAdmin = $this->container
            ->get('cpasimusante_simupoll.services.simupoll')
            ->isGrantedAccess($simupoll, 'ADMINISTRATE');

        if ($simupollAdmin === true) {
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
                '_resource' => $simupoll,
                'form' => $form->createView(),
            );
        }
        //If not admin, open
        else {
            return $this->redirect($this->generateUrl('cpasimusante_simupoll_open', array('id' => $simupoll->getId())));
        }
    }

    /**
     * When opening the Simupoll resource.
     *
     * @EXT\Route("/open/{id}", name="cpasimusante_simupoll_open", requirements={"id" = "\d+"}, options={"expose"=true})
     * @EXT\ParamConverter("simupoll", class="CPASimUSanteSimupollBundle:Simupoll", options={"id" = "id"})
     * @EXT\Template("CPASimUSanteSimupollBundle:Simupoll:open.html.twig")
     *
     * @param Simupoll $simupoll
     *
     * @return array
     */
    public function openAction($simupoll)
    {
        $em = $this->getDoctrine()->getManager();

        $workspace = $simupoll->getResourceNode()->getWorkspace();
        $user = $this->container->get('security.token_storage')
            ->getToken()->getUser();

        //can user access ?
        $this->checkAccess('OPEN', $simupoll);

        //can user edit ?
        $simupollAdmin = $this->container
            ->get('cpasimusante_simupoll.services.simupoll')
            ->isGrantedAccess($simupoll, 'ADMINISTRATE');

        //can user manage exercise
        $allowToCompose = 0;

        if (is_object($user) && ($simupollAdmin === true)) {
            $allowToCompose = 1;
        }

        //is there a period set or is this the right period to answer ?
        $opened = $this->periodManager->getOpenedPeriod($simupoll->getId());

        //is there any response to this simupoll
        $hasresponse = $this->simupollManager->hasResponse($simupoll);

        //is there any question for this simupoll
        $hasquestion = $this->simupollManager->hasQuestion($simupoll);
        //$hasquestion = ($simupoll->getHasPaper() !== true) ? false : true;

        //is there any category for this simupoll
        $hascategory = $this->simupollManager->hasCategory($simupoll);

        return array(
            '_resource' => $simupoll,
            '_workspace' => $workspace,
            'opened' => $opened,
            'allowToCompose' => $allowToCompose,
            'simupollAdmin' => $simupollAdmin,
            'hasresponse' => $hasresponse,
            'hasquestion' => $hasquestion,
            'hascategory' => $hascategory,
        );
    }

    /**
     * Organizing the Simupoll resource, how will the questions will be displayed.
     *
     * @EXT\Route("/organize/{id}", name="cpasimusante_simupoll_organize", requirements={"id" = "\d+"}, options={"expose"=true})
     * @EXT\ParamConverter("simupoll", class="CPASimUSanteSimupollBundle:Simupoll", options={"id" = "id"})
     * @EXT\Template("CPASimUSanteSimupollBundle:Simupoll:organize.html.twig")
     *
     * @param Request $request
     * @param Simupoll $simupoll
     *
     * @return array
     */
    public function organizeAction(Request $request, $simupoll)
    {
        //can user access ?
        $this->checkAccess('OPEN', $simupoll);

        $sid = $simupoll->getId();

        //can user edit ?
        $simupollAdmin = $this->container
            ->get('cpasimusante_simupoll.services.simupoll')
            ->isGrantedAccess($simupoll, 'ADMINISTRATE');

        $user = $this->container->get('security.token_storage')
            ->getToken()->getUser();

        //can user manage exercise
        $allowToCompose = 0;
        if (is_object($user) && ($simupollAdmin === true)) {
            $em = $this->getDoctrine()->getManager();
            $allowToCompose = 1;
            $repoCat = $em->getRepository('CPASimUSanteSimupollBundle:Category');
            //retrieve max level of category
            $maxCategoryLevel = $repoCat->getMaxLevel($simupoll);

            //retrieve saved organization
            $orga = $em->getRepository('CPASimUSanteSimupollBundle:Organization')
                ->findOneBySimupoll($simupoll);
//echo '<pre>';var_dump($orga);echo '</pre>';
            $choice = '';
            $choiceData = '';
            $categoryList = '';
            if ($orga !== null) {
                $choice = $orga->getChoice();
                $choiceData = $orga->getChoiceData();
            }

            if ($request->isMethod('POST')) {
                //to avoid bug in simupoll compose if we change the choice
                if (null !== $this->get('session')->get('simupaper'.$sid)) {
                    $this->get('session')->remove('simupaper'.$sid);
                }
                $choice = $request->request->get('questdisp');
                if ($choice == 0) {
                    $choiceData = '';
                } elseif ($choice == 1) {
                    $choiceData = $request->request->get('max_question_per_page');
                /*} elseif ($choice == 2) {
                    $choiceData = $request->request->get('question_per_category_level');*/
                } elseif ($choice == 2) {
                    $choice_categorygroup = $request->request->get('categorygroup');
                    $choiceData = ($choice_categorygroup !== []) ? implode(',', $choice_categorygroup) : '';
                    //retrieve category list to avoid further request in results
                    $cats = $repoCat->getCategoriesInLft($sid, $choice_categorygroup);
                    if ($cats !== null) {
                        $cl = [];
                        foreach ($cats as $c) {
                            $cl[] = $c->getId();
                        }
                        if ($cl !== array()) {
                            $categoryList = implode(',', $cl);
                        }
                    }
                }

                if ($orga === null) {
                    $orga = new Organization();
                    $orga->setSimupoll($simupoll);
                    $orga->setChoice($choice);
                    $orga->setChoiceData($choiceData);
                    $orga->setCategoryList($categoryList);
                    $em->persist($orga);
                } else {
                    $orga->setChoice($choice);
                    $orga->setChoiceData($choiceData);
                    $orga->setCategoryList($categoryList);
                    $em->persist($orga);
                }
                $em->flush();
                $this->get('session')->getFlashBag()->add('info', 'Organisation mise à jour');
            }

            $categoryTree = $this->categoryManager->getCategoryTreeForQuestions($simupoll, $choice, $choiceData);

            return [
                'choice' => $choice,
                'choiceData' => $choiceData,
                'tree' => $categoryTree,
                'maxCategoryLevel' => $maxCategoryLevel['maxlevel'],
                'allowToCompose' => $allowToCompose,
                '_resource' => $simupoll,
            ];
        } else {
            return $this->redirect($this->generateUrl(
                'cpasimusante_simupoll_open',
                ['id' => $sid]
            ));
        }
    }

    /**
     * Display the statistics choices page.
     *
     * @EXT\Route("/result/{id}", name="cpasimusante_simupoll_results", requirements={"id" = "\d+"}, options={"expose"=true})
     * @EXT\ParamConverter("simupoll", class="CPASimUSanteSimupollBundle:Simupoll", options={"id" = "id"})
     * @EXT\Template("CPASimUSanteSimupollBundle:Simupoll:results.html.twig")
     *
     * @param Simupoll $simupoll
     *
     * @return array
     */
    public function resultsAction($simupoll)
    {
        //can user access ?
        $this->checkAccess('OPEN', $simupoll);

        $user = $this->container->get('security.token_storage')
            ->getToken()->getUser();

        //can user edit ?
        $simupollAdmin = $this->container
            ->get('cpasimusante_simupoll.services.simupoll')
            ->isGrantedAccess($simupoll, 'ADMINISTRATE');
// echo '<pre>';var_dump(is_object($user));echo '</pre>';
// //echo '<pre>';var_dump($simupollAdmin);echo '</pre>';
// die();
        //can user manage exercise
        $allowToCompose = 0;
        if (is_object($user)) {
            $allowToCompose = 1;

            return [
                'allowToCompose' => $allowToCompose,
                '_resource' => $simupoll,
            ];
        } else {
            return $this->redirect($this->generateUrl('cpasimusante_simupoll_open', ['id' => $simupoll->getId()]));
        }
    }

/**
 * Setup the statistics.
 *
 * @EXT\Route("/statsetup/{id}", name="cpasimusante_simupoll_stat_setup", requirements={"id" = "\d+"}, options={"expose"=true})
 * @EXT\ParamConverter("simupoll", class="CPASimUSanteSimupollBundle:Simupoll", options={"id" = "id"})
 * @EXT\Template("CPASimUSanteSimupollBundle:Simupoll:statSetup.html.twig")
 *
 * @param Simupoll $simupoll
 *
 * @return array
 */
     //TODO : remove categorylist and completecategorylist from statmanage
    public function statSetupAction(Request $request, $simupoll)
    {
        $categories = [];
        $user = $this->container->get('security.token_storage')
            ->getToken()->getUser();
        $uids = '';
        $datatosave = [];
        //TODO : change for dynamic group number
        $groupNb = 9;

        //TODO : manage user administrate stat display OR  chose a stat for him
        //can user OPEN ? Not ADMINISTRATE because user can make its
        $simupollAdmin = $this->container
            ->get('cpasimusante_simupoll.services.simupoll')
            ->isGrantedAccess($simupoll, 'OPEN');
        $allowToCompose = 0;

        //If user is auth
        if (is_object($user) && ($simupollAdmin === true)) {
            $allowToCompose = 1;
            //retrieve data
            $statsmanage = $this->statmanageManager
                ->getStatmanageBySimupollAndUser($user, $simupoll);
            $statcategorygroup = [];
            $titles = [];
            $categorygroupsWithComma = [];

            if ($statsmanage !== []) {
                //retrieve users
                $uids = $statsmanage[0]->getUserList();

                $statcategorygroup = $this->statcategorygroupManager
                    ->getStatcategorygroupByStatmanage($statsmanage[0]);
                //retrieve title & categorygroups
                foreach ($statcategorygroup as $scg) {
                    $titles[] = $scg->getTitle();
                    $categorygroupsWithComma[] = ','.$scg->getGroup().',';
                }
            }

            if ($request->isMethod('POST')) {
                //list of users
                $uids = $request->request->get('simupoll_userlist');

                for ($i = 0; $i <= $groupNb; ++$i) {
                    //raw values from request
                    $title = $request->request->get('group_title'.$i);
                    $categorygroup = $request->request->get('categorygroup'.$i);

                    $titles[$i] = $title;

                    if (isset($categorygroup)) {
                        $categorygroup = implode(',', $categorygroup);
                        $categorygroupsWithComma[$i] = ','.$categorygroup.',';
                    }

                    //group to save
                    if (trim($title) !== '') {
                        $datatosave[] = ['title' => $title, 'group' => $categorygroup];
                    }
                }
//echo '<pre>';var_dump($datatosave);echo '</pre>';

                $em = $this->getDoctrine()->getManager();
                //save Statmanage
                if ($statsmanage === []) {
                    $statsmanage = new Statmanage();
                    $statsmanage->setUser($user);
                    $statsmanage->setSimupoll($simupoll);
                    $statsmanage->setUserList($uids);
                    $em->persist($statsmanage);
                    $sm = $statsmanage;
                } else {
                    $statsmanage[0]->setUserList($uids);
                    $em->persist($statsmanage[0]);
                    $sm = $statsmanage[0];
                }
                $em->flush();

                if ($datatosave !== []) {
                    //save Statcategorygroup
                    if ($statcategorygroup !== []) {
                        //delete old value
                        foreach ($statcategorygroup as $scg) {
                            $em->remove($scg);
                        }
                    }
                    //insert new values
                    foreach ($datatosave as $value) {
                        $statcategorygroup = new Statcategorygroup();
                        $statcategorygroup->setStatmanage($sm);
                        $statcategorygroup->setTitle($value['title']);
                        $statcategorygroup->setGroup($value['group']);
                        $em->persist($statcategorygroup);
                    }
                    $em->flush();
                }
            }

            $tree = $this->categoryManager->getCategoryTreeForStatsV2($simupoll, $categorygroupsWithComma, $groupNb);

            return [
                'groupNb' => $groupNb,
                'userlist' => $uids,
                'titles' => $titles,
                'tree' => $tree,
                'allowToCompose' => $allowToCompose,
                '_resource' => $simupoll,
            ];
        //User not auth => get out!
        } else {
            return $this->redirect($this->generateUrl('cpasimusante_simupoll_open', ['id' => $simupoll->getId()]));
        }

/*
        if ($statsmanage != array()) {
            $uids = $statsmanage[0]->getUserList();
            $cats = $statsmanage[0]->getCategoryList();
            $categories = ($cats != '') ? explode(',', $cats) : array();
        }

        if ($request->isMethod('POST')) {
            $uids = $request->request->get('simupoll_userlist');
            $categories = $request->request->get('categorygroup');
            $cats = ($categories != null) ? implode(',', $categories) : '';

            //generate list of all categories
            $allcats = $this->categoryManager->getCategoryListStats($simupoll->getId(), $categories);

            if ($statsmanage == null) {
                $statsmanage = new Statmanage();
                $statsmanage->setUser($user);
                $statsmanage->setSimupoll($simupoll);
                $statsmanage->setCategoryList($cats);
                $statsmanage->setUserList($uids);
                $statsmanage->setCompleteCategoryList($allcats);
                $em->persist($statsmanage);
            } else {
                $statsmanage[0]->setCategoryList($cats);
                $statsmanage[0]->setUserList($uids);
                $statsmanage[0]->setCompleteCategoryList($allcats);
                $em->persist($statsmanage[0]);
            }
            $em->flush();
        }

*/
    }

    /**
     * JSON list of users in WS.
     *
     * @EXT\Route("/usersinws/{wslist}", name="cpasimusante_simupoll_get_user_in_ws", options={"expose"=true})
     *
     * @param string $wslist
     *
     * @return JsonResponse
     */
    public function getUsersInWorkspaceAction($wslist = '')
    {
        $ids = $this->simupollManager->getUsersInWorkspace($wslist);

        return new JsonResponse($ids);
    }

    /**
     * General gathering of data to create statistics.
     *
     * @param Simupoll $simupoll
     * @param array    $categories array of categories for the group
     * @param string   $groupTitle title of the group
     * @param array    $users      list of users from Statmanage
     *
     * @return array array of results
     */
    public function prepareResultsAndStatsForSimupoll(
        Simupoll $simupoll,
        $categories = [],
        $groupTitle = '',
        $groupId = 0,
        $users = []
        ) {
        //get periods for the simupoll
        $periodData = [];
        $periods = $this->container
            ->get('cpasimusante_simupoll.services.simupoll')
            ->periodList($simupoll);
        foreach ($periods as $period) {
            $periodData[$period->getId()] = $period->getTitle();
        }
//echo '<pre>';var_dump($users);echo '</pre>';die();

        $row = $this->simupollManager
            ->getResultsAndStatsForSimupoll($simupoll, $categories, $groupTitle, $groupId, $users, $periodData);

        return [
            'row' => $row,
        ];
    }

    /**
     * Display the statistics for the Simupoll.
     *
     * @EXT\Route("/showgeneralstats/{id}", name="cpasimusante_simupoll_stats_allhtml", requirements={"id" = "\d+"}, options={"expose"=true})
     * @EXT\ParamConverter("simupoll", class="CPASimUSanteSimupollBundle:Simupoll", options={"id" = "id"})
     * @EXT\Template("CPASimUSanteSimupollBundle:Simupoll:showStats.html.twig")
     *
     * @param Simupoll $simupoll
     *
     * @return array
     */
    public function getResultAllHtmlAction(Simupoll $simupoll)
    {
        //to associate the names
        $users = [];
        $html = '';
        $user = $this->container->get('security.token_storage')
            ->getToken()->getUser();
        //get the stat Configuration
        $statsmanage = $this->statmanageManager
            ->getStatmanageBySimupollAndUser($user, $simupoll);
        $statcategorygroup = $this->statcategorygroupManager
            ->getStatcategorygroupByStatmanage($statsmanage);

        if (isset($statsmanage[0])) {
            //retrieve titles and categorygroups
            $data = $this->simupollManager->getStatcategoryData($statcategorygroup);
            //get categories groups : array (groups) of array (categories)
            $categoryList = $data['cats'];
            //List of users to be shown in the graph
            $userdata = $statsmanage[0]->getUserList();
            $userlist = ($userdata == '') ? [] : explode(',', $userdata);

            //non admin user get to see only their stats and those of the group
            $simupollAdmin = $this->container
                ->get('cpasimusante_simupoll.services.simupoll')
                ->isGrantedAccess($simupoll, 'ADMINISTRATE');
            if ($simupollAdmin != true) {
                $userlist = [$user->getId()];
            }

            $users = $this->simupollManager->getUserData($userlist);
            foreach ($categoryList as $inc => $categories) {
                $datas = $this->prepareResultsAndStatsForSimupoll(
                    $simupoll,
                    $categories,
                    $data['titles'][$inc],
                    $data['ids'][$inc],
                    $userlist
                );
                $htmltmp = $this->simupollManager->prepareHtmlStats($datas, $users);
                $html .= $htmltmp;
            }
//echo '<pre>';var_dump($categoryList);echo '</pre>';die();
        } else {
            $this->get('session')->getFlashBag()->add(
               'error',
               $this->get('translator')->trans('statmanage_not_set', [], 'resource')
           );
            return $this->redirect($this->generateUrl('cpasimusante_simupoll_results', ['id' => $simupoll->getId()] ));
        }

        return [
            '_resource' => $simupoll,
            'html' => $html,
        ];
    }

    /**
     * Export the statistics for the simupoll.
     *
     * @EXT\Route("/exportstats/{id}", name="cpasimusante_simupoll_stats_csv", requirements={"id" = "\d+"}, options={"expose"=true})
     * @EXT\ParamConverter("simupoll", class="CPASimUSanteSimupollBundle:Simupoll", options={"id" = "id"})
     *
     * @param Simupoll $simupoll
     *
     * @return array
     */
    public function getResultCsvAction($simupoll)
    {
        $date = new \DateTime();
        $now = $date->format('Y-m-d-His');
        $user = $this->container->get('security.token_storage')
            ->getToken()->getUser();
        //get the stat Configuration
        $statsmanage = $this->statmanageManager->getStatmanageBySimupollAndUser($user, $simupoll);
        $statcategorygroup = $this->statcategorygroupManager->getStatcategorygroupByStatmanage($statsmanage);
        $content = '';

        if (isset($statsmanage[0])) {
            //retrieve titles and categorygroups
            $data = $this->simupollManager->getStatcategoryData($statcategorygroup);
            //get categories groups : array (groups) of array (categories)
            $categoryList = $data['cats'];
            //List of users to be shown in the graph
            $userdata = $statsmanage[0]->getUserList();
            $userlist = ($userdata == '') ? [] : explode(',', $userdata);
            $users = $this->simupollManager->getUserData($userlist);

            //open file stream
            $handle = fopen('php://memory', 'r+');
            foreach ($categoryList as $inc => $categories) {
                $datas = $this->prepareResultsAndStatsForSimupoll(
                    $simupoll,
                    $categories,
                    $data['titles'][$inc],
                    $data['ids'][$inc],
                    $userlist
                );
                //Add csv lines
                $this->simupollManager->setCsvContent($datas, $users, $handle);
            }

            rewind($handle);
            $content = stream_get_contents($handle);
            fclose($handle);

            return new Response($content, 200, [
                'Content-Type' => 'application/force-download',
                'Content-Disposition' => 'attachment; filename="simupoll-exportall-'.$now.'.csv"',
            ]);
        } else {
            $this->get('session')->getFlashBag()->add(
               'error',
               $this->get('translator')->trans('statmanage_not_set', [], 'resource')
           );
            return $this->redirect($this->generateUrl('cpasimusante_simupoll_results', ['id' => $simupoll->getId()]));
        }
    }

    /**
     * Page to display the graph for the Simupoll.
     *
     * @EXT\Route("/simugraph/{id}", name="cpasimusante_simupoll_graph_show", requirements={"id" = "\d+"}, options={"expose"=true})
     * @EXT\ParamConverter("simupoll", class="CPASimUSanteSimupollBundle:Simupoll", options={"id" = "id"})
     * @EXT\Template("CPASimUSanteSimupollBundle:Simupoll:showGraph.html.twig")
     *
     * @param Simupoll $simupoll
     *
     * @return array
     */
    public function getResultGraphAction($simupoll)
    {
        return [
            '_resource' => $simupoll,
        ];
    }

    /**
     * Prepare the statistics in json for radar display
     * format :
     * {
     *      labels: ["group1","group2",...],
     *      datasets: [
     *          {
     *              fillColor : "rgba(r,v,b,0.5)",
     *              strokeColor : "rgba(r,v,b,1)",
     *              pointColor : "rgba(r,v,b,1)",
     *              pointstrokeColor : "yellow",
     *              data : [va11, val2, ...],
     *              title : "sometitle"
     *          },{...}
     *      ]
     * }.
     *
     * @EXT\Route("/jsonstats/{id}", name="cpasimusante_simupoll_stats_json", requirements={"id" = "\d+"}, options={"expose"=true})
     * @EXT\ParamConverter("simupoll", class="CPASimUSanteSimupollBundle:Simupoll", options={"id" = "id"})
     *
     * @param Simupoll $simupoll
     *
     * @return array
     */
    public function getJsonForGraphAction($simupoll)
    {
        $json = [];
        $users = [];
        $allgalmeanlast = [];
        $allgalmean = [];
        $usernames = [];
        $dataforjson = [];
        $user = $this->container->get('security.token_storage')
            ->getToken()->getUser();

        //get the stat Configuration
        $statsmanage = $this->statmanageManager
            ->getStatmanageBySimupollAndUser($user, $simupoll);
        $statcategorygroup = $this->statcategorygroupManager
            ->getStatcategorygroupByStatmanage($statsmanage);

        if (isset($statsmanage[0])) {
            //retrieve titles and categorygroups
            $data = $this->simupollManager
                ->getStatcategoryData($statcategorygroup);
            //List of users to be shown in the graph
            $userdata = $statsmanage[0]->getUserList();
            $userlist = ($userdata == '') ? [] : explode(',', $userdata);

            //non admin user get to see only their stats and those of the group
            $simupollAdmin = $this->container
                ->get('cpasimusante_simupoll.services.simupoll')
                ->isGrantedAccess($simupoll, 'ADMINISTRATE');
            if ($simupollAdmin != true) {
                $userlist = [$user->getId()];
            }

            $users = $this->simupollManager->getUserData($userlist);

            //get categories groups : array (groups) of array (categories)
            $categoryList = $data['cats'];
//echo '<pre>';var_dump($data);echo '</pre>';
            foreach ($categoryList as $inc => $categories) {
                $datas = $this->prepareResultsAndStatsForSimupoll(
                    $simupoll,
                    $categories,
                    $data['titles'][$inc],
                    $data['ids'][$inc],
                    $userlist
                );
//echo '<pre>';var_dump($users);echo '</pre>';die();
                foreach ($datas['row']['period'] as $periodId => $period) {
                    if (isset($datas['row']['gAvgByPeriod'][$periodId][$datas['row']['groupid']])) {
                        $juser['mean'][0][$periodId][] = number_format(($datas['row']['gAvgByPeriod'][$periodId][$datas['row']['groupid']]) * 100, 2);
                    } else {
                        $juser['mean'][0][$periodId][] = 0;
                    }
                    foreach ($users as $userId => $userd) {
                        $juser['name'][$userId] = $userd;
                        if (isset($datas['row']['avgByPeriodAndUser'][$periodId][$datas['row']['groupid']][$userId])) {
                            $juser['mean'][$userId][$periodId][] = number_format(($datas['row']['avgByPeriodAndUser'][$periodId][$datas['row']['groupid']][$userId]) * 100, 2);
                        } else {
                            $juser['mean'][$userId][$periodId][] = 0;
                        }
                    }
                    $json[$periodId]['graph']['labels'][] = $datas['row']['grouptitle'];
                }
            }
//echo '<pre>';var_dump($juser);echo '</pre>';
            $colors = array_map(array($this->simupollManager, 'rgb2hex'), self::$RGBCOLORS);

            foreach ($datas['row']['period'] as $periodId => $period) {
                $inc = 0;
                $json[$periodId]['graphtitle'] = 'Statistiques pour '.$period;
                //for group
                $json[$periodId]['graph']['datasets'][] = $this->simupollManager
                    ->setObjectForRadarDataset(
                        'group',
                        $juser['mean'][0][$periodId],
                        $this->simupollManager->rgbacolor($colors[$inc])
                    );
                ++$inc;
                //for user
                foreach ($juser['name'] as $uid => $name) {
                    $json[$periodId]['graph']['datasets'][] = $this->simupollManager
                        ->setObjectForRadarDataset(
                            $name,
                            $juser['mean'][$uid][$periodId],
                            $this->simupollManager->rgbacolor($colors[$inc])
                        );
                    ++$inc;
                }
//echo '<pre>';var_dump($json);echo '</pre>';
            }
        }

        return new JsonResponse($json);
    }

    /**
     * Importing Simupoll data.
     *
     * @EXT\Route("/import/{id}",  name="cpasimusante_simupoll_import", requirements={"id" = "\d+"}, options={"expose"=true})
     * @EXT\ParamConverter("simupoll", class="CPASimUSanteSimupollBundle:Simupoll", options={"id" = "id"})
     * @EXT\Template("CPASimUSanteSimupollBundle:Simupoll:import.html.twig")
     *
     * @return array
     */
    public function importSimupollAction(Request $request, Simupoll $simupoll)
    {
        $sid = $simupoll->getId();
        $sim = $simupoll;
//echo '<pre>$sim->getResourceNode()->getWorkspace()->getId';var_dump($sim->getResourceNode()->getWorkspace()->getId());echo '</pre>';
        if ($request->isMethod('POST')) {
            $questionfile = $request->files->get('questionfile');
            $categoryfile = $request->files->get('categoryfile');

            if (isset($questionfile)) {
                //if ($questionfile->getMimeType() != 'text/csv')
                $this->simupollManager->importFile($sid, $questionfile, 'question');
            }
            if (isset($categoryfile)) {
                $user = $this->container->get('security.token_storage')
                    ->getToken()->getUser();
                $this->simupollManager->importFile($sid, $categoryfile, 'category', $user);
            }
        }
//echo '<pre>$sim->getResourceNode()->getWorkspace()->getId';var_dump($sim->getResourceNode()->getWorkspace()->getId());echo '</pre>';

        return [
            '_resource' => $sim,
        ];
    }
}
