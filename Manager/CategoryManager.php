<?php

namespace CPASimUSante\SimupollBundle\Manager;

use JMS\DiExtraBundle\Annotation as DI;
use Doctrine\ORM\EntityManager;
use Claroline\CoreBundle\Persistence\ObjectManager;
use Claroline\CoreBundle\Entity\User;

use CPASimUSante\SimupollBundle\Entity\Simupoll;
use CPASimUSante\SimupollBundle\Entity\Category;

/**
 * Helper functions for Categories
 *
 * @DI\Service("cpasimusante.simupoll.category_manager")
 */
class CategoryManager
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

    public function getCategoryByIdAndUser($cid, $user)
    {
        return $this->om->getRepository('CPASimUSanteSimupollBundle:Category')
            ->findOneBy(
                array(
                    'id'=>$cid,
                    'user'=>$user
                ));
    }

    public function getCategoryBySimupoll(Simupoll $simupoll)
    {
        return $this->om->createQueryBuilder()
            ->select('node')
            ->from('CPASimUSante\SimupollBundle\Entity\Category', 'node')
            ->orderBy('node.root, node.lft', 'ASC')
            ->where('node.simupoll = ?1')
            ->setParameters(array(1 => $simupoll))
            ->getQuery();
    }

    /**
    * @param $simupoll Simupoll
    * @param $choice integer type of category selection
    * @param $choiceData string data for the current choice
    */
    public function getCategoryTreeForQuestions(Simupoll $simupoll, $choice=0, $choiceData=array())
    {
        //display tree of categories for group
        $query = $this->getCategoryBySimupoll($simupoll);
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
    * @param $simupoll Simupoll
    * @param $sid integer id of the simupoll
    */
    public function getCategoryTree(Simupoll $simupoll, $sid)
    {
        //Custom query to display only tree from this resource
        $query = $this->getCategoryBySimupoll($simupoll);

        $repo = $this->om->getRepository('CPASimUSanteSimupollBundle:Category');
        //options for the tree display

         $options = array(
            'decorate' => true,
            'rootOpen' => '',
            'rootClose' => '',
            'childOpen' => '<tr>',
            'childClose' => '</tr>',
            'nodeDecorator' => function($node) use ($sid) {
                $modify = ' <a class="btn btn-primary btn-sm category-modify-btn" data-id="'.$node['id'].'" data-sid="'.$sid.'" href="#" title="Modifier la catégorie"><i class="fa fa-edit"></i></a>';
                $add = ' <a class="btn btn-primary btn-sm category-add-btn" data-id="'.$node['id'].'" data-sid="'.$sid.'" href="#" title="Créer une catégorie enfant"><i class="fa fa-plus"></i></a>';
                $delete = ' <a class="btn btn-danger btn-sm category-delete-btn" data-id="'.$node['id'].'" data-sid="'.$sid.'" href="#"><i class="fa fa-trash"></i></a>';
                return '<td>'.str_repeat("=",($node['lvl'])*2).' '.$node['name'].'</td><td class="col-md-1">'.$modify.'</td><td class="col-md-1">'.$add.'</td><td class="col-md-1">'.$delete.'</td>';
            }
        );

        return $repo->buildTree($query->getArrayResult(), $options);
    }

    /**
     * Get array of entity for Use in AJS
     * @param $simupoll Simupoll
     * @return array of entities
     */
    public function getCategoryTreeNg(Simupoll $simupoll)
    {
        //Custom query to display only tree from this resource
        $query = $this->getCategoryBySimupoll($simupoll);
        $results = $query->getArrayResult();
        //add the indent here
        if ($results != array()) {
            foreach ($results as $key => $result) {
                $results[$key]['indent'] = str_repeat("=",($result['lvl'])*2);
            }
        }
        return $results;
    }

    /**
    * @param $simupoll Simupoll
    * @param $categories array list of categories
    */
    public function getCategoryTreeForStats(Simupoll $simupoll, $categories)
    {
        //display tree of categories for group
        $query = $this->getCategoryBySimupoll($simupoll);
        $repoCat = $this->om->getRepository('CPASimUSanteSimupollBundle:Category');
        $repoQuestion = $this->om->getRepository('CPASimUSanteSimupollBundle:Question');
        $options = array(
            'decorate' => true,
            'rootOpen' => '',
            'rootClose' => '',
            'childOpen' => '<tr>',
            'childClose' => '</tr>',
            'nodeDecorator' => function($node) use ($repoQuestion, $categories) {
                $qcount = $repoQuestion->getQuestionCount($node['id']);
                $checked = (in_array($node['id'], $categories)) ? 'checked' : '';
                $input = ' <input type="checkbox" data-id="'.$node['id'].'" name="categorygroup[]" value="'.$node['id'].'" '.$checked.'>';
                return '<td>'.$input.'</td><td>'.$qcount.'</td><td>'.str_repeat("=",($node['lvl'])*2).' '.$node['name'].'</td>';
            }
        );

        return $repoCat->buildTree($query->getArrayResult(), $options);
    }

    public function getCategoryTreeForStatsV2(Simupoll $simupoll, $categorygroups)
    {
        //display tree of categories for group
        $query = $this->getCategoryBySimupoll($simupoll);
        $repoCat = $this->om->getRepository('CPASimUSanteSimupollBundle:Category');
        $repoQuestion = $this->om->getRepository('CPASimUSanteSimupollBundle:Question');
        $options = array(
            'decorate' => true,
            'rootOpen' => '',
            'rootClose' => '',
            'childOpen' => '<tr>',
            'childClose' => '</tr>',
            'nodeDecorator' => function($node) use ($repoQuestion, $categorygroups) {
                $qcount = $repoQuestion->getQuestionCount($node['id']);
                $input = '';
                for ($inc=0;$inc<5;$inc++) {
                    $checked = (isset($categorygroups[$inc]) && false !== strpos($categorygroups[$inc], ','.$node['id'].',')) ? 'checked' : '';
                    $input .= ' <input type="checkbox" title="Groupe '.($inc+1).'" data-id="'.$node['id'].'" name="categorygroup'.$inc.'[]" class="categorygroup'.$inc.'" value="'.$node['id'].'" '.$checked.'>';
                }
                return '<td>'.$input.'</td><td>'.$qcount.'</td><td>'.str_repeat("=",($node['lvl'])*2).' '.$node['name'].'</td>';
            }
        );

        return $repoCat->buildTree($query->getArrayResult(), $options);
    }

    /**
     * Retrieve category list for stats
     *
     * @param $sid Simupoll simupoll id
     * @param $categories array list of category bounds
     */
    public function getCategoryListStats($sid, $categories)
    {
        $allcats = null;
        if ($categories != null) {
            $repoCat = $this->om->getRepository('CPASimUSanteSimupollBundle:Category');
            $allcatstmp = array();
            //find lft value bounds
            $catLft = $repoCat->findLftById($sid, $categories);
            $catlength = count($catLft);

            for ($c=0;$c<$catlength;$c++) {
                $begin = $catLft[$c];
                $end = (isset($catLft[$c+1])) ? $catLft[$c+1] : '';
                $allcatstmp[] = $repoCat->getCategoriesBetween($sid, $begin, $end);
            }
            $allcats = json_encode($allcatstmp);
        }
        return $allcats;
    }

    /**
    * obsolete
    */
    public function decodeCategories($statsmanage)
    {
        $categoryList = array();
        if ($statsmanage != array()) {
            $list = $statsmanage[0]->getCompleteCategoryList();
            $categoryList = json_decode($list);
        }
        return $categoryList;
    }

    /**
     * Deletes a category.
     *
     * @param integer $cid
     * @param User $user
     */
    public function deleteCategory($cid, User $user)
    {
        $category = $this->getCategoryByIdAndUser($cid, $user);
        $this->om->remove($category);
        $this->om->flush();
    }
}
