<?php

namespace App\DataFixtures;

use App\Entity\Axe1;
use App\Entity\Axe2;
use App\Entity\Axe3;
use App\Entity\Configuration;
use App\Entity\Conge;
use App\Entity\Periode;
use App\Entity\Section;
use App\Entity\TypeConge;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $this->loadSections($manager);
        $this->loadTypesConge($manager);
        $this->loadConfiguration($manager);
        $this->loadExampleData($manager);

        $manager->flush();
    }

    private function loadSections(ObjectManager $manager): void
    {
        $sectionsData = [
            ['code' => 'PROD', 'libelle' => 'Production', 'ordre' => 1],
            ['code' => 'RD', 'libelle' => 'Recherche et Developpement', 'ordre' => 2],
            ['code' => 'ADMIN', 'libelle' => 'Administratif', 'ordre' => 3],
            ['code' => 'COMM', 'libelle' => 'Commercial', 'ordre' => 4],
            ['code' => 'FORM', 'libelle' => 'Formation', 'ordre' => 5],
        ];

        foreach ($sectionsData as $data) {
            $section = new Section();
            $section->setCode($data['code']);
            $section->setLibelle($data['libelle']);
            $section->setOrdre($data['ordre']);
            $section->setActif(true);
            $manager->persist($section);
            $this->addReference('section_' . $data['code'], $section);
        }

        $manager->flush();

        // Axes 1 pour Production
        $axes1Prod = [
            ['code' => 'DEV', 'libelle' => 'Developpement', 'ordre' => 1],
            ['code' => 'MAINT', 'libelle' => 'Maintenance', 'ordre' => 2],
            ['code' => 'SUPP', 'libelle' => 'Support client', 'ordre' => 3],
        ];

        $sectionProd = $this->getReference('section_PROD', Section::class);
        foreach ($axes1Prod as $data) {
            $axe1 = new Axe1();
            $axe1->setCode($data['code']);
            $axe1->setLibelle($data['libelle']);
            $axe1->setOrdre($data['ordre']);
            $axe1->setSection($sectionProd);
            $axe1->setActif(true);
            $manager->persist($axe1);
            $this->addReference('axe1_' . $data['code'], $axe1);
        }

        // Axes 1 pour R&D
        $axes1RD = [
            ['code' => 'RECH', 'libelle' => 'Recherche', 'ordre' => 1],
            ['code' => 'PROTO', 'libelle' => 'Prototypage', 'ordre' => 2],
            ['code' => 'TEST', 'libelle' => 'Tests', 'ordre' => 3],
        ];

        $sectionRD = $this->getReference('section_RD', Section::class);
        foreach ($axes1RD as $data) {
            $axe1 = new Axe1();
            $axe1->setCode($data['code']);
            $axe1->setLibelle($data['libelle']);
            $axe1->setOrdre($data['ordre']);
            $axe1->setSection($sectionRD);
            $axe1->setActif(true);
            $manager->persist($axe1);
            $this->addReference('axe1_rd_' . $data['code'], $axe1);
        }

        // Axes 1 pour Administratif
        $axes1Admin = [
            ['code' => 'COMPTA', 'libelle' => 'Comptabilite', 'ordre' => 1],
            ['code' => 'RH', 'libelle' => 'Ressources humaines', 'ordre' => 2],
            ['code' => 'DIR', 'libelle' => 'Direction', 'ordre' => 3],
        ];

        $sectionAdmin = $this->getReference('section_ADMIN', Section::class);
        foreach ($axes1Admin as $data) {
            $axe1 = new Axe1();
            $axe1->setCode($data['code']);
            $axe1->setLibelle($data['libelle']);
            $axe1->setOrdre($data['ordre']);
            $axe1->setSection($sectionAdmin);
            $axe1->setActif(true);
            $manager->persist($axe1);
            $this->addReference('axe1_admin_' . $data['code'], $axe1);
        }

        $manager->flush();

        // Axes 2 pour Developpement
        $axes2Dev = [
            ['code' => 'FRONT', 'libelle' => 'Frontend', 'ordre' => 1],
            ['code' => 'BACK', 'libelle' => 'Backend', 'ordre' => 2],
            ['code' => 'INFRA', 'libelle' => 'Infrastructure', 'ordre' => 3],
        ];

        $axe1Dev = $this->getReference('axe1_DEV', Axe1::class);
        foreach ($axes2Dev as $data) {
            $axe2 = new Axe2();
            $axe2->setCode($data['code']);
            $axe2->setLibelle($data['libelle']);
            $axe2->setOrdre($data['ordre']);
            $axe2->setAxe1($axe1Dev);
            $axe2->setActif(true);
            $manager->persist($axe2);
            $this->addReference('axe2_' . $data['code'], $axe2);
        }

        $manager->flush();

        // Axes 3 pour Frontend
        $axes3Front = [
            ['code' => 'REACT', 'libelle' => 'React', 'ordre' => 1],
            ['code' => 'VUE', 'libelle' => 'Vue.js', 'ordre' => 2],
            ['code' => 'CSS', 'libelle' => 'CSS/Design', 'ordre' => 3],
        ];

        $axe2Front = $this->getReference('axe2_FRONT', Axe2::class);
        foreach ($axes3Front as $data) {
            $axe3 = new Axe3();
            $axe3->setCode($data['code']);
            $axe3->setLibelle($data['libelle']);
            $axe3->setOrdre($data['ordre']);
            $axe3->setAxe2($axe2Front);
            $axe3->setActif(true);
            $manager->persist($axe3);
            $this->addReference('axe3_' . $data['code'], $axe3);
        }

        // Axes 3 pour Backend
        $axes3Back = [
            ['code' => 'API', 'libelle' => 'API REST', 'ordre' => 1],
            ['code' => 'BDD', 'libelle' => 'Base de donnees', 'ordre' => 2],
            ['code' => 'SECU', 'libelle' => 'Securite', 'ordre' => 3],
        ];

        $axe2Back = $this->getReference('axe2_BACK', Axe2::class);
        foreach ($axes3Back as $data) {
            $axe3 = new Axe3();
            $axe3->setCode($data['code']);
            $axe3->setLibelle($data['libelle']);
            $axe3->setOrdre($data['ordre']);
            $axe3->setAxe2($axe2Back);
            $axe3->setActif(true);
            $manager->persist($axe3);
            $this->addReference('axe3_back_' . $data['code'], $axe3);
        }

        $manager->flush();
    }

    private function loadTypesConge(ObjectManager $manager): void
    {
        $typesData = [
            ['code' => 'CP', 'libelle' => 'Conges payes', 'decompte' => true, 'couleur' => '#22c55e'],
            ['code' => 'RTT', 'libelle' => 'RTT', 'decompte' => true, 'couleur' => '#3b82f6'],
            ['code' => 'RECUP', 'libelle' => 'Recuperation', 'decompte' => true, 'couleur' => '#8b5cf6'],
            ['code' => 'MAL', 'libelle' => 'Maladie', 'decompte' => false, 'couleur' => '#ef4444'],
            ['code' => 'SANS', 'libelle' => 'Sans solde', 'decompte' => false, 'couleur' => '#6b7280'],
            ['code' => 'FORM', 'libelle' => 'Formation', 'decompte' => false, 'couleur' => '#f59e0b'],
            ['code' => 'FERIE', 'libelle' => 'Jour ferie', 'decompte' => false, 'couleur' => '#ec4899'],
        ];

        foreach ($typesData as $data) {
            $type = new TypeConge();
            $type->setCode($data['code']);
            $type->setLibelle($data['libelle']);
            $type->setDecompte($data['decompte']);
            $type->setCouleur($data['couleur']);
            $type->setActif(true);
            $manager->persist($type);
            $this->addReference('type_conge_' . $data['code'], $type);
        }

        $manager->flush();
    }

    private function loadConfiguration(ObjectManager $manager): void
    {
        $configData = [
            ['cle' => 'heures_jour', 'valeur' => '7', 'description' => 'Nombre d\'heures par jour travaille'],
            ['cle' => 'heures_semaine', 'valeur' => '35', 'description' => 'Nombre d\'heures par semaine'],
            ['cle' => 'cp_annuels', 'valeur' => '25', 'description' => 'Nombre de jours de CP annuels'],
            ['cle' => 'rtt_annuels', 'valeur' => '10', 'description' => 'Nombre de jours de RTT annuels'],
            ['cle' => 'validation_obligatoire', 'valeur' => '1', 'description' => 'Validation des conges obligatoire'],
            ['cle' => 'axe1_obligatoire', 'valeur' => '1', 'description' => 'Axe 1 obligatoire lors de la saisie'],
            ['cle' => 'axe2_obligatoire', 'valeur' => '0', 'description' => 'Axe 2 obligatoire lors de la saisie'],
            ['cle' => 'axe3_obligatoire', 'valeur' => '0', 'description' => 'Axe 3 obligatoire lors de la saisie'],
        ];

        foreach ($configData as $data) {
            $config = new Configuration();
            $config->setCle($data['cle']);
            $config->setValeur($data['valeur']);
            $config->setDescription($data['description']);
            $manager->persist($config);
        }

        $manager->flush();
    }

    private function loadExampleData(ObjectManager $manager): void
    {
        $user = $this->getReference(UserFixtures::USER_REFERENCE, User::class);
        $sectionProd = $this->getReference('section_PROD', Section::class);
        $axe1Dev = $this->getReference('axe1_DEV', Axe1::class);
        $axe2Back = $this->getReference('axe2_BACK', Axe2::class);

        // Periodes de la semaine derniere
        $today = new \DateTimeImmutable();
        $monday = $today->modify('monday this week');

        for ($i = 0; $i < 5; $i++) {
            $date = $monday->modify("+$i days");

            // Matin
            $periode1 = new Periode();
            $periode1->setUser($user);
            $periode1->setDate($date);
            $periode1->setHeureDebut(new \DateTimeImmutable('09:00'));
            $periode1->setHeureFin(new \DateTimeImmutable('12:00'));
            $periode1->setSection($sectionProd);
            $periode1->setAxe1($axe1Dev);
            $periode1->setAxe2($axe2Back);
            $periode1->setValidee(true);
            $manager->persist($periode1);

            // Apres-midi
            $periode2 = new Periode();
            $periode2->setUser($user);
            $periode2->setDate($date);
            $periode2->setHeureDebut(new \DateTimeImmutable('14:00'));
            $periode2->setHeureFin(new \DateTimeImmutable('18:00'));
            $periode2->setSection($sectionProd);
            $periode2->setAxe1($axe1Dev);
            $periode2->setAxe2($axe2Back);
            $periode2->setValidee(true);
            $manager->persist($periode2);
        }

        // Conges d'exemple
        $typeCP = $this->getReference('type_conge_CP', TypeConge::class);

        $conge = new Conge();
        $conge->setUser($user);
        $conge->setType($typeCP);
        $conge->setDateDebut(new \DateTimeImmutable('2026-08-01'));
        $conge->setDateFin(new \DateTimeImmutable('2026-08-15'));
        $conge->setNbJours('11');
        $conge->setStatut(Conge::STATUT_VALIDE);
        $conge->setCommentaire('Vacances d\'ete');
        $manager->persist($conge);

        $manager->flush();
    }
}
