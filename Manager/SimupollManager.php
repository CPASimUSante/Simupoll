<?php

namespace CPASimUSante\SimupollBundle\Manager;

use JMS\DiExtraBundle\Annotation as DI;
use Doctrine\ORM\EntityManager;
use Claroline\CoreBundle\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Claroline\CoreBundle\Entity\User;

use CPASimUSante\SimupollBundle\Entity\Simupoll;
use CPASimUSante\SimupollBundle\Entity\Question;
use CPASimUSante\SimupollBundle\Entity\Category;
use CPASimUSante\SimupollBundle\Entity\Period;
use CPASimUSante\SimupollBundle\Entity\Proposition;
use CPASimUSante\SimupollBundle\Entity\Statcategorygroup;
use CPASimUSante\SimupollBundle\Controller\SimupollController;

/**
 * Helper functions for Simupoll Actions
 *
 * @DI\Service("cpasimusante.simupoll.simupoll_manager")
 */
class SimupollManager
{
    private $om;
    private $container;
    private $translator;
    private $session;

    /**
     * @DI\InjectParams({
     *     "om"         = @DI\Inject("claroline.persistence.object_manager"),
     *     "container"  = @DI\Inject("service_container"),
     *     "session"    = @DI\Inject("session"),
     *     "translator" = @DI\Inject("translator")
     * })
     *
     * @param ObjectManager $om
     */
    public function __construct(
        ObjectManager $om,
        ContainerInterface $container,
        SessionInterface $session,
        TranslatorInterface $translator
        )
    {
        $this->om           = $om;
        $this->container    = $container;
        $this->translator   = $translator;
        $this->session      = $session;
    }

    public function getSimupollById($sid)
    {
        return $this->om->getRepository('CPASimUSanteSimupollBundle:Simupoll')
            ->findOneById($sid);
    }

    public function getSimupollData(Simupoll $simupoll)
    {
        $simupollData = array();
        $simupollRawData = $this->om->getRepository('CPASimUSanteSimupollBundle:Simupoll')
            ->findOneById($simupoll->getId());

        $questionRawData = $this->om->getRepository('CPASimUSanteSimupollBundle:Question')
            ->getQuestionsBySimupoll($simupoll);

        foreach ($questionRawData as $keyq => $question) {
            $propositionRawData = $this->om->getRepository('CPASimUSanteSimupollBundle:Proposition')
                ->findByQuestion($question);
            $props = array();
            $cat = $question->getCategory();
            $category = array(
                'id'        => $cat->getId(),
                'name'      => $cat->getName(),
                'lft'       => $cat->getLft(),
                'lvl'       => $cat->getLvl(),
                'rgt'       => $cat->getRgt(),
                'root'      => $cat->getRoot(),
                'indent'    => str_repeat("=",($cat->getLvl())*2)
            );
            foreach ($propositionRawData as $keyp => $proposition) {
                $props[] = array(
                    'id'=>$proposition->getId(),
                    'choice'=> $proposition->getChoice(),
                    'mark'=> $proposition->getmark()
                );
            }
            $simupollData['qp'][] = array(
                'title'         => $question->getTitle(),
                'category'      => $category,
                'id'            => $question->getId(),
                'propositions'  => $props
            );
        }
        $simupollData['description'] = $simupollRawData->getDescription();

        return $simupollData;
    }

    /**
     * Does the Simupoll has answer
     *
     * @param $simupoll Simupoll
     * @return boolean
     */
    public function hasResponse(Simupoll $simupoll)
    {
        $answers = $this->om->getRepository('CPASimUSanteSimupollBundle:Paper')
            ->findBySimupoll($simupoll);
        return ($answers !== array()) ? true : false;
    }

    /**
     * Does the Simupoll has question
     *
     * @param $simupoll Simupoll
     * @return boolean
     */
    public function hasQuestion(Simupoll $simupoll)
    {
        $question = $this->om->getRepository('CPASimUSanteSimupollBundle:Question')
            ->findBySimupoll($simupoll);
        return ($question !== array()) ? true : false;
    }

    /**
     * Does the Simupoll has category
     *
     * @param $simupoll Simupoll
     * @return boolean
     */
    public function hasCategory(Simupoll $simupoll)
    {
        $category = $this->om->getRepository('CPASimUSanteSimupollBundle:Category')
            ->findBySimupoll($simupoll);
        return ($category !== array()) ? true : false;
    }

    /**
     * Array of categories to get stats from
     *
     * @param $categories string list of categories (c1,c2;c3,c4;c5)
     * @return array list of categories
     */
    public function getListOfCategories($categories)
    {
        $categorylist = array();
        if ($categories != '') {
            //for each group of exercise
            $list = explode(';', $categories);
            //get array of exercise
            foreach ($list as $item) {
                if ($item != '') {
                    $categorylist[] = explode(',', $item);
                }
            }
        }
        return $categorylist;
    }

