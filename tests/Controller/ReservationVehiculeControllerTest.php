<?php

namespace App\Tests\Controller;

use App\Entity\ReservationVehicule;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class ReservationVehiculeControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $reservationVehiculeRepository;
    private string $path = '/res/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->reservationVehiculeRepository = $this->manager->getRepository(ReservationVehicule::class);

        foreach ($this->reservationVehiculeRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('ReservationVehicule index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'reservation_vehicule[date_debut]' => 'Testing',
            'reservation_vehicule[date_fin]' => 'Testing',
            'reservation_vehicule[prix_total]' => 'Testing',
            'reservation_vehicule[status]' => 'Testing',
            'reservation_vehicule[id_vehicule]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->reservationVehiculeRepository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new ReservationVehicule();
        $fixture->setDate_debut('My Title');
        $fixture->setDate_fin('My Title');
        $fixture->setPrix_total('My Title');
        $fixture->setStatus('My Title');
        $fixture->setId_vehicule('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('ReservationVehicule');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new ReservationVehicule();
        $fixture->setDate_debut('Value');
        $fixture->setDate_fin('Value');
        $fixture->setPrix_total('Value');
        $fixture->setStatus('Value');
        $fixture->setId_vehicule('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'reservation_vehicule[date_debut]' => 'Something New',
            'reservation_vehicule[date_fin]' => 'Something New',
            'reservation_vehicule[prix_total]' => 'Something New',
            'reservation_vehicule[status]' => 'Something New',
            'reservation_vehicule[id_vehicule]' => 'Something New',
        ]);

        self::assertResponseRedirects('/res/');

        $fixture = $this->reservationVehiculeRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getDate_debut());
        self::assertSame('Something New', $fixture[0]->getDate_fin());
        self::assertSame('Something New', $fixture[0]->getPrix_total());
        self::assertSame('Something New', $fixture[0]->getStatus());
        self::assertSame('Something New', $fixture[0]->getId_vehicule());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new ReservationVehicule();
        $fixture->setDate_debut('Value');
        $fixture->setDate_fin('Value');
        $fixture->setPrix_total('Value');
        $fixture->setStatus('Value');
        $fixture->setId_vehicule('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/res/');
        self::assertSame(0, $this->reservationVehiculeRepository->count([]));
    }
}
