<?php

namespace CPASimUSante\SimupollBundle\Manager;

use JMS\DiExtraBundle\Annotation as DI;
use Doctrine\ORM\EntityManager;
use Claroline\CoreBundle\Persistence\ObjectManager;

use CPASimUSante\SimupollBundle\Entity\Simupoll;
use CPASimUSante\SimupollBundle\Entity\Statcategorygroup;
use CPASimUSante\SimupollBundle\Controller\SimupollController;
use Buzz\Browser;

/**
 * Helper functions for Simupoll Actions
 *
 * @DI\Service("cpasimusante.simupoll.simupoll_manager")
 */
class SimupollManager
{
    private $om;

    /**
     * @DI\InjectParams({
     *     "om" = @DI\Inject("claroline.persistence.object_manager")
     * })
     *
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    public function getSimupollById($sid)
    {
        return $this->om->getRepository('CPASimUSanteSimupollBundle:Simupoll')
            ->findOneById($sid);
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
     * @return array array of titles and categories
     */
    public function getStatcategoryData($statcategorygroup)
    {
        $cats = array();
        $titles = array();
        if ($statcategorygroup != array()) {
            foreach ($statcategorygroup as $scg) {
                $cats[] = explode(',', $scg->getGroup());
                $titles[] = $scg->getTitle();
            }
        }
        return array('cats'=> $cats, 'titles'=>$titles);
    }

    /**
    * Prepare the html code for displaying the stat results
    *
    * @param $datas array array of stats results
    * @return string
    */
    public function prepareHtmlStats($datas)
    {
        $html = '';
        $htmltmp = '';
        $data = $datas['row'];
        // echo '<pre>';var_dump($data);echo '</pre>';die();
        //echo '<pre>';echo $sid.'<br>';var_dump($data);echo '</pre>';die();
        $htmltmp .= '
            <tr><th><b>'.$data['simupoll'].'</b></th>
            <th>Moyenne générale : '.number_format(($data['galmean'])*100, 2).'%<br> = Moyenne tous essais pour tous utilisateurs</th>
            <th>Moyenne dernier essai : '.number_format(($data['galmeanlast'])*100, 2).'%</th></tr>';

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
                $htmltmp .= '<tr>
                    <td><u>' . $userdata['uname'] . '</u></td>
                    <td>Moyenne tous essais :  ' . number_format(($data['user'][$u]['mean']) * 100, 2) . '%</td>
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
        return $html;
    }

