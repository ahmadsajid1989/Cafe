<?php


namespace App\Controller;


use App\Entity\Ledger;
use App\Entity\User;
use Doctrine\ORM\QueryBuilder;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\DateTimeColumn;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTable;
use Omines\DataTablesBundle\DataTableFactory;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    private $factory;

    public function __construct(DataTableFactory $factory) {
        $this->factory = $factory;
    }

    /**
     * Creates and returns a basic DataTable instance.
     *
     * @param array $options Options to be passed
     * @return DataTable
     */
    protected function createDataTable(array $options = [])
    {
        return $this->factory->create($options);
    }

    /**
     * Creates and returns a DataTable based upon a registered DataTableType or an FQCN.
     *
     * @param string $type FQCN or service name
     * @param array $typeOptions Type-specific options to be considered
     * @param array $options Options to be passed
     * @return DataTable
     */
    protected function createDataTableFromType($type, array $typeOptions = [], array $options = [])
    {
        return $this->factory->createFromType($type, $typeOptions, $options);
    }

    /**
     * @Route("/user", name="user_home")
     * @IsGranted("ROLE_USER")
     * @param Request $request
     * @param DataTableFactory $dataTableFactory
     *
     * @return Response
     */
    public function userDashboard(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $id = $this->getUser()->getId();


        $table = $this->createDataTable()
            ->add('description', TextColumn::class, array("label" => "Transaction Details"))
            ->add('credit', TextColumn::class, array("label" => "Added"))
            ->add('debit', TextColumn::class, array("label" => "Deducted"))
            ->add('created_at', DateTimeColumn::class, array("label" => "Date", "format" => "d-m-Y"))

            ->createAdapter(ORMAdapter::class, [
                'entity' => Ledger::class,
                'query' => function (QueryBuilder $builder) use ($id) {
                    $builder
                        ->select('l')
                        ->from(Ledger::class, 'l')
                        ->where('l.user = :userId')
                        ->setParameter('userId', $id)
                    ;
                },
            ])->handleRequest($request);


        if ($table->isCallback()) {
            return $table->getResponse();
        }

        return $this->render('user/index.html.twig', array(
            'datatable' => $table
        ));
    }
    /**
     * @Route("/user/my-account", name="user_myaccount")
     * @IsGranted("ROLE_USER")
     * @param Request $request
     *
     * @return Response
     */

    public function myAccount(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $id = $this->getUser()->getId();

        $user = $this->getDoctrine()->getRepository(User::class)->find($id);

        if(!$user) {
            throw new NotFoundHttpException("%s is not associated with any user");
        }

        return $this->render("user/myaccount.html.twig", array("user" => $user));
    }
}