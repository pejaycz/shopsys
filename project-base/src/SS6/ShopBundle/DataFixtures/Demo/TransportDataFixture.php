<?php

namespace SS6\ShopBundle\DataFixtures\Demo;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use SS6\ShopBundle\Model\Transport\Transport;
use SS6\ShopBundle\Model\Transport\TransportData;

class TransportDataFixture extends AbstractFixture {

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 */
	public function load(ObjectManager $manager) {
		$this->createTransport($manager, 'transport_cp', 'Česká pošta - balík do ruky', 99.95, 'Pouze na vlastní nebezpečí');
		$this->createTransport($manager, 'transport_ppl', 'PPL', 199.95, null);
		$this->createTransport($manager, 'transport_personal', 'Osobní převzetí', 0, 'Uvítá Vás milý personál!');
		$manager->flush();
	}
	
	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 * @param string $referenceName
	 * @param string $name
	 * @param string $price
	 * @param string|null $description
	 * @param boolean $hide
	 */
	private function createTransport(ObjectManager $manager, $referenceName, $name, $price, $description, $hide = false) {
		$transport = new Transport(new TransportData($name, $price, $description, $hide));
		$manager->persist($transport);
		$this->addReference($referenceName, $transport);
	}

}
