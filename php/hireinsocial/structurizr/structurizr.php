<?php

declare (strict_types=1);

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use StructurizrPHP\Client\Client;
use StructurizrPHP\Client\Credentials;
use StructurizrPHP\Client\Infrastructure\Http\SymfonyRequestFactory;
use StructurizrPHP\Client\UrlMap;
use StructurizrPHP\Core\Model\Enterprise;
use StructurizrPHP\Core\Model\Location;
use StructurizrPHP\Core\Model\Tags;
use StructurizrPHP\Core\View\Configuration\Shape;
use StructurizrPHP\Core\View\PaperSize;
use StructurizrPHP\Core\Workspace;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpClient\Psr18Client;

require __DIR__ . '/../vendor/autoload.php';

if (class_exists(Dotenv::class) && file_exists(__DIR__.'/../.env')) {
    (new Dotenv())->load(__DIR__ . '/../.env');
}
const TAG_PERSON_CANDIDATE = 'PERSON_CANDIDATE';
const TAG_PERSON_RECRUITER = 'PERSON_RECRUITER';
const TAG_HIS = 'TAG_HIS';
const TAG_DB = 'TAG_DB';

$workspace = new Workspace(
    $id = (string) \getenv('STRUCTURIZR_WORKSPACE_ID'),
    $name = 'Hire in Social',
    $description = 'Hire in Social - platform where people can post job offers that are promoted on social media.'
);
$workspace->getModel()->setEnterprise(new Enterprise('Hire in Social'));

// People
$candidate = $workspace->getModel()->addPerson('Candidate', 'Candidate looking for a job', Location::external());
$candidate->addTags(TAG_PERSON_CANDIDATE);
$recruiter = $workspace->getModel()->addPerson('Recruiter', 'Recruiter looking for candidates', Location::external());
$recruiter->addTags(TAG_PERSON_RECRUITER);

// Software Systems
$hireInSocial = $workspace->getModel()->addSoftwareSystem('Hire in Social', 'Social media based hiring platform', Location::internal());
$hireInSocial->addTags(TAG_HIS);

$facebook = $workspace->getModel()->addSoftwareSystem('Facebook', 'Social media platform', Location::external());

$mailServer = $workspace->getModel()->addSoftwareSystem('Mail Server', 'Hire in Social mail server', Location::internal());
$mailServer->addTags(TAG_HIS);

// Containers
$webApp = $hireInSocial->addContainer('Web Application', 'List job offers posted by recruiters so candidates can browse them', 'php');
$cliApp = $hireInSocial->addContainer('CLI Application', 'Read emails from candidates and forward them to recruiters', 'php');

$db = $hireInSocial->addContainer('Database', 'Stores job offers and applications from candidates', 'PostgreSQL');
$db->addTags(TAG_HIS, TAG_DB);

// People Relationships
$candidate->usesSoftwareSystem($hireInSocial, 'Browse job offers', 'Web Browser');
$candidate->usesSoftwareSystem($facebook, 'Browse groups with job offers');
$candidate->usesSoftwareSystem($mailServer, 'Apply to position', 'Email');

$recruiter->usesSoftwareSystem($hireInSocial, 'Post job offers', 'Web Browser');

$candidate->usesContainer($webApp,  'Browse job offers', 'Web Browser');
$recruiter->usesContainer($webApp,  'ost job offers', 'Web Browser');

// Software System Relationships
$hireInSocial->usesSoftwareSystem($facebook, 'Post job offers at groups');
$hireInSocial->usesSoftwareSystem($mailServer, 'Read emails from candidates, forward them to recruiters');

$facebook->delivers($candidate, 'Notify about new posts');

$mailServer->delivers($recruiter, 'Notify about new applications', 'Email');

// Containers Relationships

$webApp->usesContainer($db, 'reads and writes');
$webApp->addTags(TAG_HIS);
$cliApp->usesContainer($db, 'reads and writes');
$cliApp->addTags(TAG_HIS);
$cliApp->usesSoftwareSystem($mailServer, 'Read Emails', 'IMAP');
$cliApp->usesSoftwareSystem($mailServer, 'Forward Emails', 'SMTP');

// System Landscape View
$systemContextView = $workspace->getViews()->createSystemLandscapeView('system-landscape', 'Hire in Social - Overview');
$systemContextView->setPaperSize(PaperSize::A4_Landscape());
$systemContextView->addAllElements();

// Container View
$systemContainerView = $workspace->getViews()->createContainerView($hireInSocial, 'system-container', 'Hire in Social - detailed view');
$systemContainerView->addAllPeople(true);
$systemContainerView->addSoftwareSystem($mailServer);
$systemContainerView->addSoftwareSystem($facebook);
$systemContainerView->addContainer($db);
$systemContainerView->addContainer($webApp);
$systemContainerView->addContainer($cliApp);

// Styles
$styles = $workspace->getViews()->getConfiguration()->getStyles();
$styles->addElementStyle(Tags::PERSON)->shape(Shape::person());
$styles->addElementStyle(TAG_PERSON_CANDIDATE)->color('#ffffff')->background('#7dd2fa');
$styles->addElementStyle(TAG_HIS)->color('#ffffff')->background('#58b543');
$styles->addElementStyle(TAG_DB)->shape(Shape::cylinder());

$client = new Client(
    new Credentials((string) \getenv('STRUCTURIZR_API_KEY'), (string) \getenv('STRUCTURIZR_API_SECRET')),
    new UrlMap('https://api.structurizr.com'),
    new Psr18Client(),
    new SymfonyRequestFactory(),
    (new Logger('structurizr'))->pushHandler(new StreamHandler(__DIR__ . '/../var/structurizr/' . basename(__FILE__) . '.log', Logger::DEBUG))
);

$client->put($workspace);