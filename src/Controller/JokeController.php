<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class JokeController extends AbstractController
{
    public function __construct(
        private HttpClientInterface $client,
        private string $valueJoke = '',
    ) {
    }

    #[Route('/joke/{id}', name: 'joke')]
    public function index($id): Response
    {
        $this->getJoke($id);

        return $this->render('joke/index.html.twig', [
            'controller_name' => 'JokeController',
            'id' => $id,
            'joke' => $this->valueJoke
        ]);
    }

    public function getJoke($category)
    {
        $response = $this->client->request(
            'GET',
            'https://api.chucknorris.io/jokes/random?category=' . $category
        );

        $statusCode = $response->getStatusCode();
        $contentType = $response->getHeaders()['content-type'][0];
        $content = $response->getContent();
        $content = $response->toArray();

        $this->valueJoke = $content['value'];

        return $content;
    }
}
