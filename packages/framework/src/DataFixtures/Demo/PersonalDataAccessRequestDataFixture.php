<?php

namespace Shopsys\FrameworkBundle\DataFixtures\Demo;

use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\FrameworkBundle\Component\DataFixture\AbstractReferenceFixture;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Model\PersonalData\PersonalDataAccessRequest;
use Shopsys\FrameworkBundle\Model\PersonalData\PersonalDataAccessRequestData;
use Shopsys\FrameworkBundle\Model\PersonalData\PersonalDataAccessRequestFacade;

class PersonalDataAccessRequestDataFixture extends AbstractReferenceFixture
{
    const VALID_ACCESS_DISPLAY_REQUEST = 'valid_access_display_request';
    const VALID_ACCESS_EXPORT_REQUEST = 'valid_access_export_request';

    /** @var PersonalDataAccessRequestFacade */
    private $personalDataFacade;

    /**
     * @param \Shopsys\FrameworkBundle\Model\PersonalData\PersonalDataAccessRequestFacade $personalDataFacade
     */
    public function __construct(PersonalDataAccessRequestFacade $personalDataFacade)
    {
        $this->personalDataFacade = $personalDataFacade;
    }

    public function load(ObjectManager $manager)
    {
        $personalDataAccessRequestData = new PersonalDataAccessRequestData();
        $personalDataAccessRequestData->domainId = Domain::FIRST_DOMAIN_ID;
        $personalDataAccessRequestData->email = 'no-reply@netdevelo.cz';
        $personalDataAccessRequestData->hash = 'UrSqiLmCK0cdGfBuwRza';
        $personalDataAccessRequestData->type = PersonalDataAccessRequest::PERSONAL_DATA_DISPLAY;

        $personalDataAccessRequest = $this->personalDataFacade->createPersonalDataAccessRequest(
            $personalDataAccessRequestData,
            Domain::FIRST_DOMAIN_ID
        );

        $this->addReference(self::VALID_ACCESS_DISPLAY_REQUEST, $personalDataAccessRequest);

        $personalDataAccessRequestData = new PersonalDataAccessRequestData();
        $personalDataAccessRequestData->domainId = Domain::FIRST_DOMAIN_ID;
        $personalDataAccessRequestData->email = 'no-reply@netdevelo.cz';
        $personalDataAccessRequestData->hash = 'UrSqiLmCK0cdGfBuwRza';
        $personalDataAccessRequestData->type = PersonalDataAccessRequest::PERSONAL_DATA_EXPORT;

        $personalDataAccessRequest = $this->personalDataFacade->createPersonalDataAccessRequest(
            $personalDataAccessRequestData,
            Domain::FIRST_DOMAIN_ID
        );

        $this->addReference(self::VALID_ACCESS_EXPORT_REQUEST, $personalDataAccessRequest);
    }
}