    /**
    * @param $choice integer type of category selection
    * @param $choiceData string data for the current choice
    */
    public function getCategoryTree(Simupoll $simupoll, $choice=0, $choiceData=array())
    {
        //display tree of categories for group
        $query = $this->om->createQueryBuilder()
          ->select('node')
          ->from('CPASimUSante\SimupollBundle\Entity\Category', 'node')
          ->orderBy('node.root, node.lft', 'ASC')
          ->where('node.simupoll = ?1')
          ->setParameters(array(1 => $simupoll))
          ->getQuery();
        $repoCat = $this->om->getRepository('CPASimUSanteSimupollBundle:Category');
        $repoQuestion = $this->om->getRepository('CPASimUSanteSimupollBundle:Question');
        $options = array(
            'decorate' => true,
            'rootOpen' => '',
            'rootClose' => '',
            'childOpen' => '<tr>',
            'childClose' => '</tr>',
        );
        if ($choice != 2) {
            $options['nodeDecorator'] = function($node) use ($repoQuestion) {
                $qcount = $repoQuestion->getQuestionCount($node['id']);
                if ($node['lvl']==0) {
                    $extra = '<input type="hidden" name="categorygroup[]" value="'.$node['lft'].'">';
                    $input = $extra.' <input type="checkbox" data-id="'.$node['id'].'" name="categorygroup[]" value="'.$node['lft'].'" checked disabled>';
                } else {
                    $input = ' <input type="checkbox" data-id="'.$node['id'].'" name="categorygroup[]" value="'.$node['lft'].'">';
                }
                return '<td>'.$input.'</td><td>'.$qcount.'</td><td>'.str_repeat("=",($node['lvl'])*2).' '.$node['name'].'</td>';
            };
        } else {
            $choice_categorygroup = ($choiceData != array()) ? explode(',', $choiceData) : array();
            $options['nodeDecorator'] = function($node) use ($repoQuestion, $choice_categorygroup) {
                $qcount = $repoQuestion->getQuestionCount($node['id']);
                $disabled = ($node['lvl']==0) ? " disabled" : "";
                $checked = (in_array($node['lft'], $choice_categorygroup) || $node['lvl']==0) ? " checked" : "";
                //root is mandatory
                $extra = ($node['lvl']==0) ? '<input type="hidden" name="categorygroup[]" value="'.$node['lft'].'">' : '';
                $input = $extra.' <input type="checkbox" data-id="'.$node['id'].'" name="categorygroup[]" value="'.$node['lft'].'"'.$checked.$disabled.'>';
                return '<td>'.$input.'</td><td>'.$qcount.'</td><td>'.str_repeat("=",($node['lvl'])*2).' '.$node['name'].'</td>';
            };
        }
        $tree = $repoCat->buildTree($query->getArrayResult(), $options);
        return $tree;
    }

    /**
     * @param $statcategorygroup    array result of findBy : array of Statcategorygroup
     * @return array array of titles, categories and ids
     */
    public function getStatcategoryData($statcategorygroup)
    {
        $cats = array();
        $titles = array();
        $ids = array();
        if ($statcategorygroup != array()) {
            foreach ($statcategorygroup as $scg) {
                $cats[] = explode(',', $scg->getGroup());
                $titles[] = $scg->getTitle();
                $ids[] = $scg->getId();
            }
        }
        return array('cats'=> $cats, 'titles'=>$titles, 'ids'=> $ids);
    }

    public function getUserData($userlist)
    {
        $userdata = array();
        $ud = $this->om->getRepository('ClarolineCoreBundle:User')
            ->findById($userlist);
        foreach($ud as $user) {
            $userdata[$user->getId()] =  $user->getLastName() . '-' . $user->getFirstName();
        }
        return $userdata;
    }

