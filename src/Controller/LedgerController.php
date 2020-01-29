<?php


namespace App\Controller;

use App\Entity\Ledger;
use App\Entity\User;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class LedgerController extends AbstractController
{

    /**
     * @Route("/api/ledger/posting/", name="deduct_posting", methods={"POST"})
     * @param $request Request
     *
     * @param LoggerInterface $logger
     *
     * @return Response
     * @throws \Exception
     */
    public function deductFromBalance(Request $request, LoggerInterface $logger): Response
    {
        $padded_bongo_id = $request->getContent();
        $bongo_id= hexdec(substr($padded_bongo_id,4,6));
        $logger->info(sprintf("received %s from RFID",$bongo_id));
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['bongo_id' => $bongo_id]);

        if (!$user) {
            $logger->error(sprintf("can't find %s, this user",$bongo_id));
            return new Response("Can't find user", 400);
        }

        $em = $this->getDoctrine()->getManager();

        if($this->getDoctrine()->getRepository(Ledger::class)->isEligibleToCharge($user)) {

            $balance = $this->getDoctrine()->getRepository(Ledger::class)->getBalance($user);

            if ($balance == NULL OR $balance > 75) {

                $ledger = new Ledger();
                $ledger->setName($user->getName());
                $ledger->setDescription(Ledger::RFID_CHARGE);
                $ledger->setDebit(75);
                $ledger->setCreatedAt(new \DateTime('now'));
                $ledger->setUser($user);
                $em->persist($ledger);
                $em->flush();
            }

            return new Response('ok',200);
        }

        return new Response('error', 400);

    }

}