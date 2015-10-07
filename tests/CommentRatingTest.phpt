<?php
namespace Test;

use App\Model\Entities\Comment;
use App\Model\Entities\CommentRating;
use App\Model\Entities\User;
use Kdyby\Doctrine\EntityManager;
use Nette;
use Tester;
use Tester\Assert;

$container = require __DIR__ . '/bootstrap.php';

class CommentRatingTest extends Tester\TestCase
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

    function createRating(Comment $comment, $positive = TRUE)
    {
        $user = new User();
        $this->em->persist($user);

        $rating = new CommentRating($this->mockAuthor, $comment);
        $positive ? $rating->setPositive() : $rating->setNegative();
        $this->em->persist($rating);

        return $rating;
    }

    function testUnratedComment()
    {
        $unratedComment = new Comment();
        $this->em->persist($unratedComment);

        Assert::equal(0, $unratedComment->getRatingUp());
        Assert::equal(0, $unratedComment->getRatingDown());
    }

    function testPositivelyRatedQuoteRating()
    {
        $positiveComment = new Comment();
        $this->createRating($positiveComment);
        $this->createRating($positiveComment);
        $this->em->persist($positiveComment);

        Assert::equal(2, $positiveComment->getRatingUp());
    }

    function testNegativelyRatedQuoteRating()
    {
        $negativeComment = new Comment();
        $this->createRating($negativeComment, FALSE);
        $this->createRating($negativeComment, FALSE);
        $this->em->persist($negativeComment);

        Assert::equal(2, $negativeComment->getRatingDown());
    }

    function testMixedRatedQuoteRating()
    {
        $mixedComment = new Comment();
        $this->createRating($mixedComment);
        $this->createRating($mixedComment);
        $this->createRating($mixedComment, FALSE);
        $this->createRating($mixedComment, FALSE);
        $this->createRating($mixedComment, FALSE);
        $this->em->persist($mixedComment);

        Assert::equal(2, $mixedComment->getRatingUp());
        Assert::equal(3, $mixedComment->getRatingDown());
    }
}

$test = new CommentRatingTest($container);
$test->run();