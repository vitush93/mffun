<?php

namespace App\Model\Services;

use App\Model\Entities\Quote;
use App\Model\Entities\Tag;
use App\Model\Repositories\QuoteRepository;
use App\Model\Repositories\TagRepository;
use Nette\Object;

class QuoteManagementService extends Object
{
    private $quoteRepository;
    private $tagRepository;

    public function __construct(QuoteRepository $quoteRepository, TagRepository $tagRepository)
    {
        $this->quoteRepository = $quoteRepository;
        $this->tagRepository = $tagRepository;
    }

    public function addQuote(Quote $quote, array $tags)
    {
        $this->quoteRepository->save($quote);

        foreach ($tags as $t) {
            $tag = $this->tagRepository->findOneByTag($t);
            if ($tag == NULL) {
                $tag = new Tag();
                $tag->setTag($t);
            }

            $tag->assignToQuote($quote);
            $this->tagRepository->save($tag);
        }
    }
} 