    /**
    * Prepare the html code for displaying the stat results
    *
    * @param $datas array array of stats results
    * @param $user array array of selected users (user[id] = name)
    * @return string html to be display
    */
    public function prepareHtmlStats($datas, $users)
    {
        $html = '';
        $htmltmp = '';
        $htmlmean = array('th'=>'', 'td'=>'');
        $data = $datas['row'];
        $periodcount = count($data['period']);
        $questioncount = count($data['question']);
        $htmlmeantmp = array();
        $htmlmeangroup = '<th>Groupe</th>';
        $htmlgalmeantmp = '';
        $htmlmeantmp[0] = '<th>Groupe</th>';
//echo '<pre>';var_dump($data);echo '</pre>';

        foreach($data['period'] as $periodId => $period) {
            $htmlmean['th'] .= '<th>'.$period.'</th>';
            /*//for group stats
            if (isset($data['galmean'][$periodId])) {
                $htmlmeangroup .= '<td>'.number_format(($data['galmean'][$periodId])*100,2).'%</td>';
            } else {
                $htmlmeangroup .= '<td>-</td>';
            }*/
            //for group stats
            if (isset($data['gAvgByPeriod'][$periodId][$data['groupid']])) {
                $htmlmeantmp[0] .= '<td>'.number_format(($data['gAvgByPeriod'][$periodId][$data['groupid']])*100,2).'%</td>';
            } else {
                $htmlmeantmp[0] .= '<td>-</td>';
            }
            $htmltmp .= '<h3>'.$period.'</h3>';

            foreach ($users as $userId => $userdata) {
                if (!isset($htmlmeantmp[$userId])) {$htmlmeantmp[$userId] = '<th>'.$userdata.'</th>';}
                if (isset($data['avgByPeriodAndUser'][$periodId][$data['groupid']][$userId])) {
                    $htmlmeantmp[$userId] .= '<td>'.number_format(($data['avgByPeriodAndUser'][$periodId][$data['groupid']][$userId])*100,2).'%</td>';
                } else {
                    $htmlmeantmp[$userId] .= '<td>-</td>';
                }
                $htmltmp .= '<h4>'.$userdata.'</h4>';
                $htmltmp .= '<table class="table table-responsive">
                <tr><th>Question</th><th>Réponse</th></tr>';
                foreach ($data['question'] as $questionId => $question) {
                    $htmltmp .= '<tr><td>'.$question.'</td>';
                    if (isset($data['user'][$userId]['proposition'][$questionId][$periodId])){
                        $htmltmp .= '<td>'.$data['user'][$userId]['proposition'][$questionId][$periodId];
                        $htmltmp .= ' ('.$data['user'][$userId]['mark'][$questionId][$periodId].')</td>';
                    } else {
                        $htmltmp .= '<td>-</td>';
                    }
                    $htmltmp .= '</tr>';
                }
                $htmltmp .= '</table><hr>';
            }
        }
        $htmlhead = '<h2>'.$data['grouptitle'].'</h2>';

        //Mean part
        $htmlhead .= '
        <table class="table table-responsive">
        <tr><th>Moyenne par période</th>'.$htmlmean['th'].'</tr>';
        foreach ($users as $userId => $userdata) {
            $htmlhead .= '<tr>'.$htmlmeantmp[$userId].'</tr>';
        }
        $htmlhead .= '<tr>'.$htmlmeantmp[0].'</tr>';
        $htmlhead .= '</table>';
        $htmlhead .= '<p>Moyenne générale, toutes périodes : '.number_format(($data['aAvg'][$data['groupid']])*100,2).'%</p>';
        $html .= $htmltmp;
        $html = $htmlhead.$htmltmp.'</table>';
        return $html;
    }

    /**
     * Prepare the csv content for exporting the stat results
     *
     * @param $datas array array of stats results
     * @param $users array array of selected users (user[id] = name)
     * @param $handle file handle
     * @return void
     */
    public function setCsvContent($datas, $users, $handle)
    {
//echo '<pre>';var_dump($datas);echo '</pre>';
//die();
        $csvmeantmp = array();
        //"spacer" for csv
        $csvspaceperiods = array();
        $data = $datas['row'];

        //Row 1 : group name
        $csv = array();
        $csv[] = 'Nom';
        $csv[] = $data['grouptitle'];
        fputcsv($handle, $csv);

        //Row 2 : mean by period
        $csv = array();
        $csv[] = 'Moyenne par période';
        foreach($data['period'] as $periodId => $period) {
            $csv[] = $period;
        }
        fputcsv($handle, $csv);
        //Row 2-1 : Group
        $csv = array();
        $csv[] = 'Groupe';
        foreach($data['period'] as $periodId => $period) {
            if (isset($data['gAvgByPeriod'][$periodId][$data['groupid']])) {
                $csv[] = number_format(($data['gAvgByPeriod'][$periodId][$data['groupid']])*100,2);
            } else {
                $csv[] = '-';
            }
        }
        fputcsv($handle, $csv);
        //Row 2-(2+) : selected users
        foreach($data['period'] as $periodId => $period) {
            $csvspaceperiods[] = '';
            foreach ($users as $userId => $userdata) {
                if (!isset($csvmeantmp[$userId][0])) {$csvmeantmp[$userId][0] = $userdata;}
                if (isset($data['avgByPeriodAndUser'][$periodId][$data['groupid']][$userId])) {
                    $csvmeantmp[$userId][] = number_format(($data['avgByPeriodAndUser'][$periodId][$data['groupid']][$userId])*100,2);
                } else {
                    $csvmeantmp[$userId][] = '-';
                }
            }
        }
        foreach ($users as $userId => $userdata) {
            fputcsv($handle, $csvmeantmp[$userId]);
        }

        $csv = array();
        fputcsv($handle, $csv);

        //Row 3 : general mean, all periods
        $csv = array();
        $csv[] = 'Moyenne générale, toutes périodes';
        $csv[] = number_format(($data['aAvg'][$data['groupid']])*100,2);
        fputcsv($handle, $csv);

        $csv = array();
        fputcsv($handle, $csv);

        //Row 4 : questions
        $csv = array();
        $csv = $csvspaceperiods + $csv;
        $csv[] = '';
        $csv[] = '';
        //questions name
        foreach($data['question'] as $question) {
            $csv[] = $question;
        }
        fputcsv($handle, $csv);

        //Row 5 + : results for users
        foreach($data['user'] as $userId => $userdata) {
            //row 4n :
            $csv = array();
            $csv = $csvspaceperiods + $csv;
            $csv[] = '';
            //username
            $csv[] = $userdata['uname'];
            fputcsv($handle, $csv);

            foreach($data['period'] as $periodId => $period) {
                $csv = array();
                $csv = $csvspaceperiods + $csv;
                $csv[] = '';
                $csv[] = $period;
                foreach($data['question'] as $questionId => $question) {
                    if (isset($userdata['proposition'][$questionId][$periodId])) {
                        $csv[] = $userdata['proposition'][$questionId][$periodId];
                    } else {
                        $csv[] = '-';
                    }
                }
                fputcsv($handle, $csv);
            }
            $csv = array();
            fputcsv($handle, $csv);
        }
    }

