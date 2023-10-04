<?php

namespace App\Controller;

use App\Entity\Joke;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class JokeController extends AbstractController
{
    public function __construct(
        private readonly HttpClientInterface $client,
        private readonly ManagerRegistry $doctrine,
        private readonly EntityManagerInterface $entityManager,
        private string $valueJoke = '',
    ) {
    }

    #[Route('/joke/{id}', name: 'joke')]
    public function index($id): Response
    {
        $content = 0;

        if ($id == 0) {
            $content = $this->getRandomJoke();
        } else {
            $content = $this->getJoke($id);
        }

        $this->valueJoke = $content['value'];

        $joke = new Joke();
        $joke->setUrl($content['url']);
        $joke->setIconUrl($content['icon_url']);
        $joke->setValue($this->valueJoke);

        $this->submitJoke($this->entityManager, $joke);

        return $this->render('joke/index.html.twig', [
            'controller_name' => 'JokeController',
            'joke' => $this->valueJoke
        ]);
    }

    public function getJoke($category): array
    {
        $response = $this->client->request(
            'GET',
            'https://api.chucknorris.io/jokes/random?category=' . $category
        );

        $statusCode = $response->getStatusCode();
        $contentType = $response->getHeaders()['content-type'][0];
        $content = $response->getContent();
        $content = $response->toArray();

        return $content;
    }

    private function getRandomJoke(): array
    {
        $response = $this->client->request(
            'GET',
            'https://api.chucknorris.io/jokes/random'
        );

        $statusCode = $response->getStatusCode();
        $contentType = $response->getHeaders()['content-type'][0];
        $content = $response->getContent();
        $content = $response->toArray();

        return $content;
    }

    private function submitJoke(EntityManagerInterface $entityManager, $joke): void
    {
        $entityManager->persist($joke);
        $entityManager->flush();
    }
}
