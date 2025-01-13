<?php

declare(strict_types=1);

namespace App\Controller\Controller;

use App\DataTransferObject\JobFilterDto;
use App\Entity\JobAdvertisement;
use App\Entity\User;
use App\Enum\FlashEnum;
use App\Form\Filter\JobAdvertisementFilter;
use App\Form\Form\Job\JobAdvertisementFormType;
use App\Repository\JobAdvertisementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class JobAdvertisementController extends AbstractController
{

    public function __construct(
        private readonly JobAdvertisementRepository $jobAdvertisementRepository
    )
    {
    }

    #[Route(path: '/jobs', name: 'jobs')]
    public function index(Request $request): Response
    {
        $jobsFilterDto = new JobFilterDto();
        $jobs = $this->jobAdvertisementRepository->findByFilter(jobsFilterDto: $jobsFilterDto);
        $jobsFilter = $this->createForm(JobAdvertisementFilter::class, $jobsFilterDto);

        $jobsFilter->handleRequest(request: $request);
        if ($jobsFilter->isSubmitted() && $jobsFilter->isValid()) {
            $jobs = $this->jobAdvertisementRepository->findByFilter(jobsFilterDto: $jobsFilterDto);
            return $this->render('jobs/index.html.twig', [
                'jobsFilter' => $jobsFilter,
                'jobs' => $jobs
            ]);
        }

        return $this->render('jobs/index.html.twig', [
            'jobsFilter' => $jobsFilter,
            'jobs' => $jobs
        ]);
    }

    #[Route(path: '/jobs/create', name: 'create_job')]
    public function create(Request $request, #[CurrentUser] User $currentUser): Response
    {
        $job = new JobAdvertisement(owner: $currentUser);
        $jobForm = $this->createForm(JobAdvertisementFormType::class, $job);
        $jobForm->handleRequest($request);
        if ($jobForm->isSubmitted() && $jobForm->isValid()) {
            $this->jobAdvertisementRepository->save(entity: $job, flush: true);
            $this->addFlash(FlashEnum::SUCCESS->value, 'Posting Successful, advertisement under review');
            return $this->redirectToRoute('job', ['id' => $job->getId()]);
        }

        return $this->render('jobs/create.html.twig', [
            'jobForm' => $jobForm
        ]);
    }

    #[Route(path: '/jobs/{id}', name: 'job')]
    public function show(JobAdvertisement $job): Response
    {
        return $this->render('jobs/show.html.twig', [
            'job' => $job
        ]);
    }

}
