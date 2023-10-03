<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CategoriesController extends AbstractController
{
    public function __construct(
        private readonly HttpClientInterface $client,
        public array $categories = array(),
    ) {
    }

    #[Route('/', name: 'app_categories')]
    public function index(): Response
    {
        $this->getNewJoke();

        return $this->render('categories/index.html.twig', [
            'controller_name' => 'David',
            'categories' => $this->categories
        ]);
    }

    public function getNewJoke() {
        $this->categories = $this->getCategories();
    }

    /**
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     */
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

    private function getCategories(): array
    {
        $response = $this->client->request(
            'GET',
            'https://api.chucknorris.io/jokes/categories'
        );

        $statusCode = $response->getStatusCode();
        $contentType = $response->getHeaders()['content-type'][0];
        $content = $response->getContent();
        $content = $response->toArray();

        return $content;
    }
}
