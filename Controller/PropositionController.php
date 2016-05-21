<?php

namespace CPASimUSante\SimupollBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration as EXT;
use JMS\DiExtraBundle\Annotation as DI;
use CPASimUSante\SimupollBundle\Manager\PropositionManager;
use CPASimUSante\SimupollBundle\Entity\Proposition;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class PropositionController.
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
 *      name    = "cpasimusante_simupoll_proposition",
 * )
 */
class PropositionController extends Controller
{
    private $propositionManager;

    /**
     * @DI\InjectParams({
     *     "propositionManager"         = @DI\Inject("cpasimusante.simupoll.proposition_manager")
     * })
     *
     * @param PropositionManager   propositionManager
     */
    public function __construct(
        PropositionManager $propositionManager
    ) {
        $this->propositionManager = $propositionManager;
    }

    /**
     * @EXT\Route("/delete/{pid}", name="simupoll_delete_proposition", options = {"expose"=true})
     * @EXT\ParamConverter("proposition", class="CPASimUSanteSimupollBundle:Proposition", options={"pid" = "id"})
     * @EXT\Method("DELETE")
     *
     * @param Proposition $proposition
     *
     * @return JsonResponse
     */
    public function deletePropositionAction(Proposition $proposition)
    {
        //$this->assertCanEdit($category->getResult());
        $this->propositionManager->deleteProposition($proposition);

        return new JsonResponse('', 204);
    }
}