    /**
     * Data prepared for use in radar display
     *
     * @param $datas array array of stats results
     * @param $userlist array list of users to be used in the graph
     * @return $json array array of data prepared for display
     */
    public function getContentForRadar($datas, $userlist)
    {
// echo '<pre>';var_dump($datas['row']);echo '</pre>';
//die();
        //Init
        $uu = array_keys($datas['row']['user']);

        //array of user without data
        $userlisttmp = $userlist;
        //Display mean (use $exerciselist['mean_last'] instead, for last)
        foreach($datas['row']['mean_last'] as $u => $val) {
            if (in_array($u, $userlist)) {
                if (isset($datas['row']['user'][$u])) { //TODO : fix it : if no answer, user is not known
                    $usernames[$u] = $datas['row']['user'][$u];
                    $user[$u]['name'] = $datas['row']['user'][$u]['uname'];
                    $user[$u]['mean'][] = number_format(($val)*100, 2);
                    //TODO : remove this hacky shit ! save the real username for later
                    $user[$u]['nameok'] = true;
                    //remove user
                    array_diff($userlisttmp, [$u]);
                }
            }
        }
// echo '<pre>';var_dump($userlisttmp);echo '</pre>';
// die();
        //put 0 for absence of data for a user
        foreach($userlist as $u) {
            if (!in_array($u, $uu)) {
                $user[$u]['name'] = $u;
                $user[$u]['mean'][] = 0;
                $user[$u]['nameok'] = false;
            }
        }

        return $user;
    }
    /**
    * @param $json array
    * @return $json array array of data prepared for display
     */
    public function setJsonContentForRadar($allgalmean, $user, $userlist, $json)
    {
        //to rgb
        $colors = array_map(array($this, 'rgb2hex'), SimupollController::$RGBCOLORS);
        $inc = 0;
        //dataset for group
        $json['datasets'][] = $this->setObjectForRadarDataset('group', $allgalmean, $this->rgbacolor($colors[$inc]));
        $inc++;

        //datasets for users
        foreach($user as $uid => $ud) {
            //display only selected users
            if (in_array($uid, $userlist)) {
                if (isset($usernames[$uid])) {  //TODO : fix it : if no answer, user is not known
                    $name = ($ud['nameok']) ? $ud['name'] : $usernames[$uid];
                    $json['datasets'][] = $this->setObjectForRadarDataset($name, $ud['mean'], $this->rgbacolor($colors[$inc]));
                    $inc++;
                }
            }
        }

        return $json;
    }

    /**
     * Transforms rgb color into hexa color
     *
     * @param $color
     * @return array
     */
    public function rgb2hex($color)
    {
        $color = str_replace("#", "", $color);
        $r = hexdec(substr($color,0,2));
        $g = hexdec(substr($color,2,2));
        $b = hexdec(substr($color,4,2));
        return array($r, $g, $b);
    }

    /**
    * Create an object usable for ChartNew.js dataset data in radar graph
    *
    * @param $label string label of dataset
    * @param $data array stats to display
    * @param $color string color of line
    * @param $fill  boolean area filled or empty
    * @return object
    */
    public function setObjectForRadarDataset($label, $data, $color, $fill=false)
    {
        $class = new \stdClass();
        $class->label = $label;
        $class->data = $data;
        $class->pointStrokeColor = "#fff";
        $class->pointHighlightFill = "#fff";
        $class->fillColor = "rgba(0,0,0,0)";
        $class->strokeColor = $color;
        $class->pointHighlightFill = $color;
        return $class;
    }

