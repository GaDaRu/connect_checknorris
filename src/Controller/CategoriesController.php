<?php

namespace App\Controller;

use App\Entity\Joke;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use function Sodium\add;

class CategoriesController extends AbstractController
{
    public function __construct(
        private readonly HttpClientInterface $client,
        public array $categories = array(),
        public array $categoriesUpper = array(),
    ) {
    }

    #[Route('/', name: 'app_categories')]
    public function index(): Response
    {
        $this->getAllCategories();

        return $this->render('categories/index.html.twig', [
            'categories' => $this->categories,
            'categoriesUpperCase' => $this->categoriesUpper,
        ]);
    }

    public function getAllCategories(): void
    {
        $array = array();

        $this->categoriesUpper = $this->getCategories();

        foreach ($this->categoriesUpper as $category) {
            array_push($array, ucfirst($category));
        }

        $this->categories = $array;
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
