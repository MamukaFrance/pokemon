<?php

namespace App\Controller;

use App\Entity\Pokemon;
use App\Repository\PokemonRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use PhpParser\Node\Scalar\String_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use function Symfony\Component\DependencyInjection\Loader\Configurator\param;

final class PokemonController extends AbstractController
{
    #[Route('/', name: 'pokemon_home')]
    public function home(): Response
    {
        return $this->render('pokemon/home.html.twig');
    }

    #[Route('/list', name: 'pokemon_list')]
    public function list(PokemonRepository $pokemonRepository): Response
    {
        $pokemons = $pokemonRepository->findAll();

        return $this->render('pokemon/list.html.twig', ['pokemons' => $pokemons]);
    }

    #[Route('/list/{id}', name: 'pokemon_detail', requirements: ['id' => '\d+'])]
    public function detail(Pokemon $pokemon): Response
    {
        return $this->render('pokemon/detail.html.twig', ['pokemon' => $pokemon]);
    }

    #[Route('/capture/{id}', name: 'pokemon_capture', requirements: ['id' => '\d+'])]
    public function capture(Pokemon $pokemon, PokemonRepository $pokemonRepository, EntityManagerInterface $em): Response
    {
        $pokemon->setEstCapture(!$pokemon->isEstCapture());
        $em->flush();

        $pokemons = $pokemonRepository->findAll();
        return $this->render('pokemon/list.html.twig', ['pokemons' => $pokemons]);
    }

    #[Route('/tri/{param}', name: 'pokemon_tri')]
    public function tri(string $param, PokemonRepository $pokemonRepository, EntityManagerInterface $em): Response
    {
        $pokemons = match ($param) {
            'name' => $pokemonRepository->sortByName(),
            'capture' => $pokemonRepository->sortByCapture(),
            default => $pokemonRepository->findAll(),
        };

        return $this->render('pokemon/list.html.twig', ['pokemons' => $pokemons]);
    }



}
