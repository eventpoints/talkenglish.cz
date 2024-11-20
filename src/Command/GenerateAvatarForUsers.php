<?php

declare(strict_types=1);

namespace App\Command;

use App\Repository\UserRepository;
use App\Service\AvatarService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:generate-avatars',
    description: 'Generates avatars for users.'
)]
class GenerateAvatarForUsers extends Command
{

    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly AvatarService $avatarService
    )
    {
        parent::__construct();
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle(input: $input, output: $output);

        $users = $this->userRepository->findAll();

        foreach ($users as $user){
            $avatar = $this->avatarService->createAvatar(hashString:  $user->getEmail());
            $user->setAvatar($avatar);
            $this->userRepository->save(entity: $user,flush: true);
        }

        $io->success('complete!');
        return Command::SUCCESS;
    }
}
