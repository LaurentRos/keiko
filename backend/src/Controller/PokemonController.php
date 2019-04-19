<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Pokemon;

/**
 * @Route("/api/pokemon")
 */
class PokemonController
{
    private $normalizer;
    private $entityManager;
    private $serializer;
    private $validator;

    public function __construct(
        NormalizerInterface $normalizer,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    )
    {
        $this->normalizer = $normalizer;
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
        $this->validator = $validator;
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
    public function create(Request $request): JsonResponse
    {
        $pokemon = $this->serializer->deserialize($request->getContent(), Pokemon::class, 'json');

        $violations = $this->validator->validate($pokemon);
        if (0 !== count($violations)) {
           throw new BadRequestHttpException($violations);
        }

        $this->entityManager->persist($pokemon);
        $this->entityManager->flush();

        $response = $this->normalizer->normalize($pokemon, 'json');

        return new JsonResponse($response);
    }
}
