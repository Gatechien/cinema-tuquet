<?php

namespace App\Command;

use App\Repository\MovieRepository;
use App\Services\OmdbApi;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class OmdbFetchPosterCommand extends Command
{
    protected static $defaultName = 'omdb:fetch:poster';
    protected static $defaultDescription = 'Utilise l\'API OMDB pour actualiser les poster de la BDD';
/**
    * Service OmdbApi
    *
    * @var OmdbApi
    */
    private $omdbapi;
    
    /**
    * Service Repository pour les Movie
    *
    * @var MovieRepository
    */
    private $movieRepository;
    
    /**
    * Service ManagerRegistry
    *
    * @var ManagerRegistry
    */
    private $managerRegistry;
    

    /**
    * Constructor
    */
    public function __construct(OmdbApi $omdbapi, MovieRepository $movieRepository, ManagerRegistry $registry)
    {
        parent::__construct();

        $this->omdbapi = $omdbapi;
        $this->movieRepository = $movieRepository;
        $this->managerRegistry = $registry;
    }   
    
    protected function configure(): void
    {
        
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        $allMovie = $this->movieRepository->findAll();
        $io->progressStart(count($allMovie));

        foreach ($allMovie as $movie) {

            $newPoster = $this->omdbapi->fetchPoster($movie->getTitle());
            $movie->setPoster($newPoster);
            $io->progressAdvance();
        }

        $manager = $this->managerRegistry->getManager();
        $manager->flush();
        $output->writeln(['']);
        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }
}