    /**
     * Create rgba color with opacity
     *
     * @param $color array rvb color
     * @param int $opacity
     * @return string
     */
    public function rgbacolor($color, $opacity=1)
    {
        return 'rgba('.join(',',$color).','.$opacity.')';
    }

    /**
     * Retrieve list of user for the current WS
     *
     * @param $wslist string csv list WS to find users in
     * @return $ids array of users id
     */
    public function getUsersInWorkspace($wslist = '')
    {
        $ids = [];
        if ($wslist !== '') {
            $ws = explode(',', $wslist);
            $listofuser = $this->om->getRepository('ClarolineCoreBundle:User')
                ->findUsersByWorkspaces($ws);
            foreach($listofuser as $user) {
                $ids[] = $user->getId();
            }
        }
        return $ids;
    }

    /**
     * Prepare the data for use in HTML, csv or graph display
     *
     * @param $simupoll Simupoll
     * @param $categories array
     * @param $groupTitle string name of the group
     * @param $groupId integer id of the group
     * @param $userlist array list of user selected in Statmanage
     * @param $periods array list of Period entity for the Simupoll
     * @return $row array
     *  - avgByPeriodAndUser[periodId][groupId][userId]
     *  - gAvgByPeriod[periodId]["period"][groupId]
     *  - user[userId](["uname"], ["mark"], ["start"], ["end"], ["question"][periodId], ["mean"])
     *  - question[questionId][name]
     */
    public function getResultsAndStatsForSimupoll(
        Simupoll $simupoll,
        $categories=array(),
        $groupTitle='',
        $groupId=0,
        $userlist=array(),
        $periods=array()
        )
    {
        $row = array();
        $simupollId = $simupoll->getId();
        //list of labels for Choice
        $choicetmp = array();
        $tmpmean = array();
        $gmean = array();
        $row['question'] = array();
        $row['period'] = $periods;
        $row['simupoll'] = $simupoll->getTitle();
        $row['grouptitle'] = $groupTitle;
        $row['groupid'] = $groupId;

        //avg by period, group, user
        $averages = array();
        $tmpavg = $this->om->getRepository('CPASimUSanteSimupollBundle:Answer')
            ->getAverageForSimupollInCategoriesByUserAndPeriod($simupollId, $categories, $userlist);
        foreach ($tmpavg as $avg) {
            $averages[$avg['period']][$groupId][$avg['user']] = $avg['average_mark'];
        }
        $row['avgByPeriodAndUser'] = $averages;
//echo '<pre>';var_dump($averages);echo '</pre>';die();

        //general average (= for all users), by period
        $tmpgavg = $this->om->getRepository('CPASimUSanteSimupollBundle:Answer')
            ->getGeneralAverageForSimupollInCategoriesByPeriod($simupollId, $categories);
        foreach ($tmpgavg as $id => $gavg) {
            $row['gAvgByPeriod'][$tmpgavg[$id]['period']][$groupId] = $gavg['average_mark'];
        }

        //general average all (= for all users), for all periods = 1 value
        $tmpaavg = $this->om->getRepository('CPASimUSanteSimupollBundle:Answer')
            ->getGeneralAverageForSimupollInCategoriesAllPeriod($simupollId, $categories);
        $row['aAvg'][$groupId] = $tmpaavg[0]['average_mark'];
 //echo '<pre>';var_dump($tmpaavg);echo '</pre>';die();

        //get all answers
        $simupollAnswers = $this->om->getRepository('CPASimUSanteSimupollBundle:Answer')
            ->getQuerySimupollAllResponsesInCategoriesForAllUsers($simupoll->getId(), $categories, 'id');

        foreach ($simupollAnswers as $answers) {
            $paper = $answers->getPaper();
            $paperId = $paper->getId();
            $userId = $paper->getUser()->getId();
            $period = $paper->getPeriod();
            $periodId = $period->getId();
            $question = $answers->getQuestion();
            $questionId = $question->getId();
            //user name
            $uname = $paper->getUser()->getLastName() . '-' . $paper->getUser()->getFirstName();
            $user[$userId] = $uname;

            //mark
            $mark = $answers->getMark();

            $row['user'][$userId]['uname'] = $uname;
            $row['user'][$userId]['mark'][$questionId][$periodId] = $mark;
            $row['user'][$userId]['start'][$periodId] = $paper->getStart()->format('Y-m-d H:i:s');
            $row['user'][$userId]['end'][$periodId] = '';//$paper->getEnd()->format('Y-m-d H:i:s');

            //can't get the choice directly in the first query (string with ;)
            $choice = array();
            $choiceIds = array_filter(explode(";", $answers->getAnswer()), 'strlen'); //to avoid empty value
            foreach ($choiceIds as $cid) {
                if (!in_array($cid, $choicetmp)) { //to avoid duplicate queries
                    $label = $this->om
                        ->getRepository('CPASimUSanteSimupollBundle:Proposition')
                        ->find($cid)->getChoice();
                    $choicetmp[$cid] = $label;
                    $choice[] = $label;
                } else {
                    $row['user'][$userId]['question'];
                }
            }

            //question title
            $row['question'][$questionId] = $question->getTitle();
            $row['user'][$userId]['proposition'][$questionId][$periodId] = implode(';', $choice);

            if (!isset($tmpmean[$periodId][$userId])) {
                $tmpmean[$periodId][$userId]['sum'] = $mark;
                $tmpmean[$periodId][$userId]['count'] = 1;
            } else {
                $tmpmean[$periodId][$userId]['sum'] += $mark;
                $tmpmean[$periodId][$userId]['count'] += 1;
            }
            if (!isset($gmean[$periodId]['mark'])) {
                $gmean[$periodId]['mark'] = $mark;
                $gmean[$periodId]['count'] = 1;
            } else {
                $gmean[$periodId]['mark'] += $mark;
                $gmean[$periodId]['count'] += 1;
            }
        }

        //compute mean
        foreach ($tmpmean as $tmpperiod) {
            foreach ($tmpperiod as $userId => $mean) {
                //compute mean for each user
                if (isset($mean['count'])) {
                    $row['user'][$userId]['mean'] = $mean['sum']/$mean['count'];
                } else {
                    $row['user'][$userId]['mean'] = 0;
                }
                //general mean for user
                if (isset($row['mean'][$userId])) {
                    $row['mean'][$userId] += $row['user'][$userId]['mean'];
                    $row['mean_count'][$userId] += 1;
                } else {
                    $row['mean'][$userId] = $row['user'][$userId]['mean'];
                    $row['mean_count'][$userId] = 1;
                }
            }
        }

        foreach ($gmean as $periodId => $mean) {
            if ($mean['count'] != 0) {
                $row['galmean'][$periodId] = $mean['mark']/$mean['count'];
            } else {
                $row['galmean'][$periodId] = 0;
            }
            //mean for all simupoll
            if (!isset($row['allgalmean'][$periodId])) {
                $row['allgalmean'][$periodId]      = $row['galmean'][$periodId];
                $row['galmeancount'][$periodId]    = 1;
            } else {
                $row['allgalmean'][$periodId]      += $row['galmean'][$periodId];
                $row['galmeancount'][$periodId]    += 1;
            }

            //Compute means
            if ($row['galmeancount'][$periodId] > 0) {
                $row['allgalmean'][$periodId]      = $row['allgalmean'][$periodId] / $row['galmeancount'][$periodId];
                foreach($row['mean'] as $u => $val) {
                    $row['mean'][$u] = $val / $row['mean_count'][$u];
                }
            }
        }

        return $row;
    }

