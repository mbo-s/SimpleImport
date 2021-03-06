<?php
/**
 * @filesource
 * @copyright (c) 2013 - 2017 Cross Solution (http://cross-solution.de)
 * @license MIT
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.30
 */
namespace SimpleImport\Hydrator;

use Zend\Hydrator\HydrationInterface;
use Jobs\Entity\AtsMode;
use SimpleImport\Job\GeocodeLocation;
use Doctrine\Common\Collections\ArrayCollection;
use SimpleImport\Hydrator\Job\ClassificationsHydrator;
use Jobs\Entity\Job;
use InvalidArgumentException;

class JobHydrator implements HydrationInterface
{
    
    /**
     * @var GeocodeLocation
     */
    private $geocodeLocation;
    
    /**
     * @var ClassificationsHydrator
     */
    private $classificationsHydrator;
    
    /**
     * @param GeocodeLocation $geocodeLocation
     * @param ClassificationsHydrator $classificationsHydrator
     */
    public function __construct(GeocodeLocation $geocodeLocation, ClassificationsHydrator $classificationsHydrator)
    {
        $this->geocodeLocation = $geocodeLocation;
        $this->classificationsHydrator = $classificationsHydrator;
    }
    
    /**
     * {@inheritDoc}
     * @see \Zend\Hydrator\HydrationInterface::hydrate()
     */
    public function hydrate(array $data, $job)
    {
        if (!$job instanceof Job) {
            throw new InvalidArgumentException(sprintf('Object must be instance of "%s"', Job::class));
        }
        
        /** @var \Jobs\Entity\Job $job */
        $job->setTitle($data['title'])
            ->setLocation($data['location'])
            ->setCompany($data['company'])
            ->setReference($data['reference'])
            ->setContactEmail($data['contactEmail'])
            ->setLanguage($data['language'])
            ->setLink($data['link'])
            ->setDatePublishStart($data['datePublishStart'])
            ->setLogoRef($data['logoRef']);

        if (isset($data['templateValues'])) {
            $templateValues = $job->getTemplateValues();
            foreach ($data['templateValues'] as $key => $value) {
                if (!$value) { continue; }
                if ('tasks' == $key) { $key = 'qualifications'; }
                $method = 'set' . $key;
                if (method_exists($templateValues, $method)) {
                    $templateValues->$method($value);
                }
            }
        }

        if ($data['datePublishEnd']) {
            $job->setDatePublishEnd($data['datePublishEnd']);
        }

        if ($data['linkApply']) {
            $job->setAtsMode(new AtsMode(AtsMode::MODE_URI, $data['linkApply']));
        } else {
            $job->setAtsMode(new AtsMode(AtsMode::MODE_NONE));
        }
        
        $locations = $this->geocodeLocation->getLocations($data['location']);
        $job->setLocations(new ArrayCollection($locations));
        
        $this->classificationsHydrator->hydrate($data['classifications'], $job->getClassifications());
    }
}
