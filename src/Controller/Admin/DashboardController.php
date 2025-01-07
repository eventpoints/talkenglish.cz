<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\AnswerOption;
use App\Entity\Lesson;
use App\Entity\Question;
use App\Entity\QuestionExtra;
use App\Entity\Quiz;
use App\Entity\QuizParticipation;
use App\Entity\User;
use App\Entity\WeeklyQuiz;
use App\Enum\RoleEnum;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends AbstractDashboardController
{

    public function __construct(
        private readonly Security $security
    )
    {
    }

    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $currentUser = $this->security->getUser();

        if(!$currentUser instanceof User){
            return $this->redirect($this->generateUrl('app_login'));
        }

        $this->denyAccessUnlessGranted(attribute: RoleEnum::ADMIN->value, subject: $currentUser);
         $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
         return $this->redirect($adminUrlGenerator->setController(UserCrudController::class)->generateUrl());
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('talkenglish.cz');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
         yield MenuItem::linkToCrud('Users', 'fas fa-list', User::class);
         yield MenuItem::linkToCrud('Quizzes', 'fas fa-list', Quiz::class);
         yield MenuItem::linkToCrud('Weekly Quiz', 'fas fa-list', WeeklyQuiz::class);
         yield MenuItem::linkToCrud('Questions', 'fas fa-list', Question::class);
         yield MenuItem::linkToCrud('QuestionExtra', 'fas fa-list', QuestionExtra::class);
         yield MenuItem::linkToCrud('AnswerOptions', 'fas fa-list', AnswerOption::class);
         yield MenuItem::linkToCrud('Quiz Participations', 'fas fa-list', QuizParticipation::class);
    }
}
