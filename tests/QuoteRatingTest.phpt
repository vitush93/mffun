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
    }

    function createRating(Quote $quote, $positive = TRUE)
    {
        $user = new User();
        $this->em->persist($user);

        $rating = new QuoteRating($this->mockAuthor, $quote);
        $positive ? $rating->setPositive() : $rating->setNegative();

        return $rating;
    }

    function testUnratedQuoteRating()
    {
        $unratedQuote = new Quote("some text", new \DateTime());
        $this->em->persist($unratedQuote);

        Assert::equal(0, $unratedQuote->getRating());
    }

    function testPositivelyRatedQuoteRating()
    {
        $positivelyRatedQuote = new Quote("another text", new \DateTime());
        $this->createRating($positivelyRatedQuote);
        $this->createRating($positivelyRatedQuote);
        $this->em->persist($positivelyRatedQuote);

        Assert::equal(2, $positivelyRatedQuote->getRating());
    }

    function testNegativelyRatedQuoteRating()
    {
        $negativelyRatedQuote = new Quote("blah blah", new \DateTime());
        $this->createRating($negativelyRatedQuote, FALSE);
        $this->createRating($negativelyRatedQuote, FALSE);
        $this->em->persist($negativelyRatedQuote);

        Assert::equal(-2, $negativelyRatedQuote->getRating());
    }

    function testMixedRatedQuoteRating()
    {
        $mixedRatedQuote = new Quote("xxx", new \DateTime());
        $this->createRating($mixedRatedQuote);
        $this->createRating($mixedRatedQuote);
        $this->createRating($mixedRatedQuote, FALSE);
        $this->createRating($mixedRatedQuote, FALSE);
        $this->createRating($mixedRatedQuote, FALSE);
        $this->em->persist($mixedRatedQuote);

        Assert::equal(-1, $mixedRatedQuote->getRating());
    }
}

$test = new QuoteRatingTest($container);
$test->run();