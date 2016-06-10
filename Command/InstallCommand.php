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
    const LOAD_FIXTURES_OPTION_NAME = "load-fixtures";
    const CREATE_USER_OPTION_NAME = "create-user";
    const USER_LOGIN_OPTION_NAME = "login";
    const USER_EMAIL_OPTION_NAME = "email";
    const USER_PASSWORD_OPTION_NAME = "password";

    /** @var InputInterface */
    private $input;

    /** @var OutputInterface */
    private $output;

    /** @var QuestionHelper */
    private $questionHelper;

    /** @var Validators */
    private $validators;

    /**
     * @inheritdoc
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
        $this->questionHelper = $this->getHelper('question');
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('grossum:install')
            ->setDescription('Load fixtures and create user')
            ->addOption(
                self::LOAD_FIXTURES_OPTION_NAME,
                null,
                InputOption::VALUE_NONE,
                'For non-interactive mode: load fixtures'
            )
            ->addOption(
                self::CREATE_USER_OPTION_NAME,
                null,
                InputOption::VALUE_NONE,
                'For non-interactive mode: create user'
            )
            ->addOption(
                self::USER_LOGIN_OPTION_NAME,
                null,
                InputOption::VALUE_REQUIRED,
                'User login'
            )
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
     * @inheritdoc
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $this->printIntro();

        $fixturesLoadQuestion = new ConfirmationQuestion(
            $this->getQuestion('Load fixtures', false, '?'),
            false
        );

        if ($this->questionHelper->ask($input, $output, $fixturesLoadQuestion)) {
            $this->loadFixtures();
            $this->output->writeln('');
        }

        $userCreateQuestion = new ConfirmationQuestion(
            $this->getQuestion('Create user', false, '?'),
            false
        );

        if ($this->questionHelper->ask($input, $output, $userCreateQuestion)) {
            $this->createUser();
        }

        $this->printOutro();
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($this->input->getOption(self::LOAD_FIXTURES_OPTION_NAME)) {
            $this->loadFixtures();
        }

        if ($this->input->getOption(self::CREATE_USER_OPTION_NAME)) {
            $this->saveUserToDatabase(
                $this->getValidator()->validateUserName($this->input->getOption(self::USER_LOGIN_OPTION_NAME)),
                $this->getValidator()->validateUserEmail($this->input->getOption(self::USER_EMAIL_OPTION_NAME)),
                $this->getValidator()->validateUserPassword($this->input->getOption(self::USER_PASSWORD_OPTION_NAME))
            );
        }
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
        list($userNameOptionValue, $userEmailOptionValue, $userPasswordOptionValue) = $this->getUserOptions();

        $userNameQuestion = new Question(
            $this->getQuestion('Please enter the user name', $userNameOptionValue),
            $userNameOptionValue
        );
        $userNameQuestion->setValidator([$this->getValidator(), 'validateUserName']);

        $userEmailQuestion = new Question(
            $this->getQuestion('Please enter the user email', $userEmailOptionValue),
            $userEmailOptionValue
        );
        $userEmailQuestion->setValidator([$this->getValidator(), 'validateUserEmail']);

        $userPasswordQuestion = new Question(
            $this->getQuestion('Please enter the user password', $userPasswordOptionValue),
            $userPasswordOptionValue
        );
        $userPasswordQuestion->setValidator([$this->getValidator(), 'validateUserPassword']);

        $userName = $this->questionHelper->ask($this->input, $this->output, $userNameQuestion);
        $userEmail = $this->questionHelper->ask($this->input, $this->output, $userEmailQuestion);
        $userPassword = $this->questionHelper->ask($this->input, $this->output, $userPasswordQuestion);

        $this->saveUserToDatabase($userName, $userEmail, $userPassword);
    }

    private function loadFixtures()
    {
        $command = $this->getApplication()->find('doctrine:fixtures:load');
        $input = new ArrayInput([]);
        $input->setInteractive($this->input->isInteractive());

        $command->run($input, $this->output);
    }

    private function printIntro()
    {
        $this->output->writeln([
            '',
            '<bg=blue;fg=white>Welcome to Grossum Installation!</>',
            ''
        ]);
    }

    private function printOutro()
    {
        $this->output->writeln([
            '',
            '<bg=blue;fg=white>Grossum Installation has been finished</>',
            ''
        ]);
    }

    private function getQuestion($question, $default, $sep = ':')
    {
        if (is_bool($default)) {
            $default = $default ? 'yes' : 'no';
        }

        return $default ?
            sprintf('<info>%s</info> [<comment>%s</comment>]%s ', $question, $default, $sep) :
            sprintf('<info>%s</info>%s ', $question, $sep);
    }

    /**
     * @return Validators
     */
    private function getValidator()
    {
        if (is_null($this->validators)) {
            $this->validators = $this->getContainer()->get('grossum_core.command.validators');
        }

        return $this->validators;
    }

    /**
     * @return array
     */
    private function getUserOptions()
    {
        $userNameOptionValue     = $this->input->getOption(self::USER_LOGIN_OPTION_NAME);
        $userEmailOptionValue    = $this->input->getOption(self::USER_EMAIL_OPTION_NAME);
        $userPasswordOptionValue = $this->input->getOption(self::USER_PASSWORD_OPTION_NAME);

        return [$userNameOptionValue, $userEmailOptionValue, $userPasswordOptionValue];
    }
}