    /**
     * manage import of files in Simupoll
     *
     * @param $sid integer  Simupoll id
     * @param $file resource
     * @param $datatype string ('question' or 'category')
     * @return void
     */
    public function importFile($sid, $file, $datatype, $user=null)
    {
        $sessionFlashBag = $this->session->getFlashBag();
        $datalines = array();

        $data = file_get_contents($file);
        $data = $this->container->get('claroline.utilities.misc')
            ->formatCsvOutput($data);
        $lines = str_getcsv($data, PHP_EOL);
        foreach ($lines as $line) {
            //data separated with ;
            $datalines[] = str_getcsv($line, ';');
        }

        if ($datalines != array()) {
            $createddata = array();

            if ($datatype == 'question') {
                $createddata = $this->importQuestions($sid, $datalines, $createddata);
            } elseif ($datatype == 'category') {
                $createddata = $this->importCategories($sid, $datalines, $user, $createddata);
            }

            if (isset($createddata['ok'])) {
                foreach ($createddata['ok'] as $created) {
                    $msg =  '<' . $created . '> ';
                    $msg .= $this->translator->trans(
                    'has_been_created',
                    array(),
                    'platform'
                    );
                    $sessionFlashBag->add('success', $msg);
                }
            }
            //manage error display
            if (isset($createddata['nokq'])) {
                foreach ($createddata['nokq'] as $created) {
                    $msg = $this->translator->trans(
                        'question_not_created',
                        array('%questionname%' => $created),'resource'
                    );
                    $sessionFlashBag->add('error', $msg);
                }
            }
            if (isset($createddata['nokc'])) {
                foreach ($createddata['nokc'] as $created) {
                    $msg = $this->translator->trans(
                        'category_not_created',
                        array('%categoryname%' => $created),'resource'
                    );
                    $sessionFlashBag->add('error', $msg);
                }
            }
            if (isset($createddata['nokqc'])) {
                foreach ($createddata['nokqc'] as $created) {
                    $msg = $this->translator->trans(
                        'question_category_missing',
                        array('%questionname%' => $created[0], '%categoryname%' => $created[1]),'resource'
                    );
                    $sessionFlashBag->add('error', $msg);
                }
            }
            if (isset($createddata['nokqp'])) {
                foreach ($createddata['nokqp'] as $created) {
                    $msg = $this->translator->trans(
                        'mark_not_set',
                        array('%questionname%' => $created),'resource'
                    );
                    $sessionFlashBag->add('error', $msg);
                }
            }
        }
    }

