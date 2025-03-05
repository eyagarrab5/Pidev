<?php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;

class TestMailerCommand extends Command
{
    protected static $defaultName = 'app:test-mailer';
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        parent::__construct();
        $this->mailer = $mailer;
    }

    protected function configure()
    {
        $this->setDescription('Test the mailer service.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = (new Email())
            ->from('covoimob25@gmail.com')
            ->to('benslimenmalek26@gmail.com')
            ->subject('Test d\'envoi d\'e-mail')
            ->text('Ceci est un test d\'envoi d\'e-mail depuis Symfony.');

        $this->mailer->send($email);

        $output->writeln('E-mail envoyé avec succès !');
        return Command::SUCCESS;
    }
}