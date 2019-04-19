<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Pokemon;

/**
 * @Route("/api/pokemon")
 */
class PokemonController
{
    private $normalizer;
    private $entityManager;

    public function __construct(NormalizerInterface $normalizer, EntityManagerInterface $entityManager) {
        $this->normalizer = $normalizer;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("", methods={"GET"})
     */
    public function findAll(): JsonResponse
    {
        $pokemonRepository = $this->entityManager->getRepository(Pokemon::class);

        $pokemons = $pokemonRepository->findAll();

        $response = $this->normalizer->normalize($pokemons, 'json');

        return new JsonResponse($response);
    }

    /**
     * @Route("/{id}", methods={"GET"})
     */
    public function findOne(string $id): JsonResponse
    {
        $pokemonRepository = $this->entityManager->getRepository(Pokemon::class);

        $pokemon = $pokemonRepository->findOneById($id);

        $response = $this->normalizer->normalize($pokemon, 'json');

        return new JsonResponse($response);
    }


    /**
     * @Route("", methods={"POST"})
     */
    public function create(): JsonResponse
    {
        $pokemon = new Pokemon();
        $pokemon->setName("bulbasaur");
        $pokemon->setWeight(67);

        $this->entityManager->persist($pokemon);
        $this->entityManager->flush();

        $response = $this->normalizer->normalize($pokemon, 'json');

        return new JsonResponse($response);
    }
}
