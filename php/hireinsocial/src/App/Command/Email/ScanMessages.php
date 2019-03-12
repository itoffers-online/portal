<?php

declare(strict_types=1);

/*
 * This file is part of the Hire in Social project.
 *
 * (c) Norbert Orzechowicz <norbert@orzechowicz.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Command\Email;

use function \Safe\sprintf;
use App\Email\Parser;
use Ddeboer\Imap\ConnectionInterface;
use Ddeboer\Imap\Message\AttachmentInterface;
use Ddeboer\Imap\Search\Flag\Unseen;
use HireInSocial\Application\Command\Offer\Apply\Attachment;
use HireInSocial\Application\Command\Offer\ApplyThroughEmail;
use HireInSocial\Application\Query\Offer\ApplicationQuery;
use HireInSocial\Application\Query\Offer\OfferQuery;
use HireInSocial\Application\System;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;

final class ScanMessages extends Command
{
    use LockableTrait;

    public const NAME = 'email:scan';
    protected static $defaultName = self::NAME;

    private $system;
    private $connection;

    private $tmpBasePath;
    /**
     * @var SymfonyStyle
     */
    private $io;
    /**
     * @var Filesystem
     */
    private $fs;

    public function __construct(System $system, ConnectionInterface $connection, Filesystem $filesystem)
    {
        parent::__construct();

        $this->system = $system;
        $this->connection = $connection;
        $this->fs = $filesystem;
    }

    protected function configure() : void
    {
        $this
            ->setDescription('<info>[Email]</info> Scan mailbox.')
            ->addOption('mailbox', 'm', InputOption::VALUE_OPTIONAL, 'Mailbox name where offers are received.', 'INBOX')
        ;
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);
        $this->tmpBasePath = sys_get_temp_dir() . '/' . uniqid('his_email_' . $input->getOption('mailbox') . '_');
        $this->fs->mkdir($this->tmpBasePath);

        $this->io->title('Mailbox Scan');
        $this->io->note('mailbox: ' . $input->getOption('mailbox'));
        $this->io->note('tmp dir: ' . $this->tmpBasePath);
    }

    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        if (!$this->lock()) {
            $output->writeln('The command is already running in another process.');

            return 0;
        }

        $mailbox = $this->connection->getMailbox($input->getOption('mailbox'));
        $messages = $mailbox->getMessages(new Unseen(), \SORTDATE, false);

        if (!\count($messages)) {
            $this->io->comment('No new messages found, scan ended.');

            $this->finishCommand();

            return 0;
        }

        $this->io->section('Start scan');
        $this->io->text(sprintf('New messages: <info>%d</info>', \count($messages)));

        foreach ($messages as $message) {
            $email = (new Parser($message->getTo()[0]->getAddress()))->parse();
            $sender = $message->getSender()[0]->getAddress();

            if ($offer = $this->system->query(OfferQuery::class)->findByEmailHash($email->tag())) {
                if ($this->system->query(ApplicationQuery::class)->alreadyApplied($offer->id()->toString(), $sender)) {
                    $this->io->note(sprintf('Marking email as seen since sender already applied for <info>%s</info> job offer', $offer->slug()));
                    $message->markAsSeen();

                    continue;
                }

                $attachments = \array_map([$this, 'saveInTmp'], $message->getAttachments());

                $this->io->text(sprintf('New email for: <info>%s</info>', $offer->slug()));

                $this->system->handle(new ApplyThroughEmail(
                    (string) $offer->id(),
                    $sender,
                    $message->getSubject(),
                    $message->getBodyHtml(),
                    ...\array_map(
                        function (AttachmentTmpPath $attachmentTmpPath) {
                            return new Attachment(
                                $attachmentTmpPath->toString()
                            );
                        },
                        $attachments
                    )
                ));

                $message->markAsSeen();

                $this->io->text('Message forwarded to offer contact email and marked as seen.');

                $this->io->newLine(1);
            } else {
                $this->io->note(sprintf('Marking email to offer "%s" as seen. No active related job offer was found.', $email->toString()));
                $message->markAsSeen();
            }
        }

        $this->finishCommand();

        return 0;
    }

    protected function finishCommand(): void
    {
        $this->release();
        $this->fs->remove($this->tmpBasePath);

        $this->io->note(sprintf('Tmp dir for attachments removed: %s', $this->tmpBasePath));
    }

    protected function saveInTmp(AttachmentInterface $attachment): AttachmentTmpPath
    {
        $this->io->comment(sprintf('Saving attachment "%s" to tmp storage...', $attachment->getFilename()));

        $attachmentTmp = new AttachmentTmpPath($this->tmpBasePath, $attachment->getFilename());

        $this->fs->dumpFile($attachmentTmp->toString(), $attachment->getDecodedContent());

        $this->io->comment(sprintf('Attachment saved to tmp storage: <info>%s</info>', $attachmentTmp->toString()));

        return $attachmentTmp;
    }
}
