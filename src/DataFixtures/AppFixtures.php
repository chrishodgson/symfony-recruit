<?php

namespace App\DataFixtures;

use App\Entity\Activity;
use App\Entity\Company;
use App\Entity\Contact;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

class AppFixtures extends Fixture
{
    private const NUM_COMPANIES = 5;
    private const MAX_CONTACTS_PER_COMPANY = 2;
    private const MAX_ACTIVITIES_PER_CONTACT = 7;

    /**
     * @var Generator
     */
    private $faker;

    /**
     * @var ObjectManager
     */
    private $manager;

    public function load(ObjectManager $manager)
    {
        $this->faker = Factory::create('en_GB');
        $this->manager = $manager;
        for ($i = 0; $i <= self::NUM_COMPANIES; $i++) {
            $this->createCompany();
        }
        $this->manager->flush();
    }

    private function createCompany()
    {
        $company = new Company();
        $company->setName($this->faker->company);
        $company->setType(mt_rand(1, 3));
        if (mt_rand(0, 1)) {
            $company->setLocation($this->faker->city);
        }
        if (mt_rand(0, 1)) {
            $company->setDetails($this->faker->realText(50));
        }
        $this->manager->persist($company);

        $numContacts = mt_rand(1, self::MAX_CONTACTS_PER_COMPANY);
        for ($i = 0; $i <= $numContacts; $i++) {
            $this->createContact($company);
        }
    }

    private function createContact(Company $company)
    {
        $contact = new Contact();
        $contact->setCompany($company);
        $contact->setName($this->faker->name);
        if (mt_rand(0, 1)) {
            $contact->setEmail($this->faker->email);
        }
        if (mt_rand(0, 1)) {
            $contact->setLandline($this->faker->phoneNumber);
        }
        if (mt_rand(0, 1)) {
            $contact->setMobile($this->faker->phoneNumber);
        }
        if (mt_rand(0, 1)) {
            $contact->setLinkedIn($this->faker->url);
        }
        if (mt_rand(0, 1)) {
            $contact->setRole($this->faker->jobTitle);
        }
        if (mt_rand(0, 1)) {
            $contact->setNotifyWhenAvailable(1);
        }
        if (mt_rand(0, 1)) {
            $contact->setDetails($this->faker->realText(100));
        }
        $this->manager->persist($contact);

        if (mt_rand(0, 3)) {
            $numActivities = mt_rand(1, self::MAX_ACTIVITIES_PER_CONTACT);
            for ($i = 0; $i <= $numActivities; $i++) {
                $this->createActivity($contact);
            }
        }
    }

    private function createActivity(Contact $contact)
    {
        $activity = new Activity();
        $activity->setContact($contact);
        $activity->setType(mt_rand(1, 4));
        if (1 == $activity->getType()) {
            $activity->setTranscript($this->faker->realText(100));
        }
        $activity->setSummary($this->faker->realText(20));
        $dateTime = new \DateTime();
        if (mt_rand(0, 20)) {
            $dateTime->modify('-' . mt_rand(1, 50) . ' day');
        }
        $activity->setCreatedAt($dateTime);
        $this->manager->persist($activity);
    }
}