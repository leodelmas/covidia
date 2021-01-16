<?php

namespace App\DataFixtures;

use App\Entity\Utilisateur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UtilisateurFixtures extends Fixture
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    /**
     * UserFixtures constructor.
     * @param UserPasswordEncoderInterface $encoder
     */
    public function __construct(UserPasswordEncoderInterface $encoder) {
        $this->encoder = $encoder;
    }

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $utilisateur = new Utilisateur();
        $utilisateur
            ->setNom("admin")
            ->setPrenom("admin")
            ->setEmail("admin@admin.com")
            ->setMotDePasse('admin')
            ->setDateNaissance(new \DateTime())
            ->setDateEmbauche(new \DateTime())
            ->setTelephone('0000000000')
            ->setEstCadre(true);

        $manager->persist($utilisateur);
        $manager->flush();
    }
}
