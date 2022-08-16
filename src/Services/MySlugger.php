<?php

namespace App\Services;

use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class MySlugger
{
    /**
    * instance de SluggerInterface
    *
    * @var SluggerInterface
    */
    private $slugger;

    /**
    * Paramétrage du service : active le lower()
    *
    * @var bool
    */
    private $paramLower;
        
    /**
    * Constructor
    */
    public function __construct(SluggerInterface $slugger, ContainerBagInterface $params)
    {
        $this->slugger = $slugger;
        $valeurServiceYaml = $params->get('myslugger.lower');
        $this->paramLower = ($valeurServiceYaml === 'true');
    }

    /**
     * renvoit le slug d'un titre de film
     *
     * @param string $titre
     * @return string titre sluggifié
     */
    public function slug(string $titre): string
    {  
        $slug = $this->slugger->slug($titre);

        if ($this->paramLower) {
            // @link https://symfony.com/doc/current/components/string.html#methods-to-change-case
            $slug = $slug->lower();
        }

        return $slug;
    }
}