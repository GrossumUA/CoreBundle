<?php

namespace Grossum\CoreBundle\Command;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

use Sensio\Bundle\GeneratorBundle\Command\Helper\QuestionHelper;

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
        $this->questionHelper = $this->getQuestionHelper();
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('grossum:install')
            ->setDescription('Grossum Installer: load fixtures and create user')
            ->addOption(
                self::LOAD_FIXTURES_OPTION_NAME,
                null,
                InputOption::VALUE_NONE,
                'Load fixtures (should be passed with non-interactive option)'
            )
            ->addOption(
                self::CREATE_USER_OPTION_NAME,
                null,
                InputOption::VALUE_NONE,
                'Create user (should be passed with non-interactive option)'
            )
            ->addOption(
                self::USER_LOGIN_OPTION_NAME,
                null,
                InputOption::VALUE_REQUIRED,
                'When you create user in non-interactive mode, you must to pass user login'
            )
            ->addOption(
                self::USER_PASSWORD_OPTION_NAME,
                null,
                InputOption::VALUE_REQUIRED,
                'When you create user in non-interactive mode, you must to pass user password'
            )
            ->addOption(
                self::USER_EMAIL_OPTION_NAME,
                null,
                InputOption::VALUE_REQUIRED,
                'When you create user in non-interactive mode, you must to pass user email'
            );
    }

    /**
     * @inheritdoc
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $this->questionHelper->writeSection(
            $this->output,
            'Welcome to Grossum Installer!'
        );

        $fixturesLoadQuestion = new ConfirmationQuestion(
            $this->getQuestionMessage('Load fixtures', false, '?'),
            false
        );

        if ($this->questionHelper->ask($input, $output, $fixturesLoadQuestion)) {
            $this->loadFixtures();
            $this->output->writeln('');
        }

        $userCreateQuestion = new ConfirmationQuestion(
            $this->getQuestionMessage('Create user', false, '?'),
            false
        );

        if ($this->questionHelper->ask($input, $output, $userCreateQuestion)) {
            $this->createUser();
        }

        $this->questionHelper->writeSection(
            $this->output,
            'Installation has been finished'
        );
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
     * @param string $userName
     * @param string $userEmail
     * @param string $userPassword
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

        $userNameQuestion = $this->getQuestion(
            'Please enter the user login',
            $userNameOptionValue,
            'validateUserName'
        );

        $userEmailQuestion = $this->getQuestion(
            'Please enter the user email',
            $userEmailOptionValue,
            'validateUserEmail'
        );
        
        $userPasswordQuestion = $this->getQuestion(
            'Please enter the user password',
            $userPasswordOptionValue,
            'validateUserPassword'
        );

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

    /**
     * @param string $questionMessage
     * @param string $default
     * @param string $validatorName
     * @return Question
     */
    private function getQuestion($questionMessage, $default, $validatorName)
    {
        $question = new Question(
            $this->getQuestionMessage($questionMessage, $default),
            $default
        );
        $question->setValidator([$this->getValidator(), $validatorName]);

        return $question;
    }

    /**
     * @param string $question
     * @param null $default
     * @param string $sep
     * @return string
     */
    private function getQuestionMessage($question, $default = null, $sep = ':')
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
        if (null === $this->validators) {
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

    /**
     * @return QuestionHelper
     */
    private function getQuestionHelper()
    {
        $question = $this->getHelperSet()->get('question');
        if (!$question || get_class($question) !== 'Sensio\Bundle\GeneratorBundle\Command\Helper\QuestionHelper') {
            $this->getHelperSet()->set($question = new QuestionHelper());
        }

        return $question;
    }
}
