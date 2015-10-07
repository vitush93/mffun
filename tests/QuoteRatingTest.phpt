<?php
namespace Test;

use App\Model\Entities\Quote;
use App\Model\Entities\QuoteRating;
use App\Model\Entities\User;
use Kdyby\Doctrine\EntityManager;
use Nette;
use Tester;
use Tester\Assert;

$container = require __DIR__ . '/bootstrap.php';

class QuoteRatingTest extends Tester\TestCase
{
    /** @var Nette\DI\Container */
    private $container;

    /** @var EntityManager */
    private $em;

    /** @var Quote */
    private $unratedQuote;

    /** @var Quote */
    private $positivelyRatedQuote;

    /** @var Quote */
    private $negativelyRatedQuote;

    /** @var Quote */
    private $mixedRatedQuote;

    /** @var User */
    private $mockAuthor;

    /**
     * @param Nette\DI\Container $container
     */
    function __construct(Nette\DI\Container $container)
    {
        $this->container = $container;
        $this->em = $container->getByType(EntityManager::class);
    }

    function setUp()
    {
        $this->mockAuthor = new User();
        $this->em->persist($this->mockAuthor);

        $this->unratedQuote = new Quote("some text", new \DateTime());
        $this->em->persist($this->unratedQuote);

        $this->positivelyRatedQuote = new Quote("another text", new \DateTime());
        $this->em->persist($this->positivelyRatedQuote);
        $this->createRating($this->positivelyRatedQuote);
        $this->createRating($this->positivelyRatedQuote);

        $this->negativelyRatedQuote = new Quote("blah blah", new \DateTime());
        $this->em->persist($this->negativelyRatedQuote);
        $this->createRating($this->negativelyRatedQuote, FALSE);
        $this->createRating($this->negativelyRatedQuote, FALSE);

        $this->mixedRatedQuote = new Quote("xxx", new \DateTime());
        $this->em->persist($this->mixedRatedQuote);
        $this->createRating($this->mixedRatedQuote);
        $this->createRating($this->mixedRatedQuote);
        $this->createRating($this->mixedRatedQuote, FALSE);
        $this->createRating($this->mixedRatedQuote, FALSE);
        $this->createRating($this->mixedRatedQuote, FALSE);
    }

    function createRating(Quote $quote, $positive = TRUE)
    {
        $user = new User();
        $this->em->persist($user);

        $rating = new QuoteRating($this->mockAuthor, $quote);
        $positive ? $rating->setPositive() : $rating->setNegative();
        $rating->setQuote($quote);
        $rating->setUser($user);

        return $rating;
    }

    function testSomething()
    {
        Assert::true(TRUE);
    }
}

$test = new QuoteRatingTest($container);
$test->run();