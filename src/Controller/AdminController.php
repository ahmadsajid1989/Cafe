<?php


namespace App\Controller;


use App\Entity\Ledger;
use App\Entity\User;
use App\Form\LedgerEntryFormType;
use Doctrine\ORM\QueryBuilder;
use Omines\DataTablesBundle\Column\BoolColumn;
use Omines\DataTablesBundle\Column\DateTimeColumn;
use Omines\DataTablesBundle\Column\NumberColumn;
use Omines\DataTablesBundle\DataTable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTableFactory;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;


/**
 * Class AdminController
 * @package App\Controller
 * @IsGranted("ROLE_ADMIN")
 */
class AdminController extends AbstractController
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
     * @Route("/admin", name="admin_home")
     * @IsGranted("ROLE_ADMIN")
     * @param Request $request
     * @param DataTableFactory $dataTableFactory
     *
     * @return Response
     */
    public function adminDashboard(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');


        $table = $this->createDataTable()
            ->add('name', TextColumn::class, array("label" => "Name"))
            ->add('phone', TextColumn::class, array("label" => "Phone"))
            ->add('email', TextColumn::class, array("label" => "Email"))
            ->add('bongo_id', NumberColumn::class, array("label" => "ID Card No"))
            ->add('department',TextColumn::class, array("label" => "Department"))
            ->add('status', BoolColumn::class, array("label" => "Account Status",'trueValue' => 'active',
                'falseValue' => 'in active',))
            ->add('id', TextColumn::class, ['label' => 'Action','render' => function($value, $context) {
                return sprintf('<a class="btn btn-info" href="%s">Add Balance</a> <a class="btn btn-primary" href="%s">Show Balance</a>', $this->generateUrl('admin_add_balance',['user_id' =>$value]),$this->generateUrl('admin_accounts',['id' =>$value])); }])

            ->createAdapter(ORMAdapter::class, [
                'entity' => User::class
            ])
            ->handleRequest($request);


        if ($table->isCallback()) {
            return $table->getResponse();
        }

        return $this->render('admin/index.html.twig', array(
            'datatable' => $table
        ));

    }

    /**
     * @Route("/admin/accounts/{id}", name="admin_accounts")
     * @IsGranted("ROLE_ADMIN")
     * @param Request $request
     * @param DataTableFactory $dataTableFactory
     *
     * @return Response
     */
    public function adminUserAccount(Request $request, $id): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');


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
            ])

//        $table->addEventListener(ORMAdapterEvents::PRE_QUERY, function(ORMAdapterQueryEvent $event) {
//            $event->getQuery()->useResultCache(true)->useQueryCache(true);
//        })

       ->handleRequest($request);


        if ($table->isCallback()) {
            return $table->getResponse();
        }

        return $this->render('admin/index.html.twig', array(
            'datatable' => $table
        ));

    }

    /**
     * @Route("/admin/add/{user_id}", name="admin_add_balance")
     * @IsGranted("ROLE_ADMIN")
     * @param Request $request
     *
     * @return Response
     */

    public function addFund(Request $request, $user_id):Response
    {
        $ledger_entry = new Ledger();
        $form = $this->createForm(LedgerEntryFormType::class, $ledger_entry);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            /** @var  $user User*/
            $user  = $entityManager->getRepository(User::class)->find($user_id);

            $ledger_entry->setName($user->getName());
            $ledger_entry->setUser($user);
            $ledger_entry->setDescription(Ledger::CASH_PAYMENT);

            $entityManager->persist($ledger_entry);
            $entityManager->flush();

            $this->addFlash(
                'notice',
                'Fund Successfully Added'
            );

            return $this->redirectToRoute('admin_home');
        }

        return $this->render('ledger/fund.html.twig', [
            'form' => $form->createView(),
        ]);
    }


}