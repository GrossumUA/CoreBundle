<?php

namespace Grossum\CoreBundle\Command;

use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class InstallCommand extends ContainerAwareCommand
{
    const USER_LOGIN_OPTION_NAME = "login";
    const USER_PASSWORD_OPTION_NAME = "password";
    const USER_EMAIL_OPTION_NAME = "email";

    /** @var InputInterface */
    private $input;

    /** @var OutputInterface */
    private $output;

    /** @var QuestionHelper $helper */
    private $questionHelper;

    /**
     * @inheritdoc
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
        $this->questionHelper = $this->getHelper('question');
    }

    protected function configure()
    {
        $this
            ->setName('grossum:install')
            ->setDescription('Create user and load fixtures')
            ->addOption(
                self::USER_LOGIN_OPTION_NAME,
                null,
                InputOption::VALUE_REQUIRED,
                'User login'
            )
            ->addOption(
                self::USER_PASSWORD_OPTION_NAME,
                null,
                InputOption::VALUE_REQUIRED,
                'User password'
            )
            ->addOption(
                self::USER_EMAIL_OPTION_NAME,
                null,
                InputOption::VALUE_REQUIRED,
                'User email'
            )
        ;
    }

    /**
     * @param $userName
     * @param $userEmail
     * @param $userPassword
     */
    private function saveUserToDatabase($userName, $userEmail, $userPassword)
    {
        $userManager = $this->getContainer()->get('fos_user.user_manager');

        $user = $userManager->createUser();
        $user->setUsername($userName);
        $user->setEmail($userEmail);
        $user->setPlainPassword($userPassword);
        $user->setEnabled(true);

        $userManager->updateUser($user);
    }

    private function createUser()
    {
        $commandValidators = $this->getContainer()->get('grossum_core.command.validators');

        $userNameOptionValue = $this->input->getOption(self::USER_LOGIN_OPTION_NAME);
        $userEmailOptionValue = $this->input->getOption(self::USER_EMAIL_OPTION_NAME);
        $userPasswordOptionValue = $this->input->getOption(self::USER_PASSWORD_OPTION_NAME);

        $userNameQuestion = new Question(
            'Please enter the user name [<info>'.$userNameOptionValue.'</info>]: ',
            $userNameOptionValue
        );
        $userNameQuestion->setValidator([$commandValidators, 'validateUserName']);

        $userEmailQuestion = new Question(
            'Please enter the user email [<info>'.$userEmailOptionValue.'</info>]: ',
            $userEmailOptionValue
        );
        $userEmailQuestion->setValidator([$commandValidators, 'validateUserEmail']);

        $userPasswordQuestion = new Question(
            'Please enter the user password [<info>'.$userPasswordOptionValue.'</info>]: ',
            $userPasswordOptionValue
        );
        $userPasswordQuestion->setValidator([$commandValidators, 'validateUserPassword']);

        $userName = $this->questionHelper->ask($this->input, $this->output, $userNameQuestion);
        $userEmail = $this->questionHelper->ask($this->input, $this->output, $userEmailQuestion);
        $userPassword = $this->questionHelper->ask($this->input, $this->output, $userPasswordQuestion);

        $this->saveUserToDatabase($userName, $userEmail, $userPassword);
    }

    private function loadFixtures()
    {
        $command = $this->getApplication()->find('doctrine:fixtures:load');
        $input = new ArrayInput([]);

        $command->run($input, $this->output);
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output->writeln([
            '',
            '<bg=blue;fg=white>Welcome to Grossum Installation!</>',
            ''
        ]);

        $userCreateQuestion = new ConfirmationQuestion('<info>Create user?</info> ', false);

        if ($this->questionHelper->ask($input, $output, $userCreateQuestion)) {
            $this->createUser();
        }

        $fixturesLoadQuestion = new ConfirmationQuestion('<info>Load fixtures?</info> ', false);

        if ($this->questionHelper->ask($input, $output, $fixturesLoadQuestion)) {
            $this->loadFixtures();
        }
    }
}
