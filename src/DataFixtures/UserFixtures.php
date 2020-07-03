<?php

namespace App\DataFixtures;

use App\Entity\Role;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
	private $encoder;

	public function __construct(UserPasswordEncoderInterface $encoder){

		$this->encoder = $encoder;
	}

    public function load(ObjectManager $manager)
    {

		$adminRole = new Role();
		$adminRole->setTitle('ROLE_ADMIN');
		$manager->persist($adminRole);

		$adminUser = new User();
		$adminUser->setEmail('elisee.reclus@webcoop.fr');
		$adminUser->setPassword($this->encoder->encodePassword($adminUser, 'Prenons+Conscience'));
		$adminUser->addUserRole($adminRole);

		$commonUser1 = new User();
		$commonUser1->setEmail('allan@webcoop.fr');
		$commonUser1->setPassword($this->encoder->encodePassword($commonUser1, 'Prenons+Conscience'));

		$commonUser2 = new User();
		$commonUser2->setEmail('marc.anglaret@free.fr');
		$commonUser2->setPassword($this->encoder->encodePassword($commonUser2, 'VivaZapata+'));

		$manager->persist($commonUser1);
		$manager->persist($commonUser2);
		$manager->persist($adminUser);

		$manager->flush();
		
    }
}
