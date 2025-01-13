<?php

declare(strict_types=1);

namespace App\Repository;

use App\DataTransferObject\JobFilterDto;
use App\Entity\JobAdvertisement;
use App\Enum\Job\EmploymentTypeEnum;
use App\Enum\Job\PaymentFrequencyEnum;
use Carbon\CarbonImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<JobAdvertisement>
 */
class JobAdvertisementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, JobAdvertisement::class);
    }

    public function save(JobAdvertisement $entity, bool $flush = false): void
    {
        $this->getEntityManager()
            ->persist($entity);

        if ($flush) {
            $this->getEntityManager()
                ->flush();
        }
    }

    public function remove(JobAdvertisement $entity, bool $flush = false): void
    {
        $this->getEntityManager()
            ->remove($entity);

        if ($flush) {
            $this->getEntityManager()
                ->flush();
        }
    }

    /**
     * @param JobFilterDto $jobsFilterDto
     * @param bool $isQuery
     * @return Query|array<int, JobAdvertisement>
     */
    public function findByFilter(JobFilterDto $jobsFilterDto, bool $isQuery = false): Query|array
    {
        $qb = $this->createQueryBuilder('job_advertisement');

        if (!empty($jobsFilterDto->getKeyword())) {
            $qb->andWhere(
                $qb->expr()->like('job_advertisement.title', ':title')
            )->setParameter('title', '%' . $jobsFilterDto->getKeyword() . '%');
        }

        if ($jobsFilterDto->getEmploymentTypeEnum() instanceof EmploymentTypeEnum) {
            $qb->andWhere(
                $qb->expr()->eq('job_advertisement.employmentTypeEnum', ':employmentTypeEnum')
            )->setParameter('employmentTypeEnum', $jobsFilterDto->getEmploymentTypeEnum());
        }

        if (!empty($jobsFilterDto->getSalary())) {
            $qb->andWhere(
                $qb->expr()->lte('job_advertisement.salary', ':salary')
            )->setParameter('salary', $jobsFilterDto->getSalary());
        }

        if (!empty($jobsFilterDto->getCurrency())) {
            $qb->andWhere(
                $qb->expr()->eq('job_advertisement.currency', ':currency')
            )->setParameter('currency', $jobsFilterDto->getCurrency());
        }

        if ($jobsFilterDto->getPaymentFrequencyEnum() instanceof PaymentFrequencyEnum) {
            $qb->andWhere(
                $qb->expr()->eq('job_advertisement.paymentFrequencyEnum', ':paymentFrequencyEnum')
            )->setParameter('paymentFrequencyEnum', $jobsFilterDto->getPaymentFrequencyEnum());
        }

        if (!empty($jobsFilterDto->getCountry())) {
            $qb->andWhere(
                $qb->expr()->eq('job_advertisement.country', ':country')
            )->setParameter('country', $jobsFilterDto->getCountry());
        }

        if (!empty($jobsFilterDto->getCity())) {
            $qb->andWhere(
                $qb->expr()->like('job_advertisement.city', ':city')
            )->setParameter('city', '%' . $jobsFilterDto->getCity() . '%');
        }

        $qb->andWhere(
            $qb->expr()->lte('job_advertisement.publishedAt', ':now')
        )->setParameter('now', CarbonImmutable::now());

        if ($isQuery) {
            return $qb->getQuery();
        }

        return $qb->getQuery()->getResult();
    }

}