    /**
     * Import Questions
     *
     * @param $sid integer
     * @param $questions array
     * @return $returnValues array
     */
    public function importQuestions($sid, array $questions, $returnValues=array())
    {
        $this->om->startFlushSuite();

        //cant use $simupoll entity directly because doesn't come from the same om
        //would cause doctrine error
        $simupoll = $this->om
            ->getRepository('CPASimUSanteSimupollBundle:Simupoll')
            ->findOneById($sid);

        foreach ($questions as $questionProposition) {
            $questionTitle = $questionProposition[0];
            $questionCategoryName = $questionProposition[1];
            $countQ = count($questionProposition);

            //does the question exists ?
            $question = $this->om
                ->getRepository('CPASimUSanteSimupollBundle:Question')
                ->findOneBy(array(
                    'title'     => $questionTitle,
                    'simupoll'  => $simupoll
                ));

            if ($question === null) {
                //get category
                $category = $this->om
                ->getRepository('CPASimUSanteSimupollBundle:Category')
                ->findOneBy(array(
                    'name'      => $questionCategoryName,
                    'simupoll'  => $simupoll
                ));
                if ($category != null) {
                    //add question
                    $newQuestion = new Question();
                    $newQuestion->setTitle($questionTitle);
                    $newQuestion->setCategory($category);
                    $newQuestion->setOrderq(1);
                    $newQuestion->setSimupoll($simupoll);
                    $this->om->persist($newQuestion);

                    //add propositions
                    if ($countQ>2) {
                        //get propositions
                        $propositions = array_slice($questionProposition, 2);
                        $countP = count($propositions);

                        for ($inc=0;$inc <$countP;$inc+=2) {
                            //if there is a score for each proposition
                            if (($countP%2 == 0 && $inc == $countP-1) || isset($propositions[($inc+1)])) {
                                $newProposition = new Proposition();
                                $newProposition->setQuestion($newQuestion);
                                $newProposition->setChoice($propositions[$inc]);
                                $newProposition->setMark($propositions[($inc+1)]);
                                $this->om->persist($newProposition);
                            }
                        }
                        if ($countP%2 !=0)
                            $returnValues['nokqp'][] = $questionTitle;
                    }

                    $returnValues['ok'][] = 'Question : '.$questionTitle;
                } else {
                    $returnValues['nokqc'][] = array($questionTitle, $questionCategoryName);
                }
            } else {
                $returnValues['nokq'][] = $questionTitle;
            }
        }
        $this->om->endFlushSuite();

        return $returnValues;
    }

    /**
     * Import categories
     *
     * @param $sid integer
     * @param $categories array
     * @param $user User
     * @return $returnValues array
     */
    public function importCategories($sid, array $categories, User $user, $returnValues=array())
    {
        $this->om->startFlushSuite();

        $simupoll = $this->om
            ->getRepository('CPASimUSanteSimupollBundle:Simupoll')
            ->findOneById($sid);

        $cats   = array();
        $parents = array();
        $children  = array();

        foreach ($categories as $category) {
            if (count($category) >1) {
                $categoryName = $category[0];
                $categoryParent = $category[1];
                //does the category exists already ?
                $cat = $this->om
                ->getRepository('CPASimUSanteSimupollBundle:Category')
                ->findOneBy(array(
                    'name'      => $categoryName,
                    'simupoll'  => $simupoll
                ));
                //if it doesn't
                if ($cat === null) {
                    //create category, with no parent
                    $newCategory = new Category();
                    $newCategory->setName($categoryName);
                    $newCategory->setParent(null);
                    $newCategory->setSimupoll($simupoll);
                    $newCategory->setUser($user);
                    $this->om->persist($newCategory);

                    //save infos
                    $cats[]     = $newCategory;
                    $children[] = $categoryName;
                    $parents[] = $categoryParent;

                    $returnValues['ok'][] = 'Catégorie : '. $categoryName;
                } else {
                    $returnValues['nokc'][] = $categoryName;
                }
            }
        }
        $this->om->endFlushSuite();

        //update parent
        $this->om->startFlushSuite();

        //search for parent
        foreach ($parents as $id => $parent) {
            if ($parent !== 'null') {
                $pc = $this->om->getRepository('CPASimUSanteSimupollBundle:Category')
                ->findOneBy(array(
                    'name'      => $parent,
                    'simupoll'  => $simupoll
                ));
                //if parent is created
                if ($pc !== null) {
                    $cats[$id]->setParent($pc);
                    $this->om->persist($cats[$id]);
                }
            }
        }
        $this->om->endFlushSuite();

        return $returnValues;
    }

