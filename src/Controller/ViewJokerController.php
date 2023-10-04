<?php

namespace App\Controller;

use App\Entity\Joke;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ViewJokerController extends AbstractController
{
    public function __construct(
        private readonly HttpClientInterface $client,
        private readonly ManagerRegistry $doctrine,
    ) {
    }

    #[Route('/view/joker', name: 'app_view_joker')]
    public function index(): Response
    {
        $em = $this->doctrine;
        $post = $em->getRepository(Joke::class)->findAll();

        return $this->render('view_joker/index.html.twig', [
            'jokes' => $post,
        ]);
    }
}
