<?php

namespace App\Serializer;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class DoctrineDenormalizer implements DenormalizerInterface
{

    /**
    * Instance de EntityManagerInterface
    * @var EntityManagerInterface
    */
    private $entityManagerInterface;
    
    /**
    * Constructor
    */
    public function __construct(EntityManagerInterface $entityManagerInterface)
    {
        $this->entityManagerInterface = $entityManagerInterface;
    }

    /**
     * @param [type] $data
     * @param string $type
     * @param string|null $format
     */
    public function supportsDenormalization($data, string $type, ?string $format = null)
    {
        $dataIsId = is_numeric($data);
        $typeIsEntity = strpos($type, 'App\Entity') === 0;
        return $typeIsEntity && $dataIsId;
    }

    /**
     * @param [type] $data
     * @param string $type
     * @param string|null $format
     * @param array $context
     */
    public function denormalize($data, string $type, ?string $format = null, array $context = [])
    {
        $denormalizedEntity = $this->entityManagerInterface->find($type, $data);
        return $denormalizedEntity;
    }
}