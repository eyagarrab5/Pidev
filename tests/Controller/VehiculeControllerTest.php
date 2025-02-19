<?php

namespace App\Tests\Controller;

use App\Entity\Vehicule;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class VehiculeControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $vehiculeRepository;
    private string $path = '/vehicule/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->vehiculeRepository = $this->manager->getRepository(Vehicule::class);

        foreach ($this->vehiculeRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Vehicule index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'vehicule[type_vehicule]' => 'Testing',
            'vehicule[modele]' => 'Testing',
            'vehicule[role]' => 'Testing',
            'vehicule[prix_par_heure]' => 'Testing',
            'vehicule[prix_par_jour]' => 'Testing',
            'vehicule[disponibilite]' => 'Testing',
            'vehicule[lieu_retrait]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->vehiculeRepository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Vehicule();
        $fixture->setType_vehicule('My Title');
        $fixture->setModele('My Title');
        $fixture->setRole('My Title');
        $fixture->setPrix_par_heure('My Title');
        $fixture->setPrix_par_jour('My Title');
        $fixture->setDisponibilite('My Title');
        $fixture->setLieu_retrait('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Vehicule');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Vehicule();
        $fixture->setType_vehicule('Value');
        $fixture->setModele('Value');
        $fixture->setRole('Value');
        $fixture->setPrix_par_heure('Value');
        $fixture->setPrix_par_jour('Value');
        $fixture->setDisponibilite('Value');
        $fixture->setLieu_retrait('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'vehicule[type_vehicule]' => 'Something New',
            'vehicule[modele]' => 'Something New',
            'vehicule[role]' => 'Something New',
            'vehicule[prix_par_heure]' => 'Something New',
            'vehicule[prix_par_jour]' => 'Something New',
            'vehicule[disponibilite]' => 'Something New',
            'vehicule[lieu_retrait]' => 'Something New',
        ]);

        self::assertResponseRedirects('/vehicule/');

        $fixture = $this->vehiculeRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getType_vehicule());
        self::assertSame('Something New', $fixture[0]->getModele());
        self::assertSame('Something New', $fixture[0]->getRole());
        self::assertSame('Something New', $fixture[0]->getPrix_par_heure());
        self::assertSame('Something New', $fixture[0]->getPrix_par_jour());
        self::assertSame('Something New', $fixture[0]->getDisponibilite());
        self::assertSame('Something New', $fixture[0]->getLieu_retrait());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Vehicule();
        $fixture->setType_vehicule('Value');
        $fixture->setModele('Value');
        $fixture->setRole('Value');
        $fixture->setPrix_par_heure('Value');
        $fixture->setPrix_par_jour('Value');
        $fixture->setDisponibilite('Value');
        $fixture->setLieu_retrait('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/vehicule/');
        self::assertSame(0, $this->vehiculeRepository->count([]));
    }
}