    /**
     * Copy Simupoll - called by onCopy listener
     *
     * @param $simupoll Simupoll
     * @param $loggedUser User
     * @return $newSimupoll Simupoll
     */
    public function copySimupoll(Simupoll $simupoll, $loggedUser)
    {
        $newSimupoll = new Simupoll();
        $newSimupoll->setTitle($simupoll->getTitle());
        $this->om->persist($newSimupoll);

        $questions = $simupoll->getQuestions();
        $periods = $this->om->getRepository('CPASimUSanteSimupollBundle:Period')
            ->findBySimupoll($simupoll);
        $categories = $this->om->getRepository('CPASimUSanteSimupollBundle:Category')
            ->findBySimupoll($simupoll);

        //copy period
        foreach ($periods as $period) {
            $newPeriod = new Period();
            $newPeriod->setSimupoll($newSimupoll);
            $newPeriod->setStart($period->getStart());
            $newPeriod->setStop($period->getStop());
            $newPeriod->setTitle($period->getTitle());
            $this->om->persist($newPeriod);
        }

        $nc = [];
        $pc = [];
        foreach ($categories as $category) {
            $newCategory = new Category();
            $newCategory->setName($category->getName());
            $newCategory->setUser($category->getUser());
            $newCategory->setSimupoll($newSimupoll);
            $newCategory->setParent(null);
            $this->om->persist($newCategory);

            if (null !== $category->getParent()) {
                $pc[$category->getId()] = array('nc'=> $newCategory, 'pid'=> $category->getParent()->getId()) ;
            } else {
                $pc[$category->getId()] = array('nc'=> $newCategory, 'pid'=> null) ;
            }
        }

        foreach ($pc as $oldid => $cats) {
            if (isset($pc[$cats['pid']])) {
                $cats['nc']->setParent($pc[$cats['pid']]['nc']);
            } else {
                $cats['nc']->setParent(null);
            }
            $this->om->persist($cats['nc']);
        }

        //copy questions
        foreach ($questions as $question) {
            $newQuestion = new Question();
            $newQuestion->setSimupoll($newSimupoll);
            $newQuestion->setTitle($question->getTitle());
            $newQuestion->setOrderq($question->getOrderq());
            $newQuestion->setCategory($question->getCategory());
            $this->om->persist($newQuestion);

            //copy propositions
            $propositions = $question->getPropositions();
            foreach ($propositions as $proposition) {
                $newProposition = new Proposition();
                $newProposition->setQuestion($newQuestion);
                $newProposition->setChoice($proposition->getChoice());
                $newProposition->setMark($proposition->getMark());
                $this->om->persist($newProposition);
            }
        }

        return $newSimupoll;
    }

    /*
     * Angular manager part
     */
    /**
     * Save the simupoll data
     * string $description
     * array $simupolldata
     */
     public function saveSimupoll(Simupoll $simupoll, $description, $simupolldata)
     {
         $this->om->startFlushSuite();

         $simupoll->setDescription($description);
         $this->om->persist($simupoll);

         //First remove old
         $questions = $this->om->getRepository('CPASimUSanteSimupollBundle:Question')
             ->findBySimupoll($simupoll);
        foreach ($questions as $questionToDelete) {
            $propositions = $this->om->getRepository('CPASimUSanteSimupollBundle:Proposition')
                ->findByQuestion($questionToDelete);
            foreach ($propositions as $propositionToDelete) {
                $this->om->remove($propositionToDelete);
            }
            $this->om->remove($questionToDelete);
        }
        //then add new
         foreach ($simupolldata as $question) {
             //add question
             $newQuestion = new Question();
             $newQuestion->setTitle($question['title']);
             $category = $this->om->getRepository('CPASimUSanteSimupollBundle:Category')
                 ->findById($question['category']['id']);
             $newQuestion->setCategory($category);
             $newQuestion->setOrderq(1);
             $newQuestion->setSimupoll($simupoll);
             $this->om->persist($newQuestion);

            foreach ($question['proposition'] as $proposition) {
                $newProposition = new Proposition();
                $newProposition->setQuestion($newQuestion);
                $newProposition->setChoice($proposition['choice']);
                $mark = (int)($proposition['mark']);
                $newProposition->setMark($mark);
                $this->om->persist($newProposition);
            }
        }
         $this->om->endFlushSuite();
     }
}