    /**
     * Prepare the csv content for exporting the stat results
     *
     * @param $datas array array of stats results
     * @param $handle file handle
     * @return void
     */
    public function setCsvContent($datas, $handle)
    {
//echo '<pre>';var_dump($datas);echo '</pre>';
//die();
        //Row 1 : directory name
        $csv = array();
        $csv[] = 'Nom';
        $csv[] = $datas['row']['grouptitle'];
        fputcsv($handle, $csv);

        $csv = array();
        $csv[] = '';
        $csv[] = 'Moyenne tous essais';
        $csv[] = 'Moyenne dernier essai';
        fputcsv($handle, $csv);

        $csv = array();
        $csv[] = 'Groupe';
        $csv[] = number_format(($datas['row']['allgalmean'])*100, 2);
        $csv[] = number_format(($datas['row']['allgalmeanlast'])*100, 2);
        fputcsv($handle, $csv);

        foreach($datas['row']['mean'] as $u => $val) {
            $csv = array();
            $csv[] = $datas['row']['user'][$u]['uname'];
            $csv[] = number_format(($val)*100, 2);
            $csv[] = number_format(($datas['row']['mean_last'][$u])*100, 2);
            fputcsv($handle, $csv);
        }

        //Row 2 :
        $csv = array();
        $csv[] = '';
        $csv[] = '';
        $csv[] = '';
        $csv[] = '';
        $csv[] = '';
        $csv[] = '';

        //questions name
        foreach($datas['row']['question'] as $question) {
            $csv[] = $question['name'];
        }
        fputcsv($handle, $csv);

        //Row 3 :
        $csv = array();
        $csv[] = '';
        $csv[] = '';
        $csv[] = '';
        //general mean
        $csv[] = 'Moyenne Générale';
        $csv[] = number_format(($datas['row']['galmean'])*100, 2);
        fputcsv($handle, $csv);

        //Row 4 + : all users
        foreach($datas['row']['user'] as $u => $userdata) {
            //row 4n :
            $csv = array();
            $csv[] = '';
            $csv[] = '';
            $csv[] = '';
            //username
            $csv[] = $userdata['uname'];
            fputcsv($handle, $csv);

            //row 4n+1 :
            $csv = array();
            $csv[] = '';
            $csv[] = '';
            $csv[] = '';
            $csv[] = 'Moyenne tous essais';
            $csv[] = number_format(($userdata['mean'])*100, 2);
            fputcsv($handle, $csv);

            //row 4n+2 :
            $csv = array();
            $csv[] = '';
            $csv[] = '';
            $csv[] = '';
            $csv[] = 'Moyenne dernier essai';
            $csv[] = number_format(($datas['row']['avg_last'][$u])*100, 2);
            fputcsv($handle, $csv);

            //row 4n+3 + : responses
            $incr = 1;
            foreach($userdata['question'] as $pid => $paperresponse) {
                $csv = array();
                $csv[] = '';
                $csv[] = '';
                $csv[] = '';
                $csv[] = '';
                $csv[] = '';
                $csv[] = 'Essai '.$incr. ' ('.$userdata['start'][$pid].' - '.$userdata['end'][$pid]. ')';
                foreach($paperresponse as $response) {
                    $csv[] = $response;
                }
                fputcsv($handle, $csv);
                $incr++;
            }
        }
//die();
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
    private function rgbacolor($color, $opacity=1)
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
     * @param $users array
     * @return $row array
     */
    public function getResultsAndStatsForSimupoll(
        Simupoll $simupoll,
        $categories=array(),
        $title='',
        $users=array()
        )
    {
        $row = array();
        $simupollId = $simupoll->getId();
        //list of labels for Choice
        $choicetmp = array();
/*
        //get stat parameters : list of categories and users to use
        $userlist = '';
        $categories = '';
        $usersAndCategories = $this->om->getRepository('CPASimUSanteSimupollBundle:Statmanage')
            ->findOneBy(array('simupoll' => $simupollId));
        if (isset($usersAndCategories)) {
            $userlist = $usersAndCategories->getUserList();
            $categories = $usersAndCategories->getCategoryList();
        }
*/
        //query to get the mean for last try for the exercise
        $averages = $this->om->getRepository('CPASimUSanteSimupollBundle:Answer')
            ->getAverageForSimupollLastTryByUser($simupollId);

        $row['grouptitle'] = $title;
        $row['galmeanlast'] = 0;
        foreach($averages as $average) {
            //mean for last try for a user for an exercise
            $row['avg_last'][$average['user']] = $average['average_mark'];
            //mean for last try for a user for all exercises
            if (!isset($row['mean_last'][$average['user']])) {
                $row['mean_last'][$average['user']] = $average['average_mark'];
                $row['mean_lastcount'][$average['user']] = 1;
            }
            else {
                $row['mean_last'][$average['user']] += $average['average_mark'];
                $row['mean_lastcount'][$average['user']] += 1;
            }
            $row['galmeanlast'] += $average['average_mark'];
        }
        if (count($averages)> 0)
            $row['galmeanlast'] = $row['galmeanlast'] / count($averages);

        //simupoll title
        $row['simupoll'] = $simupoll->getName();

        $tmpmean = array();
        $gmean = array('m'=>0, 'c'=>0);
        //get all answers
        $simupollAnswers = $this->om->getRepository('CPASimUSanteSimupollBundle:Answer')
            ->getQuerySimupollAllResponsesInCategoriesForAllUsers($simupoll->getId(), $categories, 'id');
            //->getSimupollAllResponsesForAllUsersQuery($simupoll->getId(), 'id');

        foreach ($simupollAnswers as $responses) {
            $paper = $responses->getPaper();
            $paperId = $paper->getId();
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
            foreach ($choiceIds as $cid) {
                if (!in_array($cid, $choicetmp)) { //to avoid duplicate queries
                    $label = $this->om->getRepository('CPASimUSanteSimupollBundle:Proposition')->find($cid)->getChoice();
                    $choicetmp[$cid] = $label;
                    $choice[] = $label;
                }
                else {
                    $choice[] = $choicetmp[$cid];
                }
            }

            $question = $responses->getQuestion();
            $questionId = $question->getId();
            //question title
            $row['question'][$questionId]['name'] = $question->getTitle();
            //list of choices
            $row['user'][$uid]['question'][$paperId][] = implode(';', $choice);

            if (!isset($tmpmean[$uid])) {
                $tmpmean[$uid]['sum'] = $mark;
                $tmpmean[$uid]['count'] = 1;
            }
            else {
                $tmpmean[$uid]['sum'] += $mark;
                $tmpmean[$uid]['count'] += 1;
            }

            $gmean['m'] += $mark;
            $gmean['c'] += 1;
        }

        foreach ($tmpmean as $uid => $m) {
            //compute mean for each user
            if (isset($m['count'])) {
                $row['user'][$uid]['mean'] = $m['sum']/$m['count'];
            }
            else {
                $row['user'][$uid]['mean'] = 0;
            }
            //general mean for user
            if (isset($row['mean'][$uid])) {
                $row['mean'][$uid] += $row['user'][$uid]['mean'];
                $row['mean_count'][$uid] += 1;
            }
            else {
                $row['mean'][$uid] = $row['user'][$uid]['mean'];
                $row['mean_count'][$uid] = 1;
            }
        }

        if ($gmean['c'] != 0) {
            $row['galmean'] = $gmean['m']/$gmean['c'];
        }
        else {
            $row['galmean'] = 0;
        }
        //mean for all exercises
        if (!isset($row['allgalmean'])) {
            $row['allgalmean']      = $row['galmean'];
            $row['allgalmeanlast']  = $row['galmeanlast'];
            $row['galmeancount']    = 1;
        }
        else {
            $row['allgalmean']      += $row['galmean'];
            $row['allgalmeanlast']  += $row['galmeanlast'];
            $row['galmeancount']    += 1;
        }
        return $row;
    }
}
